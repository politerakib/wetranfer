const config = window.NSHARE_CONFIG || {};
const ICE_SERVERS = [
    { urls: 'stun:stun.l.google.com:19302' }
];

if (config.turn?.url) {
    ICE_SERVERS.push({
        urls: config.turn.url,
        username: config.turn.username,
        credential: config.turn.password
    });
}

const peers = new Map();
let socket = null;
let callbacks = {};
let localUid = null;
let roomId = null;

const CHUNK_SIZE = 64 * 1024;

function randomId() {
    return Math.random().toString(36).slice(2, 11);
}

function ensureSocket() {
    if (socket) {
        if (socket.connected) {
            socket.emit('join_room', { roomId, uid: localUid });
        } else {
            socket.connect();
        }
        return;
    }

    socket = io(config.websocket, {
        transports: ['websocket'],
        reconnectionAttempts: 5,
    });

    socket.on('connect', () => {
        socket.emit('join_room', { roomId, uid: localUid });
    });

    socket.on('reconnect', () => {
        socket.emit('join_room', { roomId, uid: localUid });
    });

    socket.on('user_joined', (payload) => handleSignal({ type: 'user_joined', ...payload }));
    socket.on('user_left', (payload) => handleSignal({ type: 'user_left', ...payload }));
    socket.on('signal', (payload) => handleSignal({ type: 'signal', ...payload }));
}

function handleSignal(payload) {
    const type = payload.type;
    const from = payload.from;

    switch (type) {
        case 'user_joined':
            if (from === localUid || payload.uid === localUid) {
                return;
            }
            callbacks.onPresence?.({ uid: payload.uid, status: 'online' });
            createPeer(payload.uid, shouldInitiate(localUid, payload.uid));
            break;
        case 'user_left':
            removePeer(payload.uid);
            callbacks.onPresence?.({ uid: payload.uid, status: 'offline' });
            break;
        case 'signal':
            if (!payload.from) {
                return;
            }
            processSignal(payload.from, payload);
            break;
        default:
            break;
    }
}

function shouldInitiate(local, remote) {
    return local > remote;
}

function createPeer(remoteUid, initiate = false) {
    if (peers.has(remoteUid)) {
        return peers.get(remoteUid);
    }

    const pc = new RTCPeerConnection({ iceServers: ICE_SERVERS });
    const state = {
        pc,
        uid: remoteUid,
        channel: null,
        makingOffer: false,
        ignoreOffer: false,
        isPolite: !shouldInitiate(localUid, remoteUid),
        buffer: new Map(),
    };

    peers.set(remoteUid, state);

    pc.onicecandidate = ({ candidate }) => {
        if (candidate) {
            sendSignal(remoteUid, { candidate });
        }
    };

    pc.onconnectionstatechange = () => {
        const status = pc.connectionState;
        if (status === 'disconnected' || status === 'failed') {
            removePeer(remoteUid);
            callbacks.onPresence?.({ uid: remoteUid, status: 'offline' });
        }
    };

    pc.ondatachannel = (event) => {
        setupChannel(remoteUid, event.channel);
    };

    pc.onnegotiationneeded = async () => {
        try {
            state.makingOffer = true;
            const offer = await pc.createOffer();
            await pc.setLocalDescription(offer);
            sendSignal(remoteUid, { description: pc.localDescription });
        } catch (error) {
            console.error('Negotiation error', error);
        } finally {
            state.makingOffer = false;
        }
    };

    if (initiate) {
        const channel = pc.createDataChannel('nshare');
        setupChannel(remoteUid, channel);
    }

    return state;
}

function setupChannel(remoteUid, channel) {
    const peer = peers.get(remoteUid);
    if (!peer) {
        return;
    }

    peer.channel = channel;
    channel.binaryType = 'arraybuffer';

    channel.onopen = () => {
        callbacks.onPresence?.({ uid: remoteUid, status: 'online' });
    };

    channel.onclose = () => {
        callbacks.onPresence?.({ uid: remoteUid, status: 'offline' });
    };

    channel.onmessage = (event) => {
        handleChannelMessage(remoteUid, event.data);
    };
}

