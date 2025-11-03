const API_BASE = window.APP_CONFIG.apiBase;
const roomId = window.APP_CONFIG.roomId;
const currentUser = window.APP_CONFIG.user;
const history = window.APP_CONFIG.history || [];
const roomType = window.APP_CONFIG.roomType || 'direct';
const messageEndpoint = window.APP_CONFIG.messageEndpoint || 'room_message.php';
let currentTransferMode = window.APP_CONFIG.transferMode || (roomType === 'team' ? 'store' : 'webrtc');

const socket = io(API_BASE, { transports: ['websocket'] });
const configuration = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };

const allowWebRTC = roomType === 'direct';
const peers = new Map();
const incomingFiles = new Map();
let lastRoster = [];

const chatFeed = document.getElementById('chatFeed');
const userList = document.getElementById('userList');
const userCount = document.getElementById('userCount');
const dropZone = document.getElementById('dropZone');
const dropZoneText = dropZone ? dropZone.querySelector('p') : null;
const fileInput = document.getElementById('fileInput');
const messageInput = document.getElementById('messageInput');
const sendMessageBtn = document.getElementById('sendMessageBtn');
const successToast = document.getElementById('successToast');
const modeToggle = document.getElementById('modeToggle');
const modeStatus = document.getElementById('modeStatus');
const toastInstance = successToast ? new bootstrap.Toast(successToast, { delay: 2500 }) : null;

const CHUNK_SIZE = 16000;
const DATA_CHANNEL_MAX_BUFFER = 4 * 1024 * 1024; // 4MB high-water mark
const DATA_CHANNEL_LOW_WATERMARK = 512 * 1024; // resume when queue drains below 512KB
const BUFFER_CHECK_INTERVAL = 200;
const MAX_RECONNECT_ATTEMPTS = 5;
const RECONNECT_BASE_DELAY = 750;

const reconnectAttempts = new Map();
const reconnectTimers = new Map();

async function waitForChannelDrain(channel) {
    return new Promise((resolve, reject) => {
        if (!channel || channel.readyState !== 'open') {
            reject(new Error('Data channel closed'));
            return;
        }

        if (channel.bufferedAmount <= DATA_CHANNEL_LOW_WATERMARK) {
            resolve();
            return;
        }

        let settled = false;
        let poller = null;

        const finish = (success, error) => {
            if (settled) return;
            settled = true;
            if (poller) {
                clearInterval(poller);
            }
            if (typeof channel.removeEventListener === 'function') {
                channel.removeEventListener('bufferedamountlow', onLow);
                channel.removeEventListener('close', onClose);
                channel.removeEventListener('error', onError);
            }
            if (success) {
                resolve();
            } else {
                reject(error);
            }
        };

        const onLow = () => finish(true);
        const onClose = () => finish(false, new Error('Channel closed while draining'));
        const onError = () => finish(false, new Error('Channel error while draining'));

        if (typeof channel.addEventListener === 'function') {
            channel.addEventListener('bufferedamountlow', onLow);
            channel.addEventListener('close', onClose);
            channel.addEventListener('error', onError);
        }

        poller = setInterval(() => {
            if (!channel || channel.readyState !== 'open') {
                finish(false, new Error('Channel closed while draining'));
            } else if (channel.bufferedAmount <= DATA_CHANNEL_LOW_WATERMARK) {
                finish(true);
            }
        }, BUFFER_CHECK_INTERVAL);
    });
}

async function sendChannelMessage(channel, payload) {
    if (!channel || channel.readyState !== 'open') {
        throw new Error('Data channel not open');
    }

    const message = typeof payload === 'string' ? payload : JSON.stringify(payload);

    if (channel.bufferedAmount > DATA_CHANNEL_MAX_BUFFER) {
        await waitForChannelDrain(channel);
    }

    channel.send(message);

    if (channel.bufferedAmount > DATA_CHANNEL_MAX_BUFFER) {
        await waitForChannelDrain(channel);
    }
}

