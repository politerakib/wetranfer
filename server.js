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
            type: 'direct'
        });
    }
    return rooms.get(roomId);
}

function serializeUsers(room) {
    return Array.from(room.users.values()).map((user) => ({
        userId: user.userId,
        displayName: user.displayName,
        status: 'online'
    }));
}

function findSocketId(room, userId) {
    const entry = Array.from(room.users.values()).find((user) => user.userId === userId);
    return entry ? entry.socketId : null;
}

app.post('/api/rooms', (req, res) => {
    const { roomType = 'direct' } = req.body || {};
    const roomId = generateRoomId(roomType);
    const room = getRoom(roomId);
    room.type = roomType;
    res.json({ roomId, roomType });
});

io.on('connection', (socket) => {
    socket.on('join-room', ({ roomId, userId, displayName }) => {
        if (!roomId || !userId) {
            return;
        }

        const room = getRoom(roomId);
        socket.join(roomId);
        socket.data.roomId = roomId;
        socket.data.userId = userId;
        socket.data.displayName = displayName;

        room.users.set(socket.id, {
            socketId: socket.id,
            userId,
            displayName
        });

        io.to(roomId).emit('room-users', serializeUsers(room));
        socket.to(roomId).emit('user-joined', { userId, displayName });
    });

    socket.on('offer', ({ roomId, target, offer }) => {
        if (!roomId || !target || !offer) return;
        const room = getRoom(roomId);
        const targetSocketId = findSocketId(room, target);
        if (targetSocketId) {
            io.to(targetSocketId).emit('offer', { from: socket.data.userId, offer });
        }
    });

    socket.on('answer', ({ roomId, target, answer }) => {
        if (!roomId || !target || !answer) return;
        const room = getRoom(roomId);
        const targetSocketId = findSocketId(room, target);
        if (targetSocketId) {
            io.to(targetSocketId).emit('answer', { from: socket.data.userId, answer });
        }
    });

    socket.on('ice-candidate', ({ roomId, target, candidate }) => {
        if (!roomId || !target || !candidate) return;
        const room = getRoom(roomId);
        const targetSocketId = findSocketId(room, target);
        if (targetSocketId) {
            io.to(targetSocketId).emit('ice-candidate', { from: socket.data.userId, candidate });
        }
    });

    socket.on('disconnect', () => {
        const { roomId, userId, displayName } = socket.data || {};
        if (!roomId) return;
        const room = getRoom(roomId);
        room.users.delete(socket.id);
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
