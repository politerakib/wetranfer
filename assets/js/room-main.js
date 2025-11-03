(function initializeRoomMain(window) {
    const context = window.RoomContext;
    const ui = window.RoomUI;
    const webRTC = window.RoomWebRTC;
    const socketModule = window.RoomSocket;

    if (!context || !ui || !webRTC || !socketModule) {
        throw new Error('Room scripts are missing dependencies.');
    }

    const { state, dom } = context;

    async function uploadFileToServer(file) {
        const formData = new FormData();
        formData.append('roomId', state.roomId);
        formData.append('senderId', state.currentUser.id);
        formData.append('transferMode', 'store');
        formData.append('file', file);

        try {
            const response = await fetch(state.messageEndpoint, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Failed to upload file');
            }
            ui.renderMessage(data, true);
            socketModule.emitRoomMessage(data);
            ui.showToast();
        } catch (error) {
            console.error('Upload failed', error);
            alert('Failed to upload file. Please try again.');
        }
    }

    async function sendFile(file) {
        if (webRTC.shouldUseWebRTC()) {
            try {
                await webRTC.sendFileViaWebRTC(file);
                return;
            } catch (error) {
                const isPermissionError = error && error.name === 'NotReadableError';
                const message = isPermissionError
                    ? 'Realtime transfer failed because the browser could not read the file. Attempting a server upload instead.'
                    : 'Realtime transfer failed unexpectedly. Attempting a server upload instead.';
                ui.appendSystemMessage(message);
                console.warn('Falling back to server upload after realtime failure', error);
            }
        }

        await uploadFileToServer(file);
    }

    async function postTextMessage(message) {
        const response = await fetch(state.messageEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                roomId: state.roomId,
                senderId: state.currentUser.id,
                messageType: 'text',
                message,
                transferMode: state.currentTransferMode
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
            dom.sendMessageBtn.disabled = true;
            const message = await postTextMessage(text);
            ui.renderMessage(message, true);
            socketModule.emitRoomMessage(message);
        } catch (error) {
            console.error('Message send failed', error);
            alert('Unable to send message. Please retry.');
        } finally {
            dom.sendMessageBtn.disabled = false;
        }
    }

    function setupDragAndDrop() {
        if (!dom.dropZone || !dom.fileInput) return;

        dom.dropZone.addEventListener('click', () => dom.fileInput.click());

        dom.dropZone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dom.dropZone.classList.add('dragover');
        });

        dom.dropZone.addEventListener('dragleave', () => dom.dropZone.classList.remove('dragover'));

        dom.dropZone.addEventListener('drop', async (event) => {
            event.preventDefault();
            dom.dropZone.classList.remove('dragover');
            const files = Array.from(event.dataTransfer.files);
            for (const file of files) {
                await sendFile(file);
            }
        });

        dom.fileInput.addEventListener('change', async (event) => {
            const files = Array.from(event.target.files);
            for (const file of files) {
                await sendFile(file);
            }
            dom.fileInput.value = '';
        });
    }

    function setupMessageForm() {
        if (!dom.sendMessageBtn || !dom.messageInput) return;

        dom.sendMessageBtn.addEventListener('click', () => {
            const text = dom.messageInput.value.trim();
            if (!text) return;
            dom.messageInput.value = '';
            sendTextMessage(text);
        });

        dom.messageInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                dom.sendMessageBtn.click();
            }
        });
    }

    async function applyTransferMode(newMode, { remote = false } = {}) {
        const normalized = newMode === 'store' ? 'store' : 'webrtc';
        if (state.currentTransferMode === normalized) {
            if (remote && dom.modeToggle) {
                dom.modeToggle.checked = normalized === 'store';
            }
            ui.updateModeStatusLabel();
            return;
        }

        state.currentTransferMode = normalized;
        ui.updateModeStatusLabel();
        if (dom.modeToggle) {
            dom.modeToggle.checked = state.currentTransferMode === 'store';
        }

        if (state.currentTransferMode === 'store') {
            webRTC.closeAllPeerConnections();
        } else if (webRTC.shouldUseWebRTC()) {
            webRTC.ensurePeerConnections(state.lastRoster);
        }
    }

    async function updateRoomTransferMode(mode) {
        const normalized = mode === 'store' ? 'store' : 'webrtc';
        try {
            if (dom.modeToggle) {
                dom.modeToggle.disabled = true;
            }
            const phpResponse = await fetch('update_room_mode.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ roomId: state.roomId, transferMode: normalized })
            });
            if (!phpResponse.ok) {
                const error = await phpResponse.json();
                throw new Error(error.error || 'Failed to persist mode');
            }

            const nodeResponse = await fetch(`${state.apiBase}/api/rooms/${encodeURIComponent(state.roomId)}/mode`, {
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
            if (dom.modeToggle) {
                dom.modeToggle.checked = state.currentTransferMode === 'store';
            }
        } finally {
            if (dom.modeToggle) {
                dom.modeToggle.disabled = false;
            }
        }
    }

    function setupModeToggle() {
        if (!dom.modeToggle) return;
        dom.modeToggle.checked = state.currentTransferMode === 'store';
        dom.modeToggle.addEventListener('change', () => {
            const nextMode = dom.modeToggle.checked ? 'store' : 'webrtc';
            updateRoomTransferMode(nextMode);
        });
    }

    socketModule.initializeHandlers({
        onUsers: (users) => {
            ui.renderUserList(users);
        },
        onMessage: (message) => {
            ui.renderMessage(message, message.sender_id === state.currentUser.id);
        },
        onRoomInfo: (payload) => {
            if (!payload) return;
            applyTransferMode(payload.transferMode || state.currentTransferMode, { remote: true });
        },
        onUserJoined: (payload) => {
            ui.appendSystemMessage(`${payload.displayName} joined the room`);
            if (webRTC.shouldUseWebRTC()
                && payload.userId !== state.currentUser.id
                && state.currentUser.id > payload.userId) {
                webRTC.createOffer(payload.userId);
            }
        },
        onUserLeft: (payload) => {
            ui.appendSystemMessage(`${payload.displayName} left the room`);
            webRTC.closePeer(payload.userId, { clearReconnect: true });
        }
    });

    setupDragAndDrop();
    setupMessageForm();
    setupModeToggle();
    ui.updateModeStatusLabel();
    ui.renderHistory(state.history);
    ui.scrollToBottom();
})(window);
