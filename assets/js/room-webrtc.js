(function initializeRoomWebRTC(window) {
    const context = window.RoomContext;
    const ui = window.RoomUI;
    if (!context || !ui) {
        throw new Error('RoomContext and RoomUI must be loaded before RoomWebRTC');
    }

    const { state, collections, constants } = context;
    const {
        peers,
        incomingFiles,
        channelWaiters,
        reconnectAttempts,
        reconnectTimers
    } = collections;

    const outgoingTransfers = new Map();
    let peerInstance = null;
    let peerReady = false;
    let peerInitializing = false;
    const peerReadyQueue = [];

    function flushPeerReadyQueue({ error = null, peer = null } = {}) {
        while (peerReadyQueue.length) {
            const waiter = peerReadyQueue.shift();
            try {
                if (error) {
                    waiter.reject(error);
                } else {
                    waiter.resolve(peer);
                }
            } catch (err) {
                console.error('Failed to settle peer waiter', err);
            }
        }
    }

    function shouldUseWebRTC() {
        return state.allowWebRTC && state.currentTransferMode === 'webrtc';
    }

    function getLocalPeerId() {
        return `${state.roomId}-${state.currentUser.id}`;
    }

    function extractUserId(value) {
        if (typeof value === 'number') return value;
        if (typeof value === 'string') {
            const parsed = parseInt(value, 10);
            return Number.isNaN(parsed) ? null : parsed;
        }
        return null;
    }

    function getPeerEntry(userId) {
        return peers.get(userId);
    }

    function getOpenConnection(userId) {
        const entry = getPeerEntry(userId);
        if (!entry || !entry.connection) return null;
        return entry.connection.open ? entry.connection : null;
    }

    function resolveChannelWaiters(peerId, connection) {
        const waiters = channelWaiters.get(peerId);
        if (!waiters) return;
        channelWaiters.delete(peerId);
        waiters.forEach(({ resolve, timer }) => {
            clearTimeout(timer);
            try {
                resolve(connection);
            } catch (error) {
                console.error('Failed to resolve channel waiter', error);
            }
        });
    }

    function rejectChannelWaiters(peerId, error) {
        const waiters = channelWaiters.get(peerId);
        if (!waiters) return;
        channelWaiters.delete(peerId);
        waiters.forEach(({ reject, timer }) => {
            clearTimeout(timer);
            try {
                reject(error);
            } catch (err) {
                console.error('Failed to reject channel waiter', err);
            }
        });
    }

    function waitForChannelOpen(peerId, timeout = constants.CHANNEL_WAIT_TIMEOUT) {
        const existing = getOpenConnection(peerId);
        if (existing) {
            return Promise.resolve(existing);
        }

        return new Promise((resolve, reject) => {
            const waiters = channelWaiters.get(peerId) || [];
            let entry = null;
            const timer = setTimeout(() => {
                const current = channelWaiters.get(peerId) || [];
                const index = current.indexOf(entry);
                if (index !== -1) {
                    current.splice(index, 1);
                    if (current.length) {
                        channelWaiters.set(peerId, current);
                    } else {
                        channelWaiters.delete(peerId);
                    }
                }
                reject(new Error('Timed out waiting for PeerJS connection to open'));
            }, timeout);

            entry = {
                resolve,
                reject,
                timer
            };

            waiters.push(entry);
            channelWaiters.set(peerId, waiters);
        });
    }

    function waitForConnectionDrain(connection) {
        return new Promise((resolve, reject) => {
            if (!connection || connection.open === false) {
                reject(new Error('Connection closed while draining buffer'));
                return;
            }

            if (!connection.bufferSize || connection.bufferSize <= constants.DATA_CHANNEL_LOW_WATERMARK) {
                resolve();
                return;
            }

            let settled = false;
            const poller = setInterval(() => {
                if (!connection || connection.open === false) {
                    if (!settled) {
                        settled = true;
                        clearInterval(poller);
                        reject(new Error('Connection closed while draining buffer'));
                    }
                    return;
                }

                if (connection.bufferSize <= constants.DATA_CHANNEL_LOW_WATERMARK) {
                    if (!settled) {
                        settled = true;
                        clearInterval(poller);
                        resolve();
                    }
                }
            }, constants.BUFFER_CHECK_INTERVAL);
        });
    }

    async function ensurePeerReady() {
        if (!shouldUseWebRTC()) {
            throw new Error('Realtime mode disabled');
        }

        if (peerReady && peerInstance) {
            return peerInstance;
        }

        if (peerInitializing) {
            return new Promise((resolve, reject) => {
                peerReadyQueue.push({ resolve, reject });
            });
        }

        if (typeof Peer === 'undefined') {
            throw new Error('PeerJS library not loaded');
        }

        peerInitializing = true;

        const peerConfig = {
            host: state.peerConfig.host,
            port: state.peerConfig.port,
            path: state.peerConfig.path,
            secure: state.peerConfig.secure
        };

        peerInstance = new Peer(getLocalPeerId(), peerConfig);

        peerInstance.on('open', (id) => {
            state.peerId = id;
            peerReady = true;
            peerInitializing = false;
            flushPeerReadyQueue({ peer: peerInstance });
        });

        peerInstance.on('connection', (connection) => {
            const remoteUserId = extractUserId(connection?.metadata?.userId)
                || extractUserId(connection?.peer?.split('-').pop());
            if (!remoteUserId) {
                console.warn('Connection missing remote user id metadata');
                connection.close();
                return;
            }
            registerConnection(remoteUserId, connection, { initiated: false });
        });

        peerInstance.on('disconnected', () => {
            if (peerInstance && !peerInstance.destroyed) {
                try {
                    peerInstance.reconnect();
                } catch (error) {
                    console.warn('Failed to trigger PeerJS reconnect', error);
                }
            }
        });

        peerInstance.on('error', (error) => {
            console.error('PeerJS error', error);
            peerInitializing = false;
            if (!peerReady) {
                flushPeerReadyQueue({ error });
            }
            if (error && (error.type === 'unavailable-id' || error.type === 'network')) {
                setTimeout(() => {
                    if (peerInstance && !peerInstance.destroyed) {
                        try {
                            peerInstance.destroy();
                        } catch (destroyError) {
                            console.warn('Failed to destroy PeerJS instance after error', destroyError);
                        }
                    }
                    peerInstance = null;
                    peerReady = false;
                    ensurePeerReady().catch((retryError) => {
                        console.error('PeerJS auto-retry failed', retryError);
                    });
                }, constants.PEER_RECONNECT_DELAY);
            }
        });

        return new Promise((resolve, reject) => {
            peerReadyQueue.push({ resolve, reject });
        });
    }

    function clearPeerReconnect(peerId) {
        const timer = reconnectTimers.get(peerId);
        if (timer) {
            clearTimeout(timer);
            reconnectTimers.delete(peerId);
        }
        reconnectAttempts.delete(peerId);
    }

    function schedulePeerReconnect(peerId, { immediate = false } = {}) {
        if (!shouldUseWebRTC()) return;
        if (reconnectTimers.has(peerId)) return;

        const rosterEntry = Array.isArray(state.lastRoster)
            ? state.lastRoster.find((user) => user.userId === peerId && user.status === 'online')
            : null;

        if (!rosterEntry) {
            return;
        }

        const attempt = reconnectAttempts.get(peerId) || 0;
        if (attempt >= constants.MAX_RECONNECT_ATTEMPTS) {
            console.warn('Maximum reconnect attempts reached for peer', peerId);
            return;
        }

        const baseDelay = Math.min(constants.RECONNECT_BASE_DELAY * Math.pow(2, attempt), 5000);
        const delay = immediate ? 0 : baseDelay;
        reconnectAttempts.set(peerId, attempt + 1);

        const timer = setTimeout(() => {
            reconnectTimers.delete(peerId);
            connectToPeer(peerId).catch((error) => {
                console.warn('Reconnect attempt failed for peer', peerId, error);
            });
        }, delay);
        reconnectTimers.set(peerId, timer);
    }

    function registerConnection(peerId, connection, { initiated }) {
        const existing = peers.get(peerId);
        if (existing && existing.connection && existing.connection !== connection) {
            try {
                existing.connection.close();
            } catch (error) {
                console.warn('Failed to close stale connection', error);
            }
        }

        connection.metadata = connection.metadata || {};
        connection.serialization = 'json';

        peers.set(peerId, {
            connection,
            initiated,
            ack: existing ? existing.ack : 0
        });

        connection.on('open', () => {
            clearPeerReconnect(peerId);
            resolveChannelWaiters(peerId, connection);
            notifySenderOfPendingTransfers(peerId);
            notifyReceiverOfPendingTransfers(peerId);
        });

        connection.on('data', (payload) => {
            try {
                handleDataMessage(peerId, payload);
            } catch (error) {
                console.error('Failed to process data message', error);
            }
        });

        connection.on('close', () => {
            rejectChannelWaiters(peerId, new Error('Connection closed'));
            schedulePeerReconnect(peerId, { immediate: true });
        });

        connection.on('error', (error) => {
            console.warn('Peer connection error with', peerId, error);
            rejectChannelWaiters(peerId, error);
            schedulePeerReconnect(peerId, { immediate: true });
        });
    }

    async function connectToPeer(peerId) {
        if (!shouldUseWebRTC()) return null;
        if (peerId === state.currentUser.id) return null;

        const existing = getOpenConnection(peerId);
        if (existing) {
            return existing;
        }

        await ensurePeerReady();

        const remotePeerId = `${state.roomId}-${peerId}`;
        const connection = peerInstance.connect(remotePeerId, {
            reliable: true,
            metadata: {
                userId: state.currentUser.id,
                displayName: state.currentUser.display_name || state.currentUser.displayName || 'User'
            }
        });

        registerConnection(peerId, connection, { initiated: true });
        return waitForChannelOpen(peerId, constants.CHANNEL_WAIT_TIMEOUT);
    }

    function closePeer(peerId, { clearReconnect = false } = {}) {
        const entry = peers.get(peerId);
        if (entry && entry.connection) {
            try {
                entry.connection.close();
            } catch (error) {
                console.warn('Failed to close connection', error);
            }
        }
        peers.delete(peerId);
        rejectChannelWaiters(peerId, new Error('Peer connection closed'));
        if (clearReconnect) {
            clearPeerReconnect(peerId);
        }
    }

    function closeAllPeerConnections() {
        Array.from(peers.keys()).forEach((peerId) => closePeer(peerId, { clearReconnect: true }));
        if (peerInstance) {
            try {
                peerInstance.destroy();
            } catch (error) {
                console.warn('Failed to destroy peer instance', error);
            }
        }
        peerInstance = null;
        peerReady = false;
        peerInitializing = false;
        state.peerId = null;
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

    function collectTargetPeerIds() {
        const rosterPeers = Array.isArray(state.lastRoster)
            ? state.lastRoster
                .filter((user) => user.userId !== state.currentUser.id && user.status === 'online')
                .map((user) => user.userId)
            : [];
        const peerEntries = Array.from(peers.keys());
        const ids = new Set([...rosterPeers, ...peerEntries]);
        ids.delete(state.currentUser.id);
        return Array.from(ids);
    }

    async function sendPayloadToPeer(peerId, payload) {
        let attempt = 0;
        while (attempt < constants.CHANNEL_RETRY_LIMIT) {
            attempt += 1;
            let connection = getOpenConnection(peerId);
            if (!connection) {
                try {
                    // eslint-disable-next-line no-await-in-loop
                    connection = await waitForChannelOpen(peerId, constants.CHANNEL_WAIT_TIMEOUT);
                } catch (error) {
                    if (attempt >= constants.CHANNEL_RETRY_LIMIT) {
                        throw error;
                    }
                    continue;
                }
            }

            try {
                if (connection.bufferSize && connection.bufferSize > constants.DATA_CHANNEL_MAX_BUFFER) {
                    // eslint-disable-next-line no-await-in-loop
                    await waitForConnectionDrain(connection);
                }
                connection.send(payload);
                if (connection.bufferSize && connection.bufferSize > constants.DATA_CHANNEL_MAX_BUFFER) {
                    // eslint-disable-next-line no-await-in-loop
                    await waitForConnectionDrain(connection);
                }
                return true;
            } catch (error) {
                console.error(`Failed to send payload to peer ${peerId}`, error);
                if (!connection || connection.open === false) {
                    schedulePeerReconnect(peerId, { immediate: true });
                }
            }
        }

        return false;
    }

    async function broadcastPayloadToPeers(payload) {
        const targets = collectTargetPeerIds();
        if (!targets.length) {
            return { delivered: 0, targets: 0 };
        }

        let delivered = 0;
        for (const peerId of targets) {
            // eslint-disable-next-line no-await-in-loop
            const success = await sendPayloadToPeer(peerId, payload);
            if (success) {
                delivered += 1;
            }
        }

        return { delivered, targets: targets.length };
    }

    function initiateFileReception(peerId, payload) {
        const existing = incomingFiles.get(payload.fileId);
        if (existing) {
            existing.meta.size = payload.size;
            existing.meta.name = payload.name;
            existing.meta.mime = payload.mime;
            existing.meta.timestamp = payload.timestamp;
            existing.peerId = peerId;
            ui.updateProgress(existing.card, Math.round((existing.receivedSize / existing.meta.size) * 100));
            return;
        }

        const meta = {
            fileId: payload.fileId,
            name: payload.name,
            size: payload.size,
            mime: payload.mime,
            sender: payload.sender,
            timestamp: payload.timestamp
        };
        const card = ui.createTransferCard(meta, 'in');
        incomingFiles.set(payload.fileId, {
            meta,
            peerId,
            card,
            receivedSize: payload.resumeFrom || 0,
            chunks: []
        });

        if (payload.resumeFrom) {
            ui.updateProgress(card, Math.round((payload.resumeFrom / meta.size) * 100));
        }
    }

    function receiveFileChunk(peerId, payload) {
        const transfer = incomingFiles.get(payload.fileId);
        if (!transfer) return;
        const binary = Uint8Array.from(atob(payload.data), (c) => c.charCodeAt(0));
        transfer.chunks.push(binary);
        transfer.receivedSize += binary.length;
        const progress = Math.min(100, Math.round((transfer.receivedSize / transfer.meta.size) * 100));
        ui.updateProgress(transfer.card, progress);
        sendAckToSender(peerId, payload.fileId, transfer.receivedSize);
    }

    function finalizeFile(peerId, payload) {
        const transfer = incomingFiles.get(payload.fileId);
        if (!transfer) return;
        const blob = new Blob(transfer.chunks, { type: transfer.meta.mime });
        ui.updateProgress(transfer.card, 100);
        ui.renderPreview(transfer.card, blob, transfer.meta.mime);

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

    function handleAckFromPeer(peerId, payload) {
        const transfer = outgoingTransfers.get(payload.fileId);
        if (!transfer) return;
        const recipient = transfer.recipients.get(peerId);
        if (!recipient) return;
        const received = Math.max(0, payload.received || 0);
        if (received <= recipient.ack) return;
        recipient.ack = received;
        const progress = Math.min(100, Math.round((recipient.ack / transfer.meta.size) * 100));
        ui.updateProgress(transfer.card, progress);
        if (recipient.ack >= transfer.meta.size) {
            recipient.completed = true;
            maybeResolveTransfer(transfer);
        }
    }

    function handleResumeRequest(peerId, payload) {
        const transfer = outgoingTransfers.get(payload.fileId);
        if (!transfer) return;
        const recipient = transfer.recipients.get(peerId);
        if (!recipient) return;
        const resumeFrom = Math.max(0, payload.received || 0);
        if (resumeFrom > recipient.ack) {
            recipient.ack = resumeFrom;
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
                sendAckToSender(peerId, payload.fileId, Number.MAX_SAFE_INTEGER);
                break;
            case 'file-ack':
                handleAckFromPeer(peerId, payload);
                break;
            case 'file-resume-request':
                handleResumeRequest(peerId, payload);
                break;
            case 'text-message':
                ui.renderMessage({
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

    function notifySenderOfPendingTransfers(peerId) {
        outgoingTransfers.forEach((transfer) => {
            const recipient = transfer.recipients.get(peerId);
            if (!recipient) return;
            if (recipient.ack >= transfer.meta.size) {
                return;
            }
            sendPayloadToPeer(peerId, {
                type: 'file-resume-request',
                fileId: transfer.meta.fileId,
                received: recipient.ack
            }).catch((error) => {
                console.warn('Failed to notify sender about resume request', error);
            });
        });
    }

    function notifyReceiverOfPendingTransfers(peerId) {
        incomingFiles.forEach((transfer) => {
            if (transfer.peerId !== peerId) return;
            if (transfer.receivedSize >= transfer.meta.size) {
                return;
            }
            sendAckToSender(peerId, transfer.meta.fileId, transfer.receivedSize);
        });
    }

    function sendAckToSender(peerId, fileId, received) {
        sendPayloadToPeer(peerId, {
            type: 'file-ack',
            fileId,
            received
        }).catch((error) => {
            console.warn('Failed to send acknowledgement to peer', error);
        });
    }

    async function logFileMessage(meta, transferMode = 'webrtc') {
        const response = await fetch(state.messageEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                roomId: state.roomId,
                senderId: state.currentUser.id,
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
        if (context.socket) {
            context.socket.emit('room-message', { roomId: state.roomId, message: data });
        }
        return data;
    }

    function maybeResolveTransfer(transfer) {
        const allComplete = Array.from(transfer.recipients.values())
            .every((recipient) => recipient.completed);
        if (allComplete && typeof transfer.resolve === 'function') {
            transfer.resolve();
        }
    }

    function rejectTransfer(transfer, error) {
        if (typeof transfer.reject === 'function') {
            transfer.reject(error);
        }
    }

    async function transmitToPeer(peerId, transfer) {
        const recipient = transfer.recipients.get(peerId);
        if (!recipient) return;

        while (recipient.ack < transfer.meta.size) {
            let connection;
            try {
                // eslint-disable-next-line no-await-in-loop
                connection = await waitForChannelOpen(peerId, constants.CHANNEL_WAIT_TIMEOUT * 2);
            } catch (error) {
                throw new Error(`Unable to reach peer ${peerId} for realtime transfer`);
            }

            const resumeFrom = recipient.ack;
            try {
                // eslint-disable-next-line no-await-in-loop
                await sendPayloadToPeer(peerId, {
                    type: 'file-meta',
                    ...transfer.meta,
                    resumeFrom
                });
            } catch (error) {
                console.warn('Failed to deliver metadata to peer', peerId, error);
                schedulePeerReconnect(peerId, { immediate: true });
                continue;
            }

            let offset = resumeFrom;
            while (offset < transfer.meta.size) {
                let chunk;
                try {
                    // eslint-disable-next-line no-await-in-loop
                    chunk = await readFileChunk(transfer.file, offset, offset + constants.CHUNK_SIZE);
                } catch (error) {
                    console.error('Failed to read file chunk', error);
                    throw error;
                }

                if (!chunk.length) {
                    break;
                }

                try {
                    // eslint-disable-next-line no-await-in-loop
                    await sendPayloadToPeer(peerId, {
                        type: 'file-chunk',
                        fileId: transfer.meta.fileId,
                        data: encodeChunkToBase64(chunk)
                    });
                } catch (error) {
                    console.warn('Realtime chunk delivery failed, retrying after reconnect', error);
                    schedulePeerReconnect(peerId, { immediate: true });
                    break;
                }

                offset += chunk.length;
            }

            if (recipient.ack >= transfer.meta.size) {
                break;
            }

            if (offset >= transfer.meta.size) {
                try {
                    // eslint-disable-next-line no-await-in-loop
                    await sendPayloadToPeer(peerId, {
                        type: 'file-complete',
                        fileId: transfer.meta.fileId,
                        senderId: state.currentUser.id
                    });
                } catch (error) {
                    console.warn('Failed to deliver completion notice, waiting for reconnect', error);
                    schedulePeerReconnect(peerId, { immediate: true });
                }
            }

            await new Promise((resolve) => setTimeout(resolve, constants.PEER_RECONNECT_DELAY));
        }

        recipient.completed = true;
        maybeResolveTransfer(transfer);
    }

    async function sendFileViaWebRTC(file) {
        if (!shouldUseWebRTC()) {
            return;
        }

        await ensurePeerReady();

        const targets = collectTargetPeerIds();
        if (!targets.length) {
            throw new Error('No connected peers available for realtime transfer right now.');
        }

        const meta = {
            fileId: `${Date.now()}-${Math.random().toString(16).slice(2)}`,
            name: file.name,
            size: file.size,
            mime: file.type || 'application/octet-stream',
            sender: state.currentUser.display_name || state.currentUser.displayName || 'Me',
            timestamp: new Date().toISOString()
        };

        const card = ui.createTransferCard(meta, 'out');

        const transfer = {
            file,
            meta,
            card,
            recipients: new Map(),
            resolve: null,
            reject: null
        };

        targets.forEach((peerId) => {
            transfer.recipients.set(peerId, {
                ack: 0,
                completed: false
            });
        });

        outgoingTransfers.set(meta.fileId, transfer);

        const completionPromise = new Promise((resolve, reject) => {
            transfer.resolve = resolve;
            transfer.reject = reject;
        });

        try {
            await Promise.all(targets.map((peerId) => connectToPeer(peerId)));
            await Promise.all(targets.map((peerId) => transmitToPeer(peerId, transfer)));
            await completionPromise;
        } catch (error) {
            rejectTransfer(transfer, error);
            outgoingTransfers.delete(meta.fileId);
            throw error;
        }

        ui.updateProgress(card, 100);
        ui.showToast();
        ui.renderPreview(card, file, meta.mime);

        try {
            await logFileMessage(meta, 'webrtc');
        } catch (error) {
            console.error('Failed to log realtime file', error);
        }

        outgoingTransfers.delete(meta.fileId);
    }

    function ensurePeerConnections(users) {
        if (!shouldUseWebRTC()) return;
        if (!Array.isArray(users)) return;
        users.forEach((user) => {
            if (user.userId === state.currentUser.id || user.status !== 'online') return;
            connectToPeer(user.userId).catch((error) => {
                console.warn('Unable to establish peer connection', error);
            });
        });
    }

    window.RoomWebRTC = {
        broadcastPayloadToPeers,
        closeAllPeerConnections,
        closePeer,
        connectToPeer,
        ensurePeerConnections,
        sendFileViaWebRTC,
        shouldUseWebRTC,
        waitForChannelOpen
    };
})(window);
