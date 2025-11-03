const API_BASE = window.APP_CONFIG.apiBase;
const roomId = window.APP_CONFIG.roomId;
const currentUser = window.APP_CONFIG.user;
const history = window.APP_CONFIG.history || [];

const socket = io(API_BASE, { transports: ['websocket'] });
const configuration = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };

const peers = new Map();
const chatFeed = document.getElementById('chatFeed');
const userList = document.getElementById('userList');
const userCount = document.getElementById('userCount');
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const messageInput = document.getElementById('messageInput');
const sendMessageBtn = document.getElementById('sendMessageBtn');
const successToast = document.getElementById('successToast');
const toastInstance = successToast ? new bootstrap.Toast(successToast, { delay: 2500 }) : null;

const CHUNK_SIZE = 16000;

function scrollToBottom() {
    chatFeed.scrollTop = chatFeed.scrollHeight;
}

function appendSystemMessage(text) {
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

function createFileCard({ id, name, mime, sender, timestamp, direction }) {
    const card = document.createElement('div');
    card.className = 'card file-card shadow-sm mb-3';
    card.dataset.fileId = id;
    card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid ${fileTypeIcon(mime)} fa-2x text-primary"></i>
                    <div>
                        <h5 class="card-title mb-1">${name}</h5>
                        <div class="text-secondary small">${sender} • ${new Date(timestamp).toLocaleTimeString()}</div>
                    </div>
                </div>
                <span class="badge ${direction === 'out' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success'}">
                    ${direction === 'out' ? 'Sent' : 'Received'}
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
    const progressBar = card.querySelector('.progress-bar');
    progressBar.style.width = `${value}%`;
    progressBar.textContent = `${value}%`;
}

function renderPreview(card, file) {
    const preview = card.querySelector('.file-preview');
    if (!preview) return;
    let element = null;
    if (file.type.startsWith('image/')) {
        element = document.createElement('img');
        element.src = URL.createObjectURL(file);
        element.alt = file.name;
    } else if (file.type.startsWith('video/')) {
        element = document.createElement('video');
        element.src = URL.createObjectURL(file);
        element.controls = true;
    } else if (file.type === 'application/pdf') {
        element = document.createElement('iframe');
        element.src = URL.createObjectURL(file);
    }

    if (element) {
        preview.innerHTML = '';
        preview.appendChild(element);
        preview.classList.remove('d-none');
    }
}

function addUserToList(user) {
    const item = document.createElement('li');
    item.className = 'list-group-item d-flex justify-content-between align-items-center';
    item.id = `user-${user.userId}`;
    item.innerHTML = `
        <span><i class="fa-solid fa-circle me-2 text-success"></i>${user.displayName}</span>
        <span class="badge bg-light text-secondary">${user.status || 'online'}</span>
    `;
    return item;
}

function updateUserList(users) {
    userList.innerHTML = '';
    users.forEach((user) => {
        userList.appendChild(addUserToList(user));
    });
    userCount.textContent = users.length;
}

function setupDataChannel(peerId, channel) {
    const peer = peers.get(peerId);
    if (peer) {
        peer.dataChannel = channel;
    }

    channel.binaryType = 'arraybuffer';

    channel.onopen = () => {
        console.log('Data channel open with', peerId);
    };

    channel.onmessage = async (event) => {
        if (typeof event.data === 'string') {
            try {
                const payload = JSON.parse(event.data);
                handleDataMessage(peerId, payload);
            } catch (error) {
                console.error('Invalid message', error);
            }
        } else if (event.data instanceof ArrayBuffer) {
            console.warn('Unexpected ArrayBuffer without metadata');
        }
    };
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
            appendSystemMessage(`${payload.sender}: ${payload.message}`);
            break;
        default:
            console.warn('Unknown payload type', payload.type);
    }
}

function initiateFileReception(peerId, meta) {
    const card = createFileCard({
        id: meta.fileId,
        name: meta.name,
        mime: meta.mime,
        sender: meta.sender,
        timestamp: meta.timestamp,
        direction: 'in'
    });

    peers.get(peerId).receiving = peers.get(peerId).receiving || {};
    peers.get(peerId).receiving[meta.fileId] = {
        meta,
        receivedSize: 0,
        chunks: [],
        card
    };
}

function receiveFileChunk(peerId, payload) {
    const peer = peers.get(peerId);
    if (!peer || !peer.receiving || !peer.receiving[payload.fileId]) return;

    const transfer = peer.receiving[payload.fileId];
    const binary = Uint8Array.from(atob(payload.data), (c) => c.charCodeAt(0));
    transfer.chunks.push(binary);
    transfer.receivedSize += binary.byteLength;
    const progress = Math.round((transfer.receivedSize / transfer.meta.size) * 100);
    updateProgress(transfer.card, Math.min(progress, 100));
}

function finalizeFile(peerId, payload) {
    const peer = peers.get(peerId);
    if (!peer || !peer.receiving) return;

    const transfer = peer.receiving[payload.fileId];
    if (!transfer) return;

    const blob = new Blob(transfer.chunks, { type: transfer.meta.mime });
    const file = new File([blob], transfer.meta.name, { type: transfer.meta.mime });
    updateProgress(transfer.card, 100);
    renderPreview(transfer.card, file);

    const downloadLink = document.createElement('a');
    downloadLink.href = URL.createObjectURL(blob);
    downloadLink.download = transfer.meta.name;
    downloadLink.className = 'btn btn-outline-primary btn-sm mt-3';
    downloadLink.innerHTML = '<i class="fa-solid fa-download me-2"></i>Download';
    transfer.card.querySelector('.card-body').appendChild(downloadLink);

    logFileTransfer({
        roomId,
        fileName: transfer.meta.name,
        fileType: transfer.meta.mime,
        senderId: payload.senderId
    });

    delete peer.receiving[payload.fileId];
}