function handleChannelMessage(remoteUid, data) {
    try {
        const payload = JSON.parse(typeof data === 'string' ? data : new TextDecoder().decode(data));
        switch (payload.type) {
            case 'file-meta':
                callbacks.onFileMeta?.({ ...payload, from: remoteUid });
                break;
            case 'file-chunk':
                callbacks.onFileChunk?.({ ...payload, from: remoteUid });
                break;
            case 'file-complete':
                callbacks.onFileComplete?.({ ...payload, from: remoteUid });
                break;
            default:
                break;
        }
    } catch (error) {
        console.error('Failed to decode channel payload', error);
    }
}

function sendSignal(target, payload) {
    if (!socket || !socket.connected) {
        return;
    }

    socket.emit('signal', {
        target,
        ...payload,
    });
}

async function processSignal(from, payload) {
    const peer = createPeer(from, shouldInitiate(localUid, from));
    if (!peer) {
        return;
    }

    const pc = peer.pc;

    if (payload.description) {
        const description = payload.description;
        const readyForOffer = !peer.makingOffer && (pc.signalingState === 'stable' || peer.isPolite);
        const offerCollision = description.type === 'offer' && !readyForOffer;

        peer.ignoreOffer = !peer.isPolite && offerCollision;
        if (peer.ignoreOffer) {
            return;
        }

        try {
            await pc.setRemoteDescription(description);
            if (description.type === 'offer') {
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
                sendSignal(from, { description: pc.localDescription });
            }
        } catch (error) {
            console.error('Error handling description', error);
        }
    } else if (payload.candidate) {
        try {
            await pc.addIceCandidate(payload.candidate);
        } catch (error) {
            console.error('Error adding ICE candidate', error);
        }
    }
}

export function initWebRTC(options) {
    callbacks = options;
    roomId = options.roomId;
    localUid = options.uid;
    ensureSocket();
}

export function disconnectPeers() {
    peers.forEach((peer) => {
        peer.channel?.close();
        peer.pc.close();
    });
    peers.clear();
    if (socket?.connected) {
        socket.emit('leave_room');
    }
}

function removePeer(uid) {
    const peer = peers.get(uid);
    if (!peer) {
        return;
    }
    peer.channel?.close();
    peer.pc.close();
    peers.delete(uid);
}

function broadcast(message) {
    const encoded = JSON.stringify(message);
    peers.forEach((peer) => {
        if (peer.channel?.readyState === 'open') {
            peer.channel.send(encoded);
        }
    });
}

export function connectToMembers(uids = []) {
    uids.forEach((uid) => {
        if (uid === localUid) {
            return;
        }
        createPeer(uid, shouldInitiate(localUid, uid));
    });
}

export async function sendFile(file, progressCallback) {
    const fileId = `${Date.now()}-${randomId()}`;
    const totalChunks = Math.max(1, Math.ceil(file.size / CHUNK_SIZE));

    broadcast({
        type: 'file-meta',
        fileId,
        name: file.name,
        size: file.size,
        mime: file.type || 'application/octet-stream',
        totalChunks,
    });

    const reader = file.stream().getReader();
    let sentChunks = 0;

    while (true) {
        const { done, value } = await reader.read();
        if (done) {
            break;
        }
        const chunkArray = Array.from(value);
        sentChunks += 1;
        broadcast({
            type: 'file-chunk',
            fileId,
            chunk: chunkArray,
            index: sentChunks,
            totalChunks,
        });
        progressCallback?.(fileId, sentChunks / totalChunks, { name: file.name, size: file.size });
    }

    broadcast({ type: 'file-complete', fileId });
    progressCallback?.(fileId, 1, { name: file.name, size: file.size });
    return fileId;
}

export function getPeers() {
    return Array.from(peers.keys());
}