function encodeChunkToBase64(chunk) {
    let binary = '';
    const length = chunk.length;
    for (let i = 0; i < length; i += 1) {
        binary += String.fromCharCode(chunk[i]);
    }
    return btoa(binary);
}

async function readFileChunk(file, start, end) {
    const slice = file.slice(start, end);
    if (!slice || slice.size === 0) {
        return new Uint8Array();
    }
    const buffer = await slice.arrayBuffer();
    return new Uint8Array(buffer);
}

function clearPeerReconnect(peerId) {
    const timer = reconnectTimers.get(peerId);
    if (timer) {
        clearTimeout(timer);
        reconnectTimers.delete(peerId);
    }
    reconnectAttempts.delete(peerId);
}

function schedulePeerReconnect(peerId) {
    if (!shouldUseWebRTC()) return;
    if (reconnectTimers.has(peerId)) return;
    const rosterEntry = Array.isArray(lastRoster)
        ? lastRoster.find((user) => user.userId === peerId && user.status === 'online')
        : null;
    if (!rosterEntry) return;

    const attempt = reconnectAttempts.get(peerId) || 0;
    if (attempt >= MAX_RECONNECT_ATTEMPTS) {
        console.warn('Maximum reconnect attempts reached for peer', peerId);
        return;
    }

    const delay = Math.min(RECONNECT_BASE_DELAY * Math.pow(2, attempt), 5000);
    reconnectAttempts.set(peerId, attempt + 1);

    const timer = setTimeout(() => {
        reconnectTimers.delete(peerId);
        closePeer(peerId);
        ensurePeerConnections(lastRoster);
    }, delay);
    reconnectTimers.set(peerId, timer);
}

function shouldUseWebRTC() {
    return allowWebRTC && currentTransferMode === 'webrtc';
}

function scrollToBottom() {
    if (!chatFeed) return;
    chatFeed.scrollTop = chatFeed.scrollHeight;
}

function formatTimestamp(value) {
    if (!value) return '';
    const date = new Date(value);
    return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
}

function appendSystemMessage(text) {
    if (!chatFeed) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'text-center text-secondary small my-2';
    wrapper.textContent = text;
    chatFeed.appendChild(wrapper);
    scrollToBottom();
}

