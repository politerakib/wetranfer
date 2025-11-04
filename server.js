const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const crypto = require('crypto');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});

app.use(cors());
app.use(express.json());

const rooms = new Map();

function generateRoomId(roomType = 'direct') {
    const suffix = crypto.randomBytes(4).toString('hex');
    const prefix = roomType === 'team' ? 'team-' : 'room-';
    return `${prefix}${suffix}`;
}

function getRoom(roomId) {
    if (!rooms.has(roomId)) {
        rooms.set(roomId, {
            users: new Map(),
            roster: new Map(),
            type: 'direct',
            transferMode: 'webrtc'
        });
    }
    return rooms.get(roomId);
}

function serializeUsers(room) {
    const roster = Array.from(room.roster.values());
    roster.sort((a, b) => {
        if (a.status === b.status) {
            return a.displayName.localeCompare(b.displayName);
        }
        return a.status === 'online' ? -1 : 1;
    });
    return roster;
}

app.post('/api/rooms', (req, res) => {
    const { roomType = 'direct', transferMode } = req.body || {};
    const roomId = generateRoomId(roomType);
    const room = getRoom(roomId);
    room.type = roomType;
    room.transferMode = roomType === 'team' ? 'store' : (transferMode === 'store' ? 'store' : 'webrtc');
    res.json({ roomId, roomType, transferMode: room.transferMode });
});

app.post('/api/rooms/:roomId/mode', (req, res) => {
    const { roomId } = req.params;
    const { transferMode } = req.body || {};
    if (!roomId || !transferMode) {
        return res.status(422).json({ error: 'Missing parameters' });
    }

    const normalized = transferMode === 'store' ? 'store' : 'webrtc';
    const room = getRoom(roomId);
    room.transferMode = normalized;
    io.to(roomId).emit('room-info', { roomType: room.type, transferMode: room.transferMode });
    res.json({ roomId, roomType: room.type, transferMode: room.transferMode });
});

io.on('connection', (socket) => {
    socket.on('join-room', ({ roomId, userId, displayName }) => {
        if (!roomId || !userId) {
            return;
        }

        const room = getRoom(roomId);
        const onlineUserIds = new Set(Array.from(room.users.values()).map((user) => user.userId));
        const alreadyOnline = onlineUserIds.has(userId);

        if (!alreadyOnline && room.type === 'direct' && onlineUserIds.size >= 2) {
            socket.emit('room-full');
            return;
        }

        socket.join(roomId);
        socket.data.roomId = roomId;
        socket.data.userId = userId;
        socket.data.displayName = displayName;

        room.users.set(socket.id, {
            socketId: socket.id,
            userId,
            displayName
        });

        room.roster.set(userId, {
            userId,
            displayName,
            status: 'online'
        });

        socket.emit('room-info', { roomType: room.type, transferMode: room.transferMode });
        io.to(roomId).emit('room-users', serializeUsers(room));
        socket.to(roomId).emit('user-joined', { userId, displayName });
    });

    socket.on('room-message', ({ roomId, message }) => {
        if (!roomId || !message) return;
        socket.to(roomId).emit('room-message', { message });
    });

    socket.on('disconnect', () => {
        const { roomId, userId, displayName } = socket.data || {};
        if (!roomId) return;
        const room = getRoom(roomId);
        room.users.delete(socket.id);
        const rosterEntry = room.roster.get(userId);
        if (rosterEntry) {
            rosterEntry.status = 'offline';
            room.roster.set(userId, rosterEntry);
        }
        socket.to(roomId).emit('user-left', { userId, displayName });
        io.to(roomId).emit('room-users', serializeUsers(room));

        if (room.users.size === 0 && room.type !== 'team') {
            rooms.delete(roomId);
        }
    });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`Realtime signaling server running on port ${PORT}`);
});
