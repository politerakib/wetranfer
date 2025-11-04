(function initializeRoomContext(window) {
    const config = window.APP_CONFIG || {};

    const state = {
        apiBase: config.apiBase || 'http://localhost:3000',
        roomId: config.roomId || '',
        currentUser: config.user || {},
        history: Array.isArray(config.history) ? config.history : [],
        roomType: config.roomType || 'direct',
        messageEndpoint: config.messageEndpoint || 'room_message.php',
        currentTransferMode: config.transferMode
            || ((config.roomType || 'direct') === 'team' ? 'store' : 'webrtc'),
        allowWebRTC: (config.roomType || 'direct') === 'direct',
        lastRoster: [],
        peerId: null,
        peerConfig: null
    };

    if (config.peerConfig && typeof config.peerConfig === 'object'
        && Object.keys(config.peerConfig).length > 0) {
        const rawConfig = config.peerConfig;
        const normalized = {};

        if (rawConfig.host) {
            normalized.host = String(rawConfig.host);
        }

        if (rawConfig.port) {
            const portNumber = parseInt(rawConfig.port, 10);
            if (!Number.isNaN(portNumber)) {
                normalized.port = portNumber;
            }
        }

        if (rawConfig.path) {
            normalized.path = String(rawConfig.path);
        }

        if (typeof rawConfig.secure !== 'undefined') {
            if (typeof rawConfig.secure === 'string') {
                const secureValue = rawConfig.secure.toLowerCase();
                normalized.secure = ['1', 'true', 'on', 'yes'].includes(secureValue);
            } else {
                normalized.secure = !!rawConfig.secure;
            }
        }

        if (normalized.host && typeof normalized.secure === 'undefined') {
            normalized.secure = normalized.port === 443;
        }

        if (normalized.host && !normalized.port) {
            normalized.port = normalized.secure ? 443 : 80;
        }

        if (normalized.host && !normalized.path) {
            normalized.path = '/';
        }

        if (Object.keys(normalized).length > 0) {
            state.peerConfig = normalized;
        }
    }

    const dom = {
        chatFeed: document.getElementById('chatFeed'),
        userList: document.getElementById('userList'),
        userCount: document.getElementById('userCount'),
        dropZone: document.getElementById('dropZone'),
        dropZoneText: document.getElementById('dropZone')?.querySelector('p') || null,
        fileInput: document.getElementById('fileInput'),
        messageInput: document.getElementById('messageInput'),
        sendMessageBtn: document.getElementById('sendMessageBtn'),
        successToast: document.getElementById('successToast'),
        modeToggle: document.getElementById('modeToggle'),
        modeStatus: document.getElementById('modeStatus')
    };

    const collections = {
        peers: new Map(),
        incomingFiles: new Map(),
        channelWaiters: new Map(),
        reconnectAttempts: new Map(),
        reconnectTimers: new Map()
    };

    const constants = {
        configuration: { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] },
        CHUNK_SIZE: 16000,
        DATA_CHANNEL_MAX_BUFFER: 4 * 1024 * 1024,
        DATA_CHANNEL_LOW_WATERMARK: 512 * 1024,
        BUFFER_CHECK_INTERVAL: 200,
        MAX_RECONNECT_ATTEMPTS: 5,
        RECONNECT_BASE_DELAY: 250,
        CHANNEL_RETRY_LIMIT: 3,
        CHANNEL_WAIT_TIMEOUT: 5000,
        PEER_RECONNECT_DELAY: 750
    };

    const toastInstance = dom.successToast
        ? new bootstrap.Toast(dom.successToast, { delay: 2500 })
        : null;

    window.RoomContext = {
        config,
        state,
        dom,
        collections,
        constants,
        toastInstance,
        socket: null
    };
})(window);
