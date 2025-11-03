import { initializeApp } from 'https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js';
import {
    browserLocalPersistence,
    browserSessionPersistence,
    getAuth,
    GoogleAuthProvider,
    onAuthStateChanged,
    setPersistence,
    signInWithPopup,
    signOut
} from 'https://www.gstatic.com/firebasejs/9.22.2/firebase-auth.js';

const config = window.NSHARE_CONFIG || {};
let firebaseApp = null;
let auth = null;
const provider = new GoogleAuthProvider();
const subscribers = new Set();
let currentUser = config.user || null;

function emit(user) {
    currentUser = user;
    subscribers.forEach((callback) => callback(user));
}

function initFirebase() {
    if (firebaseApp || !config.firebase?.apiKey) {
        return;
    }

    firebaseApp = initializeApp(config.firebase);
    auth = getAuth(firebaseApp);
    onAuthStateChanged(auth, (firebaseUser) => {
        if (!firebaseUser) {
            emit(null);
            return;
        }

        firebaseUser.getIdToken().then((token) => {
            persistSession(token, true);
        }).catch(() => emit(null));
    });
}

async function persistSession(idToken, remember) {
    const response = await fetch(`${config.apiBase}/auth/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idToken, remember })
    });

    if (!response.ok) {
        throw new Error('Unable to persist session');
    }

    const payload = await response.json();
    emit(payload.user);
    updateUi(payload.user);
}

function updateUi(user) {
    const loginButton = document.getElementById('loginButton');
    const profileDropdown = document.getElementById('profileDropdown');
    const profileName = document.getElementById('profileName');
    const profileAvatar = document.getElementById('profileAvatar');

    if (!loginButton || !profileDropdown) {
        return;
    }

    if (user) {
        loginButton.classList.add('hidden');
        profileDropdown.classList.remove('hidden');
        profileName.textContent = user.name || user.email;
        if (user.avatar) {
            profileAvatar.src = user.avatar;
        } else {
            profileAvatar.src = `https://api.dicebear.com/7.x/initials/svg?seed=${encodeURIComponent(user.name || user.email)}`;
        }
    } else {
        loginButton.classList.remove('hidden');
        profileDropdown.classList.add('hidden');
    }
}

async function signIn(remember = false) {
    initFirebase();
    if (!auth) {
        throw new Error('Firebase auth not initialised');
    }

    const persistence = remember ? browserLocalPersistence : browserSessionPersistence;
    await setPersistence(auth, persistence);

    const result = await signInWithPopup(auth, provider);
    const token = await result.user.getIdToken();
    await persistSession(token, remember);
}

async function logout() {
    if (auth) {
        await signOut(auth);
    }
    await fetch(`${config.apiBase}/auth/logout.php`, { method: 'POST' });
    emit(null);
    updateUi(null);
}

export function onAuthChange(callback) {
    subscribers.add(callback);
    callback(currentUser);
    return () => subscribers.delete(callback);
}

export function getCurrentUser() {
    return currentUser;
}

export async function loginWithGoogle(remember = false) {
    return signIn(remember);
}

export async function signOutUser() {
    return logout();
}

initFirebase();
updateUi(currentUser);

const loginButton = document.getElementById('loginButton');
const signOutButton = document.getElementById('signOutButton');
const profileButton = document.getElementById('profileButton');
const profileMenu = document.getElementById('profileMenu');
const profileDropdown = document.getElementById('profileDropdown');

if (loginButton) {
    loginButton.addEventListener('click', async () => {
        loginButton.disabled = true;
        try {
            await signIn(true);
        } catch (error) {
            console.error(error);
            alert('Login failed. Check console for details.');
        } finally {
            loginButton.disabled = false;
        }
    });
}

if (signOutButton) {
    signOutButton.addEventListener('click', async () => {
        await logout();
    });
}

if (profileButton && profileMenu && profileDropdown) {
    profileButton.addEventListener('click', () => {
        profileMenu.classList.toggle('hidden');
    });
    document.addEventListener('click', (event) => {
        if (!profileDropdown.contains(event.target)) {
            profileMenu.classList.add('hidden');
        }
    });
}

export async function fetchSession() {
    const response = await fetch(`${config.apiBase}/auth/me.php`);
    if (!response.ok) {
        emit(null);
        updateUi(null);
        return null;
    }

    const payload = await response.json();
    emit(payload.user);
    updateUi(payload.user);
    return payload.user;
}

fetchSession().catch(() => {});
