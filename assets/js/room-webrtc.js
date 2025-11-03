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

    function shouldUseWebRTC() {
        return state.allowWebRTC && state.currentTransferMode === 'webrtc';
    }

    function waitForChannelDrain(channel) {
        return new Promise((resolve, reject) => {
            if (!channel || channel.readyState !== 'open') {
                reject(new Error('Data channel closed'));
                return;
            }

            if (channel.bufferedAmount <= constants.DATA_CHANNEL_LOW_WATERMARK) {
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
                } else if (channel.bufferedAmount <= constants.DATA_CHANNEL_LOW_WATERMARK) {
                    finish(true);
                }
            }, constants.BUFFER_CHECK_INTERVAL);
        });
    }

    async function sendChannelMessage(channel, payload) {
        if (!channel || channel.readyState !== 'open') {
            throw new Error('Data channel not open');
        }

        const message = typeof payload === 'string' ? payload : JSON.stringify(payload);

        if (channel.bufferedAmount > constants.DATA_CHANNEL_MAX_BUFFER) {
            await waitForChannelDrain(channel);
        }

        channel.send(message);

        if (channel.bufferedAmount > constants.DATA_CHANNEL_MAX_BUFFER) {
            await waitForChannelDrain(channel);
        }
    }

    function getOpenChannel(peerId) {
        const peer = peers.get(peerId);
        if (!peer || !peer.dataChannel) return null;
        return peer.dataChannel.readyState === 'open' ? peer.dataChannel : null;
    }

    function resolveChannelWaiters(peerId, channel) {
        const waiters = channelWaiters.get(peerId);
        if (!waiters) return;
        channelWaiters.delete(peerId);
        waiters.forEach(({ resolve, timer }) => {
            clearTimeout(timer);
            try {
                resolve(channel);
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
        const existing = getOpenChannel(peerId);
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
                reject(new Error('Timed out waiting for channel to open'));
            }, timeout);

            entry = {
                resolve: (channel) => {
                    resolve(channel);
                },
                reject,
                timer
            };

            waiters.push(entry);
            channelWaiters.set(peerId, waiters);
        });
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

    function closePeer(peerId, { clearReconnect = false } = {}) {
        const peer = peers.get(peerId);
        if (!peer) {
            rejectChannelWaiters(peerId, new Error('Peer connection closed'));
            if (clearReconnect) {
                clearPeerReconnect(peerId);
            }
            return;
        }
        rejectChannelWaiters(peerId, new Error('Peer connection closed'));
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

    function schedulePeerReconnect(peerId, { immediate = false } = {}) {
        if (!shouldUseWebRTC()) return;
        if (reconnectTimers.has(peerId)) return;
        const rosterEntry = Array.isArray(state.lastRoster)
            ? state.lastRoster.find((user) => user.userId === peerId && user.status === 'online')
            : null;
        if (!rosterEntry) return;

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
            closePeer(peerId);
            rejectChannelWaiters(peerId, new Error('Reconnecting peer'));
            ensurePeerConnections(state.lastRoster);
        }, delay);
        reconnectTimers.set(peerId, timer);
    }

    function ensurePeerConnections(users) {
        if (!shouldUseWebRTC()) return;
        if (!Array.isArray(users)) return;
        users.forEach((user) => {
            if (user.userId === state.currentUser.id || user.status !== 'online') return;
            if (!peers.has(user.userId) && state.currentUser.id > user.userId) {
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
            channel.bufferedAmountLowThreshold = constants.DATA_CHANNEL_LOW_WATERMARK;
        }

        channel.onopen = () => {
            console.log('Data channel open with', peerId);
            clearPeerReconnect(peerId);
            resolveChannelWaiters(peerId, channel);
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
            rejectChannelWaiters(peerId, new Error('Channel closed'));
            schedulePeerReconnect(peerId, { immediate: true });
        };

        channel.onerror = () => {
            rejectChannelWaiters(peerId, new Error('Channel error'));
            schedulePeerReconnect(peerId, { immediate: true });
        };
    }

    function createPeerConnection(peerId, isInitiator) {
        if (!shouldUseWebRTC()) return null;

        const peerConnection = new RTCPeerConnection(constants.configuration);

        peerConnection.onicecandidate = (event) => {
            if (event.candidate && context.socket) {
                context.socket.emit('ice-candidate', {
                    roomId: state.roomId,
                    target: peerId,
                    candidate: event.candidate
                });
            }
        };

        peerConnection.onconnectionstatechange = () => {
            const status = peerConnection.connectionState;
            if (status === 'connected') {
                clearPeerReconnect(peerId);
            }
            if (status === 'disconnected' || status === 'failed') {
                schedulePeerReconnect(peerId);
            }
            if (status === 'closed') {
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
        if (context.socket) {
            context.socket.emit('offer', {
                roomId: state.roomId,
                target: peerId,
                offer
            });
        }
    }

    async function handleOffer(data) {
        if (!shouldUseWebRTC()) return;
        const { from, offer } = data;
        const peerConnection = createPeerConnection(from, false);
        if (!peerConnection) return;
        await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        if (context.socket) {
            context.socket.emit('answer', {
                roomId: state.roomId,
                target: from,
                answer
            });
        }
    }

    async function handleAnswer(data) {
        if (!shouldUseWebRTC()) return;
        const { from, answer } = data;
        const peer = peers.get(from);
        if (!peer || !peer.pc) return;
        const status = peer.pc.signalingState;
        if (status !== 'have-local-offer' && status !== 'have-local-pranswer') {
            console.warn('Ignoring unexpected answer for peer', from, 'in state', status);
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

    function initiateFileReception(peerId, payload) {
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
        ui.updateProgress(transfer.card, progress);
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

    async function deliverPayloadToPeer(peerId, payload) {
        let attempt = 0;
        while (attempt < constants.CHANNEL_RETRY_LIMIT) {
            attempt += 1;
            let channel = getOpenChannel(peerId);
            if (!channel) {
                try {
                    channel = await waitForChannelOpen(peerId, constants.CHANNEL_WAIT_TIMEOUT);
                } catch (error) {
                    console.warn(`Waiting for channel to open for peer ${peerId} failed`, error);
                    schedulePeerReconnect(peerId, { immediate: true });
                    continue;
                }
            }

            try {
                await sendChannelMessage(channel, payload);
                return true;
            } catch (error) {
                console.error(`Failed to send payload to peer ${peerId}`, error);
                if (!channel || channel.readyState !== 'open') {
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
            const success = await deliverPayloadToPeer(peerId, payload);
            if (success) {
                delivered += 1;
            }
        }

        return { delivered, targets: targets.length };
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
            sender: state.currentUser.display_name || state.currentUser.displayName || 'Me',
            timestamp: new Date().toISOString()
        };

        const card = ui.createTransferCard(meta, 'out');

        const { delivered: metaDelivered, targets: initialTargets } = await broadcastPayloadToPeers({
            type: 'file-meta',
            ...meta
        });

        const startedWithPeers = initialTargets > 0;
        if (!startedWithPeers) {
            ui.appendSystemMessage('No connected peers available for realtime transfer right now. The file will be logged for history.');
        }
        let transferInterrupted = startedWithPeers && metaDelivered === 0;
        let firstChunkSent = false;

        for (let offset = 0; offset < file.size && !transferInterrupted; offset += constants.CHUNK_SIZE) {
            let chunk;
            try {
                // eslint-disable-next-line no-await-in-loop
                chunk = await readFileChunk(file, offset, offset + constants.CHUNK_SIZE);
            } catch (error) {
                console.error('Failed to read file chunk', error);
                if (error && error.name === 'NotReadableError') {
                    ui.appendSystemMessage('The browser could not read the selected file. Please ensure it is still accessible and try again.');
                } else {
                    ui.appendSystemMessage('A file read error interrupted the realtime transfer.');
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

            // eslint-disable-next-line no-await-in-loop
            const { delivered, targets } = await broadcastPayloadToPeers(payload);
            firstChunkSent = firstChunkSent || delivered > 0;

            if (targets === 0 || (targets > 0 && delivered === 0)) {
                transferInterrupted = true;
                break;
            }

            const progress = Math.round(((offset + chunk.length) / file.size) * 100);
            ui.updateProgress(card, progress);
        }

        if (!transferInterrupted) {
            const { delivered: completionDelivered, targets: completionTargets } = await broadcastPayloadToPeers({
                type: 'file-complete',
                fileId: meta.fileId,
                senderId: state.currentUser.id
            });
            if (completionTargets === 0 || (completionTargets > 0 && completionDelivered === 0)) {
                transferInterrupted = true;
            }
        }

        if (transferInterrupted && startedWithPeers) {
            ui.appendSystemMessage('Realtime transfer interrupted. Waiting for peers to reconnect.');
        } else {
            ui.updateProgress(card, 100);
            ui.showToast();
        }

        ui.renderPreview(card, file, meta.mime);

        try {
            await logFileMessage(meta, 'webrtc');
        } catch (error) {
            console.error('Failed to log realtime file', error);
        }
    }

    function closeAllPeerConnections() {
        Array.from(peers.keys()).forEach((peerId) => closePeer(peerId, { clearReconnect: true }));
    }

    window.RoomWebRTC = {
        broadcastPayloadToPeers,
        closeAllPeerConnections,
        closePeer,
        createOffer,
        ensurePeerConnections,
        handleAnswer,
        handleDataMessage,
        handleIceCandidate,
        handleOffer,
        schedulePeerReconnect,
        sendFileViaWebRTC,
        shouldUseWebRTC,
        waitForChannelOpen
    };
})(window);