function createPeerConnection(peerId, isInitiator) {
    const peerConnection = new RTCPeerConnection(configuration);
    const peer = { pc: peerConnection, dataChannel: null, receiving: {} };

    peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
            socket.emit('ice-candidate', { roomId, target: peerId, candidate: event.candidate });
        }
    };

    peerConnection.ondatachannel = (event) => {
        setupDataChannel(peerId, event.channel);
    };

    peers.set(peerId, peer);

    if (isInitiator) {
        const channel = peerConnection.createDataChannel('file');
        setupDataChannel(peerId, channel);
    }

    return peerConnection;
}

async function createOffer(peerId) {
    const peerConnection = createPeerConnection(peerId, true);
    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);
    socket.emit('offer', { roomId, target: peerId, offer });
}

async function handleOffer(data) {
    const { from, offer } = data;
    const peerConnection = createPeerConnection(from, false);
    await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
    const answer = await peerConnection.createAnswer();
    await peerConnection.setLocalDescription(answer);
    socket.emit('answer', { roomId, target: from, answer });
}

async function handleAnswer(data) {
    const { from, answer } = data;
    const peer = peers.get(from);
    if (!peer) return;
    await peer.pc.setRemoteDescription(new RTCSessionDescription(answer));
}

async function handleIceCandidate(data) {
    const { from, candidate } = data;
    const peer = peers.get(from);
    if (!peer) return;
    try {
        await peer.pc.addIceCandidate(new RTCIceCandidate(candidate));
    } catch (error) {
        console.error('Error adding ICE candidate', error);
    }
}

function broadcastMessage(message) {
    peers.forEach((peer) => {
        if (peer.dataChannel && peer.dataChannel.readyState === 'open') {
            peer.dataChannel.send(JSON.stringify({
                type: 'text-message',
                message,
                sender: currentUser.display_name || currentUser.displayName
            }));
        }
    });
}

async function sendFile(file) {
    const meta = {
        fileId: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
        name: file.name,
        size: file.size,
        mime: file.type || 'application/octet-stream',
        sender: currentUser.display_name || currentUser.displayName || 'Me',
        timestamp: new Date().toISOString()
    };

    const card = createFileCard({ ...meta, direction: 'out' });

    const arrayBuffer = await file.arrayBuffer();
    const uint8Array = new Uint8Array(arrayBuffer);

    peers.forEach((peer, peerId) => {
        if (!peer.dataChannel || peer.dataChannel.readyState !== 'open') return;
        peer.dataChannel.send(JSON.stringify({ type: 'file-meta', ...meta }));
        for (let offset = 0; offset < uint8Array.length; offset += CHUNK_SIZE) {
            const chunk = uint8Array.subarray(offset, offset + CHUNK_SIZE);
            const base64 = btoa(String.fromCharCode(...chunk));
            peer.dataChannel.send(JSON.stringify({
                type: 'file-chunk',
                fileId: meta.fileId,
                data: base64
            }));
            const progress = Math.round(((offset + chunk.length) / uint8Array.length) * 100);
            updateProgress(card, progress);
        }
        peer.dataChannel.send(JSON.stringify({
            type: 'file-complete',
            fileId: meta.fileId,
            senderId: currentUser.id
        }));
    });

    updateProgress(card, 100);
    renderPreview(card, file);
    if (toastInstance) {
        toastInstance.show();
    }

    logFileTransfer({
        roomId,
        fileName: meta.name,
        fileType: meta.mime,
        senderId: currentUser.id
    });
}

function logFileTransfer(payload) {
    fetch('log_transfer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).catch((error) => console.error('Failed to log file transfer', error));
}

function handleHistory() {
    history.forEach((item) => {
        createFileCard({
            id: `${item.file_name}-${item.created_at}`,
            name: item.file_name,
            mime: item.file_type,
            sender: item.display_name,
            timestamp: item.created_at,
            direction: item.display_name === currentUser.display_name ? 'out' : 'in'
        });
    });
}

function setupDragAndDrop() {
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('dragover');
        const files = Array.from(event.dataTransfer.files);
        files.forEach(sendFile);
    });

    fileInput.addEventListener('change', (event) => {
        const files = Array.from(event.target.files);
        files.forEach(sendFile);
        fileInput.value = '';
    });
}

sendMessageBtn.addEventListener('click', () => {
    const message = messageInput.value.trim();
    if (!message) return;
    appendSystemMessage(`You: ${message}`);
    broadcastMessage(message);
    messageInput.value = '';
});

messageInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessageBtn.click();
    }
});

socket.on('connect', () => {
    socket.emit('join-room', {
        roomId,
        userId: currentUser.id,
        displayName: currentUser.display_name || currentUser.displayName
    });
});

socket.on('room-users', (users) => {
    updateUserList(users);
});

socket.on('user-joined', (payload) => {
    appendSystemMessage(`${payload.displayName} joined the room`);
    if (payload.userId !== currentUser.id) {
        createOffer(payload.userId);
    }
});

socket.on('user-left', (payload) => {
    appendSystemMessage(`${payload.displayName} left the room`);
    const peer = peers.get(payload.userId);
    if (peer) {
        peer.pc.close();
        peers.delete(payload.userId);
    }
});

socket.on('offer', handleOffer);
socket.on('answer', handleAnswer);
socket.on('ice-candidate', handleIceCandidate);

setupDragAndDrop();
handleHistory();
scrollToBottom();
