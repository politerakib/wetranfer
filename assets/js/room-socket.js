(function initializeRoomSocket(window) {
    const context = window.RoomContext;
    const ui = window.RoomUI;
    const webRTC = window.RoomWebRTC;
    if (!context || !ui || !webRTC) {
        throw new Error('RoomContext, RoomUI, and RoomWebRTC must be ready before RoomSocket');
    }

    const { state } = context;
    const socket = io(state.apiBase, { transports: ['websocket'] });
    context.socket = socket;

    function emitRoomMessage(message) {
        socket.emit('room-message', { roomId: state.roomId, message });
    }

    function initializeHandlers(handlers = {}) {
        const {
            onUsers,
            onMessage,
            onRoomInfo,
            onRoomFull,
            onUserJoined,
            onUserLeft
        } = handlers;

        socket.on('connect', () => {
            socket.emit('join-room', {
                roomId: state.roomId,
                userId: state.currentUser.id,
                displayName: state.currentUser.display_name || state.currentUser.displayName
            });
        });

        socket.on('room-full', () => {
            if (typeof onRoomFull === 'function') {
                onRoomFull();
            } else {
                alert('Room is full. Only two participants are allowed in a direct room.');
                window.location.href = 'index.php';
            }
        });

        socket.on('room-users', (users) => {
            if (typeof onUsers === 'function') {
                onUsers(users);
            }
        });

        socket.on('user-joined', (payload) => {
            if (typeof onUserJoined === 'function') {
                onUserJoined(payload);
            } else {
                ui.appendSystemMessage(`${payload.displayName} joined the room`);
                if (webRTC.shouldUseWebRTC()
                    && payload.userId !== state.currentUser.id
                    && state.currentUser.id > payload.userId) {
                    webRTC.createOffer(payload.userId);
                }
            }
        });

        socket.on('user-left', (payload) => {
            if (typeof onUserLeft === 'function') {
                onUserLeft(payload);
            } else {
                ui.appendSystemMessage(`${payload.displayName} left the room`);
                webRTC.closePeer(payload.userId, { clearReconnect: true });
            }
        });

        socket.on('offer', (data) => webRTC.handleOffer(data));
        socket.on('answer', (data) => webRTC.handleAnswer(data));
        socket.on('ice-candidate', (data) => webRTC.handleIceCandidate(data));

        socket.on('room-info', (payload) => {
            if (typeof onRoomInfo === 'function') {
                onRoomInfo(payload);
            }
        });

        socket.on('room-message', ({ message }) => {
            if (typeof onMessage === 'function') {
                onMessage(message);
            }
        });
    }

    window.RoomSocket = {
        emitRoomMessage,
        initializeHandlers,
        socket
    };
})(window);
