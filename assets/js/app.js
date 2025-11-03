const API_BASE = window.APP_CONFIG?.apiBase || 'http://localhost:3000';

const sendBtn = document.getElementById('btnSend');
const receiveBtn = document.getElementById('btnReceive');
const teamBtn = document.getElementById('btnTeam');
const roomInputWrapper = document.getElementById('roomInputWrapper');
const joinForm = document.getElementById('joinForm');
const themeToggle = document.getElementById('themeToggle');

async function createRoom(roomType = 'direct') {
    const response = await fetch(`${API_BASE}/api/rooms`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ roomType })
    });

    if (!response.ok) {
        throw new Error('Unable to create room');
    }

    return response.json();
}

function redirectToRoom(roomId) {
    window.location.href = `room.php?room=${encodeURIComponent(roomId)}`;
}

sendBtn?.addEventListener('click', async () => {
    try {
        sendBtn.setAttribute('disabled', 'disabled');
        const data = await createRoom('direct');
        redirectToRoom(data.roomId);
    } catch (error) {
        console.error(error);
        alert('Failed to create room. Please try again.');
    } finally {
        sendBtn?.removeAttribute('disabled');
    }
});

teamBtn?.addEventListener('click', async () => {
    try {
        teamBtn.setAttribute('disabled', 'disabled');
        const data = await createRoom('team');
        redirectToRoom(data.roomId);
    } catch (error) {
        console.error(error);
        alert('Failed to create team room.');
    } finally {
        teamBtn?.removeAttribute('disabled');
    }
});

receiveBtn?.addEventListener('click', () => {
    roomInputWrapper?.classList.toggle('d-none');
    roomInputWrapper?.classList.toggle('d-flex');
});

joinForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const roomId = event.target.room_id.value.trim();
    if (!roomId) {
        return;
    }
    redirectToRoom(roomId);
});

function initThemeToggle() {
    if (!themeToggle) return;
    themeToggle.addEventListener('click', () => {
        const isDark = document.documentElement.dataset.bsTheme === 'dark';
        document.documentElement.dataset.bsTheme = isDark ? 'light' : 'dark';
        localStorage.setItem('preferredTheme', document.documentElement.dataset.bsTheme);
    });

    const storedTheme = localStorage.getItem('preferredTheme');
    if (storedTheme) {
        document.documentElement.dataset.bsTheme = storedTheme;
    }
}

initThemeToggle();
