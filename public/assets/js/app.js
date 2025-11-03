import { loginWithGoogle, getCurrentUser, onAuthChange } from './auth.js';
import { initWebRTC, sendFile, disconnectPeers, connectToMembers } from './webrtc.js';

const config = window.NSHARE_CONFIG || {};
const state = {
    room: null,
    uid: config.user?.uid || null,
    members: new Map(),
    transfers: new Map(),
    cache: loadCache(),
    pollTimer: null,
};

const elements = {
    themeToggle: document.getElementById('themeToggle'),
    themeIcon: document.getElementById('themeIcon'),
    roomModal: document.getElementById('roomModal'),
    roomModalTitle: document.getElementById('roomModalTitle'),
    roomModalDescription: document.getElementById('roomModalDescription'),
    roomForm: document.getElementById('roomForm'),
    roomTitleField: document.getElementById('roomTitleField'),
    roomIdField: document.querySelector('#roomForm input[name="roomId"]'),
    rememberCheckbox: document.querySelector('#roomForm input[name="remember"]'),
    roomActionButton: document.getElementById('roomActionButton'),
    closeRoomModal: document.getElementById('closeRoomModal'),
    sendFile: document.getElementById('btnSendFile'),
    receiveFile: document.getElementById('btnReceiveFile'),
    teamShare: document.getElementById('btnTeamShare'),
    ctaStart: document.getElementById('ctaStart'),
    roomInterface: document.getElementById('roomInterface'),
    roomName: document.getElementById('roomName'),
    roomIdDisplay: document.getElementById('roomIdDisplay'),
    feed: document.getElementById('feed'),
    userList: document.getElementById('userList'),
    sidebarActiveCount: document.getElementById('sidebarActiveCount'),
    heroActiveCount: document.getElementById('heroActiveCount'),
    heroActiveUsers: document.getElementById('heroActiveUsers'),
    dropZone: document.getElementById('dropZone'),
    filePicker: document.getElementById('filePicker'),
    browseFiles: document.getElementById('browseFiles'),
    leaveRoom: document.getElementById('leaveRoom'),
    copyRoomId: document.getElementById('copyRoomId'),
};

const roomModalModes = {
    CREATE_P2P: 'create-p2p',
    CREATE_TEAM: 'create-team',
    JOIN: 'join',
};

setupTheme();
setupEventListeners();
restoreCachedTransfers();
checkExistingRoom();

function loadCache() {
    try {
        const raw = localStorage.getItem('nshare-cache');
        return raw ? JSON.parse(raw) : [];
    } catch (error) {
        console.warn('Failed to parse cache', error);
        return [];
    }
}

function persistCache() {
    localStorage.setItem('nshare-cache', JSON.stringify(state.cache));
}

function restoreCachedTransfers() {
    if (!state.cache.length || !elements.feed) {
        return;
    }
    state.cache.forEach((entry) => {
        const card = renderFileCard({
            fileId: entry.id,
            name: entry.name,
            size: entry.size,
            status: 'pending',
            direction: 'outbound',
        });
        card.querySelector('.progress-bar').style.width = `${Math.min(95, entry.progress || 0)}%`;
    });
}

async function checkExistingRoom() {
    try {
        const response = await fetch(`${config.apiBase}/rooms/validate.php`);
        if (!response.ok) {
            return;
        }
        const payload = await response.json();
        if (payload.room) {
            enterRoom(payload.room, payload.uid);
        }
    } catch (error) {
        console.warn('Unable to restore active room', error);
    }
}

function setupTheme() {
    if (!elements.themeToggle || !elements.themeIcon) {
        return;
    }

    const stored = localStorage.getItem('theme');
    if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        elements.themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />';
    }

    elements.themeToggle.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        const isDark = document.documentElement.classList.contains('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        elements.themeIcon.innerHTML = isDark
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5M17.657 6.343l-1.06 1.06M21 12h-1.5M17.657 17.657l-1.06-1.06M12 19.5V21M7.403 16.596l-1.06 1.06M4.5 12H3M7.403 7.403l-1.06-1.06M12 6.75a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5z" />';
    });
}