function fileTypeIcon(mime) {
    if (!mime) return 'fa-file';
    if (mime.startsWith('image/')) return 'fa-file-image';
    if (mime.startsWith('video/')) return 'fa-file-video';
    if (mime === 'application/pdf') return 'fa-file-pdf';
    if (mime.startsWith('audio/')) return 'fa-file-audio';
    if (mime.includes('zip') || mime.includes('compressed')) return 'fa-file-zipper';
    return 'fa-file-lines';
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return value
        .toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function createTextBubble({ direction, sender, message, timestamp }) {
    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${direction === 'out' ? 'outgoing' : 'incoming'}`;
    const meta = document.createElement('div');
    meta.className = 'message-meta';

    const senderSpan = document.createElement('span');
    senderSpan.className = 'message-sender';
    senderSpan.textContent = sender;

    const timeSpan = document.createElement('span');
    timeSpan.className = 'message-time';
    timeSpan.textContent = formatTimestamp(timestamp);

    const body = document.createElement('div');
    body.className = 'message-body';
    body.textContent = message;

    meta.appendChild(senderSpan);
    meta.appendChild(timeSpan);
    bubble.appendChild(meta);
    bubble.appendChild(body);
    return bubble;
}

function createStoredFileCard({ messageId, direction, sender, timestamp, fileName, fileType, fileUrl }) {
    const card = document.createElement('div');
    card.className = `card file-card shadow-sm mb-3 ${direction === 'out' ? 'outgoing' : 'incoming'}`;
    if (messageId) {
        card.dataset.messageId = messageId;
    }
    const safeName = escapeHtml(fileName);
    const safeSender = escapeHtml(sender);
    const safeTimestamp = escapeHtml(formatTimestamp(timestamp));
    const safeUrl = fileUrl ? escapeHtml(fileUrl) : '';
    const downloadButton = fileUrl
        ? `<a href="${safeUrl}" class="btn btn-sm btn-outline-primary" download title="Download">
                <i class=\"fa-solid fa-download me-1\"></i>Download
           </a>`
        : '<span class="badge bg-secondary-subtle text-secondary">Unavailable</span>';
    card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid ${fileTypeIcon(fileType)} fa-2x text-primary"></i>
                    <div>
                        <h5 class="card-title mb-1">${safeName}</h5>
                        <div class="text-secondary small">${safeSender} • ${safeTimestamp}</div>
                    </div>
                </div>
                ${downloadButton}
            </div>
            ${fileUrl ? `<div class="file-preview mt-3" data-preview="${safeUrl}"></div>` : ''}
        </div>
    `;
    return card;
}

function createWebRTCLog({ messageId, direction, sender, timestamp, fileName }) {
    const wrapper = document.createElement('div');
    wrapper.className = `webrtc-log ${direction === 'out' ? 'outgoing' : 'incoming'}`;
    if (messageId) {
        wrapper.dataset.messageId = messageId;
    }
    const safeName = escapeHtml(fileName);
    const safeSender = escapeHtml(sender);
    const safeTime = escapeHtml(formatTimestamp(timestamp));
    wrapper.innerHTML = `
        <i class="fa-solid fa-paperclip me-2"></i>
        <div>
            <div class="fw-semibold">${safeName}</div>
            <div class="small text-secondary">${safeSender} • ${safeTime} • Realtime transfer</div>
        </div>
    `;
    return wrapper;
}
function createTransferCard(meta, direction) {
    if (!chatFeed) return null;
    const card = document.createElement('div');
    card.className = `card file-card shadow-sm mb-3 ${direction === 'out' ? 'outgoing' : 'incoming'}`;
    card.dataset.fileId = meta.fileId;
    const safeName = escapeHtml(meta.name);
    const safeSender = escapeHtml(meta.sender);
    const safeTime = escapeHtml(new Date(meta.timestamp).toLocaleTimeString());
    card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid ${fileTypeIcon(meta.mime)} fa-2x text-primary"></i>
                    <div>
                        <h5 class="card-title mb-1">${safeName}</h5>
                        <div class="text-secondary small">${safeSender} • ${safeTime}</div>
                    </div>
                </div>
                <span class="badge ${direction === 'out' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success'}">
                    ${direction === 'out' ? 'Sending' : 'Receiving'}
                </span>
            </div>
            <div class="progress mt-3" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 0%;">0%</div>
            </div>
            <div class="file-preview mt-3 d-none"></div>
        </div>
    `;
    chatFeed.appendChild(card);
    scrollToBottom();
    return card;
}

function updateProgress(card, value) {
    if (!card) return;
    const progressBar = card.querySelector('.progress-bar');
    if (!progressBar) return;
    progressBar.style.width = `${value}%`;
    progressBar.textContent = `${value}%`;
    if (value >= 100) {
        progressBar.classList.add('bg-success');
    }
}

function renderPreview(card, file, mimeType) {
    if (!card) return;
    const preview = card.querySelector('.file-preview');
    if (!preview) return;

    const type = mimeType || (file && file.type) || 'application/octet-stream';
    let element = null;

    if (file instanceof Blob) {
        const url = URL.createObjectURL(file);
        if (type.startsWith('image/')) {
            element = document.createElement('img');
            element.src = url;
            element.alt = file.name || 'preview';
        } else if (type.startsWith('video/')) {
            element = document.createElement('video');
            element.src = url;
            element.controls = true;
        } else if (type === 'application/pdf') {
            element = document.createElement('iframe');
            element.src = url;
        }
    } else if (typeof file === 'string') {
        if (type.startsWith('image/')) {
            element = document.createElement('img');
            element.src = file;
            element.alt = 'preview';
        } else if (type === 'application/pdf') {
            element = document.createElement('iframe');
            element.src = file;
        }
    }

    if (element) {
        preview.innerHTML = '';
        preview.appendChild(element);
        preview.classList.remove('d-none');
    }
}

function updateModeStatusLabel() {
    if (modeStatus) {
        const label = currentTransferMode === 'store' ? 'Server storage' : 'Realtime WebRTC';
        modeStatus.innerHTML = `<i class="fa-solid fa-arrows-rotate me-1"></i>Transfer mode: ${label}`;
        modeStatus.classList.toggle('bg-info-subtle', currentTransferMode !== 'store');
        modeStatus.classList.toggle('text-info', currentTransferMode !== 'store');
        modeStatus.classList.toggle('bg-warning-subtle', currentTransferMode === 'store');
        modeStatus.classList.toggle('text-warning', currentTransferMode === 'store');
    }

    if (dropZoneText) {
        dropZoneText.innerHTML = currentTransferMode === 'store'
            ? 'Drag & drop files here or <span class="text-primary">click to browse</span> to upload & store on the server'
            : 'Drag & drop files here or <span class="text-primary">click to browse</span> for realtime WebRTC transfer';
    }
}

function renderMessage(message, isOwn = false) {
    if (!chatFeed || !message) return;
    const direction = isOwn || message.sender_id === currentUser.id ? 'out' : 'in';
    const senderName = message.display_name || (direction === 'out' ? currentUser.display_name : 'Participant');

    if (message.message_type === 'text') {
        const bubble = createTextBubble({
            direction,
            sender: senderName,
            message: message.message_text || '',
            timestamp: message.created_at
        });
        chatFeed.appendChild(bubble);
        scrollToBottom();
        return;
    }

    if (message.message_type === 'file') {
        if (message.transfer_mode === 'store' && message.file_path) {
            const existing = message.id ? chatFeed.querySelector(`[data-message-id="${message.id}"]`) : null;
            if (existing) return;
            const card = createStoredFileCard({
                messageId: message.id,
                direction,
                sender: senderName,
                timestamp: message.created_at,
                fileName: message.file_name,
                fileType: message.file_type,
                fileUrl: message.file_path
            });
            chatFeed.appendChild(card);
            if (message.file_path && message.file_type && message.file_type.startsWith('image/')) {
                renderPreview(card, message.file_path, message.file_type);
            }
            scrollToBottom();
            return;
        }

        const log = createWebRTCLog({
            messageId: message.id,
            direction,
            sender: senderName,
            timestamp: message.created_at,
            fileName: message.file_name || 'File shared'
        });
        chatFeed.appendChild(log);
        scrollToBottom();
    }
}

function handleHistory() {
    history.forEach((item) => {
        renderMessage(item, item.sender_id === currentUser.id);
    });
    scrollToBottom();
}

function updateUserList(users) {
    if (!Array.isArray(users)) return;
    lastRoster = users;
    userList.innerHTML = '';
    let onlineCount = 0;

    users.forEach((user) => {
        const isOnline = user.status === 'online';
        if (isOnline) {
            onlineCount += 1;
        } else {
            clearPeerReconnect(user.userId);
            closePeer(user.userId, { clearReconnect: true });
        }
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        const safeName = escapeHtml(user.displayName);
        li.innerHTML = `
            <span><i class="fa-solid fa-circle me-2 ${isOnline ? 'text-success' : 'text-secondary'}"></i>${safeName}</span>
            <span class="badge ${isOnline ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary'}">${isOnline ? 'Online' : 'Offline'}</span>
        `;
        userList.appendChild(li);
    });

    userCount.textContent = onlineCount;

    if (shouldUseWebRTC()) {
        ensurePeerConnections(users);
    }
}
function closePeer(peerId, { clearReconnect = false } = {}) {
    const peer = peers.get(peerId);
    if (!peer) return;
    if (peer.dataChannel) {
        try {
            peer.dataChannel.close();
        } catch (error) {
            console.warn('Failed to close data channel', error);
        }
    }
    if (peer.pc) {
        try {
            peer.pc.close();
        } catch (error) {
            console.warn('Failed to close peer connection', error);
        }
    }
    peers.delete(peerId);
    if (clearReconnect) {
        clearPeerReconnect(peerId);
    }
}

function closeAllPeerConnections() {
    Array.from(peers.keys()).forEach((peerId) => closePeer(peerId, { clearReconnect: true }));
}

function ensurePeerConnections(users) {
    if (!shouldUseWebRTC()) return;
    users.forEach((user) => {
        if (user.userId === currentUser.id || user.status !== 'online') return;
        if (!peers.has(user.userId) && currentUser.id > user.userId) {
            createOffer(user.userId);
        }
    });
}

function setupDataChannel(peerId, channel) {
    const peer = peers.get(peerId);
    if (peer) {
        peer.dataChannel = channel;
    }

    channel.binaryType = 'arraybuffer';
    if ('bufferedAmountLowThreshold' in channel) {
        channel.bufferedAmountLowThreshold = DATA_CHANNEL_LOW_WATERMARK;
    }

    channel.onopen = () => {
        console.log('Data channel open with', peerId);
        clearPeerReconnect(peerId);
    };

    channel.onmessage = async (event) => {
        if (typeof event.data === 'string') {
            try {
                const payload = JSON.parse(event.data);
                handleDataMessage(peerId, payload);
            } catch (error) {
                console.error('Invalid message', error);
            }
        }
    };

    channel.onclose = () => {
        schedulePeerReconnect(peerId);
    };

    channel.onerror = () => {
        schedulePeerReconnect(peerId);
    };
}

function createPeerConnection(peerId, isInitiator) {
    if (!shouldUseWebRTC()) return null;

    const peerConnection = new RTCPeerConnection(configuration);

    peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
            socket.emit('ice-candidate', { roomId, target: peerId, candidate: event.candidate });
        }
    };

    peerConnection.onconnectionstatechange = () => {
        const state = peerConnection.connectionState;
        if (state === 'connected') {
            clearPeerReconnect(peerId);
        }
        if (state === 'disconnected' || state === 'failed') {
            schedulePeerReconnect(peerId);
        }
        if (state === 'closed') {
            closePeer(peerId);
        }
    };

    peerConnection.ondatachannel = (event) => {
        setupDataChannel(peerId, event.channel);
    };

    peers.set(peerId, { pc: peerConnection, dataChannel: null });

    if (isInitiator) {
        const channel = peerConnection.createDataChannel('file');
        setupDataChannel(peerId, channel);
    }

    return peerConnection;
}

async function createOffer(peerId) {
    if (!shouldUseWebRTC()) return;
    const peerConnection = createPeerConnection(peerId, true);
    if (!peerConnection) return;
    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);
    socket.emit('offer', { roomId, target: peerId, offer });
}

async function handleOffer(data) {
    if (!shouldUseWebRTC()) return;
    const { from, offer } = data;
    const peerConnection = createPeerConnection(from, false);
    if (!peerConnection) return;
    await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
    const answer = await peerConnection.createAnswer();
    await peerConnection.setLocalDescription(answer);
    socket.emit('answer', { roomId, target: from, answer });
}

async function handleAnswer(data) {
    if (!shouldUseWebRTC()) return;
    const { from, answer } = data;
    const peer = peers.get(from);
    if (!peer || !peer.pc) return;
    const state = peer.pc.signalingState;
    if (state !== 'have-local-offer' && state !== 'have-local-pranswer') {
        console.warn('Ignoring unexpected answer for peer', from, 'in state', state);
        return;
    }
    try {
        await peer.pc.setRemoteDescription(new RTCSessionDescription(answer));
    } catch (error) {
        if (error.name === 'InvalidStateError') {
            console.warn('Skipped applying answer in invalid state for peer', from, error);
        } else {
            console.error('Failed to apply remote answer for peer', from, error);
        }
    }
}

async function handleIceCandidate(data) {
    if (!shouldUseWebRTC()) return;
    const { from, candidate } = data;
    const peer = peers.get(from);
    if (!peer) return;
    try {
        await peer.pc.addIceCandidate(new RTCIceCandidate(candidate));
    } catch (error) {
        console.error('Error adding ICE candidate', error);
    }
}

function handleDataMessage(peerId, payload) {
    if (!payload || !payload.type) return;

    switch (payload.type) {
        case 'file-meta':
            initiateFileReception(peerId, payload);
            break;
        case 'file-chunk':
            receiveFileChunk(peerId, payload);
            break;
        case 'file-complete':
            finalizeFile(peerId, payload);
            break;
        case 'text-message':
            renderMessage({
                message_type: 'text',
                message_text: payload.message,
                display_name: payload.sender,
                created_at: new Date().toISOString(),
                sender_id: payload.senderId || null
            });
            break;
        default:
            break;
    }
}

function initiateFileReception(peerId, payload) {
    const meta = {
        fileId: payload.fileId,
        name: payload.name,
        size: payload.size,
        mime: payload.mime,
        sender: payload.sender,
        timestamp: payload.timestamp
    };
    const card = createTransferCard(meta, 'in');
    incomingFiles.set(payload.fileId, {
        meta,
        peerId,
        card,
        receivedSize: 0,
        chunks: []
    });
}

function receiveFileChunk(peerId, payload) {
    const transfer = incomingFiles.get(payload.fileId);
    if (!transfer) return;
    const binary = Uint8Array.from(atob(payload.data), (c) => c.charCodeAt(0));
    transfer.chunks.push(binary);
    transfer.receivedSize += binary.length;
    const progress = Math.min(100, Math.round((transfer.receivedSize / transfer.meta.size) * 100));
    updateProgress(transfer.card, progress);
}

function finalizeFile(peerId, payload) {
    const transfer = incomingFiles.get(payload.fileId);
    if (!transfer) return;
    const blob = new Blob(transfer.chunks, { type: transfer.meta.mime });
    updateProgress(transfer.card, 100);
    renderPreview(transfer.card, blob, transfer.meta.mime);

    const downloadZone = transfer.card.querySelector('.card-body');
    if (downloadZone) {
        const downloadLink = document.createElement('a');
        downloadLink.className = 'btn btn-sm btn-outline-success mt-3';
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = transfer.meta.name;
        downloadLink.innerHTML = '<i class="fa-solid fa-download me-1"></i>Download';
        downloadZone.appendChild(downloadLink);
    }

    incomingFiles.delete(payload.fileId);
}

async function logFileMessage(meta, transferMode) {
    const response = await fetch(messageEndpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            roomId,
            senderId: currentUser.id,
            messageType: 'file',
            fileName: meta.name,
            fileType: meta.mime,
            transferMode
        })
    });
    const data = await response.json();
    if (!response.ok) {
        throw new Error(data.error || 'Failed to log file');
    }
    socket.emit('room-message', { roomId, message: data });
    return data;
}
async function sendFileViaWebRTC(file) {
    if (!shouldUseWebRTC()) {
        return;
    }

    const meta = {
        fileId: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
        name: file.name,
        size: file.size,
        mime: file.type || 'application/octet-stream',
        sender: currentUser.display_name || currentUser.displayName || 'Me',
        timestamp: new Date().toISOString()
    };

    const card = createTransferCard(meta, 'out');

    const livePeers = Array.from(peers.entries())
        .map(([peerId, peer]) => ({ peerId, channel: peer.dataChannel }))
        .filter(({ channel }) => channel && channel.readyState === 'open');

    const startedWithPeers = livePeers.length > 0;

    if (livePeers.length === 0) {
        appendSystemMessage('No connected peers available for realtime transfer right now. The file will be logged for history.');
    }

    const sendToPeers = async (payload) => {
        for (let index = livePeers.length - 1; index >= 0; index -= 1) {
            const target = livePeers[index];
            try {
                await sendChannelMessage(target.channel, payload);
            } catch (error) {
                console.error(`Failed to send data to peer ${target.peerId}`, error);
                schedulePeerReconnect(target.peerId);
                livePeers.splice(index, 1);
            }
        }
    };

    await sendToPeers({ type: 'file-meta', ...meta });
    let transferInterrupted = livePeers.length === 0;
    let firstChunkSent = false;

    for (let offset = 0; offset < file.size && livePeers.length; offset += CHUNK_SIZE) {
        let chunk;
        try {
            chunk = await readFileChunk(file, offset, offset + CHUNK_SIZE);
        } catch (error) {
            console.error('Failed to read file chunk', error);
            if (error && error.name === 'NotReadableError') {
                appendSystemMessage('The browser could not read the selected file. Please ensure it is still accessible and try again.');
            } else {
                appendSystemMessage('A file read error interrupted the realtime transfer.');
            }
            if (!firstChunkSent) {
                throw error;
            }
            transferInterrupted = true;
            break;
        }

        if (!chunk.length) {
            break;
        }

        const payload = {
            type: 'file-chunk',
            fileId: meta.fileId,
            data: encodeChunkToBase64(chunk)
        };
        await sendToPeers(payload);
        firstChunkSent = true;
        if (!livePeers.length) {
            transferInterrupted = true;
            break;
        }
        const progress = Math.round(((offset + chunk.length) / file.size) * 100);
        updateProgress(card, progress);
    }

    if (!transferInterrupted && livePeers.length) {
        await sendToPeers({
            type: 'file-complete',
            fileId: meta.fileId,
            senderId: currentUser.id
        });
        if (!livePeers.length) {
            transferInterrupted = true;
        }
    }

    if (transferInterrupted && startedWithPeers) {
        appendSystemMessage('Realtime transfer interrupted. Waiting for peers to reconnect.');
    } else {
        updateProgress(card, 100);
        if (toastInstance) {
            toastInstance.show();
        }
    }

    renderPreview(card, file, meta.mime);

    try {
        await logFileMessage(meta, 'webrtc');
    } catch (error) {
        console.error('Failed to log realtime file', error);
    }
}

async function uploadFileToServer(file) {
    const formData = new FormData();
    formData.append('roomId', roomId);
    formData.append('senderId', currentUser.id);
    formData.append('transferMode', 'store');
    formData.append('file', file);

    try {
        const response = await fetch(messageEndpoint, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Failed to upload file');
        }
        renderMessage(data, true);
        socket.emit('room-message', { roomId, message: data });
        if (toastInstance) {
            toastInstance.show();
        }
    } catch (error) {
        console.error('Upload failed', error);
        alert('Failed to upload file. Please try again.');
    }
}

async function sendFile(file) {
    if (shouldUseWebRTC()) {
        try {
            await sendFileViaWebRTC(file);
            return;
        } catch (error) {
            const isPermissionError = error && error.name === 'NotReadableError';
            const message = isPermissionError
                ? 'Realtime transfer failed because the browser could not read the file. Attempting a server upload instead.'
                : 'Realtime transfer failed unexpectedly. Attempting a server upload instead.';
            appendSystemMessage(message);
            console.warn('Falling back to server upload after realtime failure', error);
        }
    }

    await uploadFileToServer(file);
}

async function postTextMessage(message) {
    const response = await fetch(messageEndpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            roomId,
            senderId: currentUser.id,
            messageType: 'text',
            message,
            transferMode: currentTransferMode
        })
    });
    const data = await response.json();
    if (!response.ok) {
        throw new Error(data.error || 'Failed to send message');
    }
    return data;
}

async function sendTextMessage(text) {
    try {
        sendMessageBtn.disabled = true;
        const message = await postTextMessage(text);
        renderMessage(message, true);
        socket.emit('room-message', { roomId, message });
    } catch (error) {
        console.error('Message send failed', error);
        alert('Unable to send message. Please retry.');
    } finally {
        sendMessageBtn.disabled = false;
    }
}
function setupDragAndDrop() {
    if (!dropZone || !fileInput) return;

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

    dropZone.addEventListener('drop', async (event) => {
        event.preventDefault();
        dropZone.classList.remove('dragover');
        const files = Array.from(event.dataTransfer.files);
        for (const file of files) {
            await sendFile(file);
        }
    });

    fileInput.addEventListener('change', async (event) => {
        const files = Array.from(event.target.files);
        for (const file of files) {
            await sendFile(file);
        }
        fileInput.value = '';
    });
}

function setupMessageForm() {
    if (!sendMessageBtn || !messageInput) return;

    sendMessageBtn.addEventListener('click', () => {
        const text = messageInput.value.trim();
        if (!text) return;
        sendTextMessage(text);
        messageInput.value = '';
    });

    messageInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessageBtn.click();
        }
    });
}

async function applyTransferMode(newMode, { remote = false } = {}) {
    const normalized = newMode === 'store' ? 'store' : 'webrtc';
    if (currentTransferMode === normalized) {
        if (remote && modeToggle) {
            modeToggle.checked = normalized === 'store';
        }
        updateModeStatusLabel();
        return;
    }

    currentTransferMode = normalized;
    updateModeStatusLabel();
    if (modeToggle) {
        modeToggle.checked = currentTransferMode === 'store';
    }

    if (currentTransferMode === 'store') {
        closeAllPeerConnections();
    } else if (shouldUseWebRTC()) {
        ensurePeerConnections(lastRoster);
    }
}

async function updateRoomTransferMode(mode) {
    const normalized = mode === 'store' ? 'store' : 'webrtc';
    try {
        modeToggle.disabled = true;
        const phpResponse = await fetch('update_room_mode.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ roomId, transferMode: normalized })
        });
        if (!phpResponse.ok) {
            const error = await phpResponse.json();
            throw new Error(error.error || 'Failed to persist mode');
        }

        const nodeResponse = await fetch(`${API_BASE}/api/rooms/${encodeURIComponent(roomId)}/mode`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ transferMode: normalized })
        });
        const data = await nodeResponse.json();
        if (!nodeResponse.ok) {
            throw new Error(data.error || 'Realtime server rejected mode change');
        }
        await applyTransferMode(data.transferMode || normalized);
    } catch (error) {
        console.error('Unable to change transfer mode', error);
        alert('Unable to update transfer mode. Please try again.');
        if (modeToggle) {
            modeToggle.checked = currentTransferMode === 'store';
        }
    } finally {
        modeToggle.disabled = false;
    }
}

function setupModeToggle() {
    if (!modeToggle) return;
    modeToggle.checked = currentTransferMode === 'store';
    modeToggle.addEventListener('change', () => {
        const nextMode = modeToggle.checked ? 'store' : 'webrtc';
        updateRoomTransferMode(nextMode);
    });
}

socket.on('connect', () => {
    socket.emit('join-room', {
        roomId,
        userId: currentUser.id,
        displayName: currentUser.display_name || currentUser.displayName
    });
});

socket.on('room-full', () => {
    alert('Room is full. Only two participants are allowed in a direct room.');
    window.location.href = 'index.php';
});

socket.on('room-users', (users) => {
    updateUserList(users);
});

socket.on('user-joined', (payload) => {
    appendSystemMessage(`${payload.displayName} joined the room`);
    if (shouldUseWebRTC() && payload.userId !== currentUser.id && currentUser.id > payload.userId) {
        createOffer(payload.userId);
    }
});

socket.on('user-left', (payload) => {
    appendSystemMessage(`${payload.displayName} left the room`);
    closePeer(payload.userId, { clearReconnect: true });
});

socket.on('offer', handleOffer);
socket.on('answer', handleAnswer);
socket.on('ice-candidate', handleIceCandidate);

socket.on('room-info', (payload) => {
    if (!payload) return;
    applyTransferMode(payload.transferMode || currentTransferMode, { remote: true });
});

socket.on('room-message', ({ message }) => {
    renderMessage(message, message.sender_id === currentUser.id);
});

setupDragAndDrop();
setupMessageForm();
setupModeToggle();
updateModeStatusLabel();
handleHistory();
scrollToBottom();
