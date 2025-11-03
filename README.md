# N-Share

N-Share is a modern WebRTC-based file sharing platform that allows you to transfer files directly between browsers either person-to-person or inside collaborative team rooms. The project includes a PHP + MySQL backend for authentication, room management, and metadata tracking alongside a TailwindCSS-inspired frontend experience.

## Features

- **Google Authentication** with remember-me sessions powered by Firebase Identity.
- **Person-to-Person** rooms that auto-generate secure IDs and enforce a single active room per user.
- **Team Rooms** using WebRTC data channels and a Socket.IO signaling service running on Node.js.
- **Drag-and-Drop** uploads with local cache protection, live progress bars, and resumable UI states.
- **Active Presence** sidebar with avatars, join/leave events, and hero quick-glance badges.
- **TURN/STUN Fallback** configuration for reliable peer connectivity behind restrictive networks.
- **Responsive UI** with light/dark themes, hero marketing sections, pricing, and testimonials.

## Project Structure

```
backend/
  api/              # REST endpoints (auth, rooms, uploads)
  includes/         # Database, auth, storage, and helper functions
public/
  assets/css        # Custom styles that complement Tailwind
  assets/js         # Front-end controllers and WebRTC helpers
  index.php         # Application shell and marketing site
socket-server/      # Node.js Socket.IO signaling server
storage/uploads     # Uploaded file storage location
```

## Getting Started

1. **Install dependencies**
   ```bash
   cd socket-server
   npm install
   cd ..
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your MySQL credentials, Firebase project details, and TURN server parameters.

3. **Create the database**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

4. **Start the backend services**
   - PHP server (example):
     ```bash
     php -S localhost:8000 -t public
     ```
   - Socket.IO signaling server:
     ```bash
     cd socket-server
     npm start
     ```
     (set `SOCKET_PORT` in `.env` if you need a custom port)

5. **Access the app**
   Visit [http://localhost:8000](http://localhost:8000) to use N-Share. Sign in with Google, create or join a room, and start sharing files instantly.

## Notes

- The signaling server uses Socket.IO; ensure the port defined by `SOCKET_PORT` is accessible.
- File transfers are handled entirely via WebRTC data channels—uploaded files never leave the browser unless you also enable the optional REST upload endpoint for persistence.
- Local storage keeps track of outgoing files to prevent accidental loss if the page refreshes mid-transfer.

## Development Scripts

- `npm install --prefix socket-server` — install signaling server dependencies
- `npm start --prefix socket-server` — run signaling server
- `php -S localhost:8000 -t public` — serve the front-end

Enjoy instant, private, cross-platform file sharing! 🚀