function setupEventListeners() {
    elements.sendFile?.addEventListener('click', () => openRoomModal(roomModalModes.CREATE_P2P));
    elements.receiveFile?.addEventListener('click', () => openRoomModal(roomModalModes.JOIN));
    elements.teamShare?.addEventListener('click', () => openRoomModal(roomModalModes.CREATE_TEAM));
    elements.ctaStart?.addEventListener('click', () => openRoomModal(roomModalModes.CREATE_P2P));
    elements.closeRoomModal?.addEventListener('click', closeRoomModal);

    if (elements.roomForm) {
        elements.roomForm.addEventListener('submit', handleRoomSubmit);
    }

    if (elements.leaveRoom) {
        elements.leaveRoom.addEventListener('click', leaveActiveRoom);
    }

    if (elements.copyRoomId) {
        elements.copyRoomId.addEventListener('click', async () => {
            if (!state.room) return;
            await navigator.clipboard.writeText(state.room.room_id);
            elements.copyRoomId.textContent = 'Copied!';
            setTimeout(() => (elements.copyRoomId.textContent = 'Copy ID'), 2000);
        });
    }

    if (elements.dropZone) {
        ['dragenter', 'dragover'].forEach((eventName) => {
            elements.dropZone.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'copy';
                elements.dropZone.classList.add('ring-2', 'ring-brand-500/40');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            elements.dropZone.addEventListener(eventName, (event) => {
                event.preventDefault();
                elements.dropZone.classList.remove('ring-2', 'ring-brand-500/40');
            });
        });

        elements.dropZone.addEventListener('drop', (event) => {
            if (!state.room) {
                alert('Join or create a room to share files.');
                return;
            }
            const files = event.dataTransfer.files;
            if (files?.length) {
                handleSelectedFiles(files);
            }
        });
    }

    elements.browseFiles?.addEventListener('click', () => elements.filePicker?.click());

    elements.filePicker?.addEventListener('change', () => {
        if (!state.room) {
            alert('Join or create a room to share files.');
            elements.filePicker.value = '';
            return;
        }
        handleSelectedFiles(elements.filePicker.files);
        elements.filePicker.value = '';
    });

    onAuthChange((user) => {
        state.uid = user?.uid || state.uid;
    });
}

function openRoomModal(mode) {
    if (!elements.roomModal) {
        return;
    }

    elements.roomModal.classList.remove('hidden');
    elements.roomForm.dataset.mode = mode;

    if (mode === roomModalModes.CREATE_P2P) {
        elements.roomModalTitle.textContent = 'Start a direct share';
        elements.roomModalDescription.textContent = 'We will generate a unique room ID and connect you to the next participant.';
        elements.roomTitleField.classList.add('hidden');
        elements.roomIdField.value = 'Auto-generated';
        elements.roomIdField.readOnly = true;
        elements.roomActionButton.textContent = 'Create room';
    } else if (mode === roomModalModes.CREATE_TEAM) {
        if (!getCurrentUser()) {
            alert('Sign in with Google to create a team room.');
            closeRoomModal();
            loginWithGoogle(true).catch(() => {});
            return;
        }
        elements.roomModalTitle.textContent = 'Create a team room';
        elements.roomModalDescription.textContent = 'Invite teammates to a collaborative file-sharing space.';
        elements.roomTitleField.classList.remove('hidden');
        elements.roomIdField.value = 'Auto-generated';
        elements.roomIdField.readOnly = true;
        elements.roomActionButton.textContent = 'Create team room';
    } else {
        elements.roomModalTitle.textContent = 'Join an existing room';
        elements.roomModalDescription.textContent = 'Paste the room ID you received from a teammate to hop in.';
        elements.roomTitleField.classList.add('hidden');
        elements.roomIdField.value = '';
        elements.roomIdField.readOnly = false;
        elements.roomActionButton.textContent = 'Join room';
    }
}

function closeRoomModal() {
    elements.roomModal?.classList.add('hidden');
}

async function handleRoomSubmit(event) {
    event.preventDefault();
    const mode = event.target.dataset.mode;
    const remember = elements.rememberCheckbox?.checked ?? false;

    if (mode === roomModalModes.JOIN) {
        const roomId = elements.roomIdField.value.trim();
        if (!roomId) {
            alert('Enter a room ID to join.');
            return;
        }
        await joinRoom(roomId);
    } else if (mode === roomModalModes.CREATE_P2P) {
        await createRoom('p2p');
    } else if (mode === roomModalModes.CREATE_TEAM) {
        await createRoom('team');
    }

    if (mode === roomModalModes.CREATE_TEAM && remember && !getCurrentUser()) {
        try {
            await loginWithGoogle(true);
        } catch (error) {
            console.warn('Remember me login failed', error);
        }
    }
}

