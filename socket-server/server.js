import http from 'http';
import { Server } from 'socket.io';

const port = process.env.SOCKET_PORT || 8080;

const httpServer = http.createServer();
const io = new Server(httpServer, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  }
});

function broadcastRoom(socket, event, payload) {
  if (!socket.data.roomId) {
    return;
  }
  socket.to(socket.data.roomId).emit(event, payload);
}

function sendToTarget(roomId, targetUid, event, payload) {
  for (const [, client] of io.sockets.sockets) {
    if (client.data.roomId === roomId && client.data.uid === targetUid) {
      client.emit(event, payload);
    }
  }
}

io.on('connection', (socket) => {
  socket.data = { uid: null, roomId: null };

  socket.on('join_room', ({ roomId, uid }) => {
    if (!roomId || !uid) {
      return;
    }

    if (socket.data.roomId) {
      socket.leave(socket.data.roomId);
    }

    socket.join(roomId);
    socket.data.roomId = roomId;
    socket.data.uid = uid;

    broadcastRoom(socket, 'user_joined', { type: 'user_joined', uid });
  });

  socket.on('leave_room', () => {
    if (!socket.data.roomId) {
      return;
    }

    const { roomId, uid } = socket.data;
    socket.leave(roomId);
    socket.data.roomId = null;
    broadcastRoom(socket, 'user_left', { type: 'user_left', uid });
  });

  socket.on('signal', (payload) => {
    if (!socket.data.roomId || !socket.data.uid) {
      return;
    }

    const message = { ...payload, type: 'signal', from: socket.data.uid };
    if (payload?.target) {
      sendToTarget(socket.data.roomId, payload.target, 'signal', message);
    } else {
      broadcastRoom(socket, 'signal', message);
    }
  });

  for (const event of ['file_offer', 'file_chunk', 'file_complete']) {
    socket.on(event, (payload) => {
      if (!socket.data.roomId || !socket.data.uid) {
        return;
      }

      const message = { ...payload, type: event, from: socket.data.uid };
      if (payload?.target) {
        sendToTarget(socket.data.roomId, payload.target, event, message);
      } else {
        broadcastRoom(socket, event, message);
      }
    });
  }

  socket.on('disconnect', () => {
    if (socket.data.roomId && socket.data.uid) {
      broadcastRoom(socket, 'user_left', { type: 'user_left', uid: socket.data.uid });
    }
  });
});

httpServer.listen(port, () => {
  console.log(`Socket server listening on port ${port}`);
});