async function createRoom(type) {
    try {
        const response = await fetch(`${config.apiBase}/rooms/create.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type }),
        });
        if (!response.ok) {
            throw new Error('Unable to create room');
        }
        const payload = await response.json();
        enterRoom(payload.room, payload.uid);
        closeRoomModal();
    } catch (error) {
        console.error(error);
        alert('Unable to create room. Check console for details.');
    }
}

async function joinRoom(roomId) {
    try {
        const response = await fetch(`${config.apiBase}/rooms/join.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ roomId }),
        });
        if (!response.ok) {
            throw new Error('Unable to join room');
        }
        const payload = await response.json();
        enterRoom(payload.room, payload.uid);
        closeRoomModal();
    } catch (error) {
        console.error(error);
        alert('Unable to join room. Check console for details.');
    }
}

async function leaveActiveRoom() {
    if (!state.room) {
        return;
    }

    try {
        await fetch(`${config.apiBase}/rooms/leave.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ roomId: state.room.room_id }),
        });
    } catch (error) {
        console.warn('Failed to notify server of room leave', error);
    }

    disconnectPeers();
    clearInterval(state.pollTimer);
    state.room = null;
    state.members.clear();
    state.transfers.clear();
    elements.feed.innerHTML = '';
    elements.userList.innerHTML = '';
    elements.roomInterface.classList.add('hidden');
}

function enterRoom(room, uid) {
    disconnectPeers();
    state.room = room;
    state.uid = uid;
    elements.roomInterface.classList.remove('hidden');
    elements.roomName.textContent = room.title || (room.type === 'team' ? 'Team Room' : 'Direct Share');
    elements.roomIdDisplay.textContent = room.room_id;
    state.members = new Map(
        room.members.map((member) => [member.user_uid, { ...member, status: member.left_at ? 'offline' : 'online' }])
    );
    renderUserList();

    initWebRTC({
        roomId: room.room_id,
        uid,
        onPresence: handlePresenceEvent,
        onFileMeta: handleIncomingMeta,
        onFileChunk: handleIncomingChunk,
        onFileComplete: handleIncomingComplete,
    });

    connectToMembers(room.members.map((member) => member.user_uid));

    if (state.pollTimer) {
        clearInterval(state.pollTimer);
    }
    state.pollTimer = setInterval(fetchActiveMembers, 8000);
    fetchActiveMembers();
}

async function fetchActiveMembers() {
    if (!state.room) {
        return;
    }
    try {
        const response = await fetch(`${config.apiBase}/rooms/active.php?roomId=${state.room.room_id}`);
        if (!response.ok) {
            return;
        }
        const payload = await response.json();
        payload.members.forEach((member) => {
            state.members.set(member.user_uid, { ...member, status: 'online' });
        });
        renderUserList();
    } catch (error) {
        console.warn('Unable to refresh members', error);
    }
}

function handlePresenceEvent(event) {
    if (!state.members.has(event.uid)) {
        state.members.set(event.uid, {
            user_uid: event.uid,
            name: `Guest ${event.uid.slice(-4)}`,
            avatar_url: null,
        });
    }
    const member = state.members.get(event.uid);
    member.status = event.status;
    renderUserList();
}

function renderUserList() {
    if (!elements.userList) {
        return;
    }
    elements.userList.innerHTML = '';
    const members = Array.from(state.members.values());
    elements.sidebarActiveCount.textContent = members.length;
    elements.heroActiveCount.textContent = `${members.length} online`;
    elements.heroActiveUsers.innerHTML = '';

    members.forEach((member) => {
        const pill = document.createElement('div');
        pill.className = 'user-pill';
        const avatar = document.createElement('div');
        avatar.className = 'avatar';
        avatar.textContent = (member.name || member.user_uid || 'User').slice(0, 2).toUpperCase();
        const info = document.createElement('div');
        info.className = 'flex-1';
        const name = document.createElement('p');
        name.className = 'text-sm font-semibold';
        name.textContent = member.user_uid === state.uid ? `${member.name || 'You'} (You)` : member.name || member.user_uid;
        const status = document.createElement('span');
        status.className = `status ${member.status === 'offline' ? 'offline' : 'online'}`;
        info.appendChild(name);
        pill.append(avatar, info, status);
        elements.userList.appendChild(pill);

        const heroBadge = document.createElement('span');
        heroBadge.className = 'px-3 py-1 rounded-full bg-white/10 border border-white/30 text-xs backdrop-blur';
        heroBadge.textContent = member.name || member.user_uid;
        elements.heroActiveUsers.appendChild(heroBadge);
    });
}

function handleSelectedFiles(fileList) {
    Array.from(fileList).forEach((file) => {
        queueOutgoingFile(file);
    });
}

async function queueOutgoingFile(file) {
    const card = renderFileCard({
        name: file.name,
        size: file.size,
        status: 'sending',
        direction: 'outbound',
    });

    const cacheEntry = { id: `${Date.now()}-pending`, name: file.name, size: file.size, progress: 0 };
    state.cache.push(cacheEntry);
    persistCache();

    try {
        const fileId = await sendFile(file, (id, progress) => {
            cacheEntry.progress = progress * 100;
            cacheEntry.id = id;
            persistCache();
            if (!card.dataset.fileId) {
                card.dataset.fileId = id;
            }
            updateProgress(id, progress);
        });
        card.dataset.fileId = fileId;
        cacheEntry.id = fileId;
        cacheEntry.progress = 100;
        persistCache();
        state.transfers.set(fileId, { card, name: file.name, size: file.size, sent: true });
        state.cache = state.cache.filter((entry) => entry.id !== cacheEntry.id);
        persistCache();
    } catch (error) {
        console.error('Failed to send file', error);
        card.querySelector('.progress-bar').style.background = 'rgb(239, 68, 68)';
    }
}

function renderFileCard({ fileId, name, size, status, direction }) {
    const wrapper = document.createElement('article');
    wrapper.className = 'file-card';
    if (fileId) {
        wrapper.dataset.fileId = fileId;
    }

    const title = document.createElement('h4');
    title.textContent = name;
    const meta = document.createElement('p');
    meta.textContent = `${formatSize(size)} • ${direction === 'outbound' ? 'Sending' : 'Receiving'}`;

    const progressTrack = document.createElement('div');
    progressTrack.className = 'progress-track';
    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar';
    progressTrack.appendChild(progressBar);

    wrapper.append(title, meta, progressTrack);
    elements.feed?.appendChild(wrapper);
    elements.feed?.scrollTo({ top: elements.feed.scrollHeight, behavior: 'smooth' });

    return wrapper;
}

function updateProgress(fileId, progress) {
    const card = elements.feed?.querySelector(`[data-file-id="${fileId}"]`);
    if (!card) {
        return;
    }
    const bar = card.querySelector('.progress-bar');
    bar.style.width = `${Math.min(100, progress * 100)}%`;
}

function handleIncomingMeta(meta) {
    const card = renderFileCard({
        fileId: meta.fileId,
        name: meta.name,
        size: meta.size,
        status: 'receiving',
        direction: 'inbound',
    });
    state.transfers.set(meta.fileId, {
        card,
        name: meta.name,
        size: meta.size,
        buffers: [],
        mime: meta.mime,
        totalChunks: meta.totalChunks,
    });
}

function handleIncomingChunk(chunk) {
    const transfer = state.transfers.get(chunk.fileId);
    if (!transfer) {
        return;
    }
    transfer.buffers.push(Uint8Array.from(chunk.chunk));
    const progress = chunk.index / (transfer.totalChunks || chunk.totalChunks || 1);
    updateProgress(chunk.fileId, progress);
}

function handleIncomingComplete(data) {
    const transfer = state.transfers.get(data.fileId);
    if (!transfer) {
        return;
    }
    const blob = new Blob(transfer.buffers, { type: transfer.mime || 'application/octet-stream' });
    const url = URL.createObjectURL(blob);
    const actions = document.createElement('div');
    actions.className = 'mt-3 flex items-center gap-3 text-xs';
    const download = document.createElement('a');
    download.href = url;
    download.download = transfer.name;
    download.textContent = 'Download';
    download.className = 'rounded-full bg-brand-500/10 px-4 py-1 font-semibold text-brand-600';
    actions.appendChild(download);
    transfer.card.appendChild(actions);
    updateProgress(data.fileId, 1);
    state.transfers.delete(data.fileId);
}

function formatSize(bytes) {
    if (!bytes && bytes !== 0) return '';
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let index = 0;
    while (size >= 1024 && index < units.length - 1) {
        size /= 1024;
        index += 1;
    }
    return `${size.toFixed(1)} ${units[index]}`;
}
