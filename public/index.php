<?php
require_once __DIR__ . '/../backend/includes/bootstrap.php';

$user = auth_user();
if (!$user) {
    $user = auth_remember_from_cookie();
}

$socketPort = env_get('SOCKET_PORT', '8080');
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$hostWithoutPort = preg_replace('/:\\d+$/', '', $host);
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
$protocol = $isSecure ? 'https' : 'http';
$websocketUrl = sprintf('%s://%s:%s', $protocol, $hostWithoutPort, $socketPort);
?>
<!DOCTYPE html>
<html lang="en" class="h-full" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>N-Share — Instant WebRTC File Sharing</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="h-full bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-50">
<div id="app" class="min-h-screen flex flex-col">
    <header class="sticky top-0 z-40 backdrop-blur bg-white/70 dark:bg-slate-950/70 border-b border-slate-200 dark:border-slate-800">
        <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 text-white font-semibold">N</span>
                <div>
                    <p class="text-lg font-semibold">N-Share</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Instant WebRTC file sharing</p>
                </div>
            </div>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300">
                <a href="#features" class="hover:text-brand-600">Features</a>
                <a href="#pricing" class="hover:text-brand-600">Pricing</a>
                <a href="#how-it-works" class="hover:text-brand-600">How It Works</a>
            </nav>
            <div class="flex items-center gap-3">
                <button id="themeToggle" class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-900" title="Toggle theme">
                    <span class="sr-only">Toggle theme</span>
                    <svg id="themeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </button>
                <div id="authArea" class="flex items-center gap-3">
                    <button id="loginButton" class="hidden rounded-lg border border-transparent bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">Login with Google</button>
                    <div id="profileDropdown" class="relative hidden">
                        <button id="profileButton" class="flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-2 py-1">
                            <img id="profileAvatar" src="" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
                            <span id="profileName" class="text-sm font-medium"></span>
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m6 9 6 6 6-6" /></svg>
                        </button>
                        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-lg py-2">
                            <a href="#profile" class="block px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">Profile</a>
                            <a href="#billing" class="block px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">Billing</a>
                            <a href="#settings" class="block px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">Settings</a>
                            <button id="signOutButton" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-950">Sign Out</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <section class="relative overflow-hidden bg-slate-950 text-white">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-500/20 via-brand-500/10 to-transparent"></div>
            <div class="relative mx-auto max-w-7xl px-6 py-24 lg:flex lg:items-center lg:gap-16">
                <div class="max-w-2xl">
                    <p class="text-sm uppercase tracking-wide text-brand-200">Send &amp; receive files instantly — no servers.</p>
                    <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Share any file in seconds with N-Share</h1>
                    <p class="mt-6 text-lg text-slate-200">Connect peer-to-peer or collaborate with your team using secure WebRTC connections. Drag, drop, and deliver files without waiting on uploads.</p>
                    <div class="mt-10 flex flex-wrap gap-3">
                        <button id="btnSendFile" class="rounded-lg bg-white px-5 py-3 text-base font-semibold text-slate-900 shadow-sm hover:bg-slate-100">Send File</button>
                        <button id="btnReceiveFile" class="rounded-lg border border-white/50 px-5 py-3 text-base font-semibold text-white hover:bg-white/10">Receive File</button>
                        <button id="btnTeamShare" class="rounded-lg border border-white/50 px-5 py-3 text-base font-semibold text-white hover:bg-white/10">Team Share</button>
                    </div>
                </div>
                <div class="mt-12 lg:mt-0 lg:flex-1">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                        <div class="grid gap-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold">Active Users</p>
                                <span id="heroActiveCount" class="text-xs uppercase tracking-wide text-brand-200">0 online</span>
                            </div>
                            <div id="heroActiveUsers" class="flex flex-wrap gap-2"></div>
                            <div class="rounded-2xl border border-white/10 bg-slate-900/40 p-4">
                                <p class="text-sm font-medium text-slate-200">Drag &amp; drop files to start sharing</p>
                                <p class="mt-2 text-xs text-slate-400">Files stay local until a peer connects. Encryption is built-in via WebRTC.</p>
                                <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-800">
                                    <div id="heroProgress" class="h-full w-0 rounded-full bg-brand-400 transition-all"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="mx-auto max-w-7xl px-6 py-24">
            <div class="grid gap-16 lg:grid-cols-2">
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight">How it works</h2>
                    <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">Create a secure room, invite peers, and drop your files. N-Share handles peer discovery, progress tracking, and resilient delivery.</p>
                    <dl class="mt-8 space-y-6">
                        <div>
                            <dt class="text-base font-semibold">1. Create or join a room</dt>
                            <dd class="mt-2 text-sm text-slate-600 dark:text-slate-400">Generate an instant P2P room or start a team workspace with your colleagues.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold">2. Drop your files</dt>
                            <dd class="mt-2 text-sm text-slate-600 dark:text-slate-400">Drag files onto the canvas or use the upload button. We support images, videos, documents, and archives.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold">3. Transfer in real time</dt>
                            <dd class="mt-2 text-sm text-slate-600 dark:text-slate-400">Watch progress bars fill as data travels over encrypted WebRTC DataChannels.</dd>
                        </div>
                    </dl>
                </div>
                <div class="grid gap-6">
                    <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
                        <h3 class="text-lg font-semibold">Why N-Share?</h3>
                        <ul class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-400">
                            <li><span class="font-semibold text-slate-900 dark:text-white">Speed:</span> Direct peer connections means no waiting for server uploads.</li>
                            <li><span class="font-semibold text-slate-900 dark:text-white">Privacy:</span> Files never touch our servers — only your peers receive them.</li>
                            <li><span class="font-semibold text-slate-900 dark:text-white">Cross-platform:</span> Works on modern browsers across desktop and mobile.</li>
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
                        <h3 class="text-lg font-semibold">Local cache safety</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Transfers persist in localStorage so accidental refreshes or tab closes won't lose your pending files.</p>
                        <div class="mt-4 flex items-center gap-4 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-500/20 text-brand-500">1</span>
                                Select room
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-500/20 text-brand-500">2</span>
                                Drop files
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-500/20 text-brand-500">3</span>
                                Share instantly
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="bg-slate-50 dark:bg-slate-900/30">
            <div class="mx-auto max-w-7xl px-6 py-24">
                <h2 class="text-3xl font-semibold">Key features</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <article class="feature-card">
                        <h3>Person-to-person rooms</h3>
                        <p>Generate instant rooms with unique IDs and connect securely with one peer.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Team spaces</h3>
                        <p>Invite teammates into a shared room and exchange files like a chat timeline.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Drag-and-drop uploads</h3>
                        <p>Drop any file and watch progress bars track upload and download events.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Live user presence</h3>
                        <p>See who is online with avatars and status indicators powered by sockets.</p>
                    </article>
                    <article class="feature-card">
                        <h3>File previews</h3>
                        <p>Preview images, videos, PDFs, and archives directly in the room timeline.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Offline safe</h3>
                        <p>Local cache ensures unsent files are restored if your browser reloads.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="testimonials" class="mx-auto max-w-7xl px-6 py-24">
            <div class="grid gap-12 lg:grid-cols-2">
                <div>
                    <h2 class="text-3xl font-semibold">Loved by fast-moving teams</h2>
                    <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">N-Share keeps product squads, agencies, and remote teams connected with realtime file delivery.</p>
                </div>
                <div class="grid gap-6">
                    <div class="testimonial">
                        <p>“We ship large design files across continents in seconds. N-Share replaced our clunky FTP workflows.”</p>
                        <span>— Leah, Design Lead</span>
                    </div>
                    <div class="testimonial">
                        <p>“Our engineering team shares build artifacts instantly. No more waiting for uploads.”</p>
                        <span>— Omar, CTO</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing" class="bg-slate-50 dark:bg-slate-900/30">
            <div class="mx-auto max-w-5xl px-6 py-24 text-center">
                <h2 class="text-3xl font-semibold">Transparent pricing</h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">Start free with unlimited peer-to-peer transfers. Upgrade for advanced team analytics.</p>
                <div class="mt-12 grid gap-6 md:grid-cols-2">
                    <div class="plan-card">
                        <h3>Starter</h3>
                        <p class="price">$0</p>
                        <ul>
                            <li>Unlimited P2P transfers</li>
                            <li>Google login &amp; remember me</li>
                            <li>Secure WebRTC channels</li>
                        </ul>
                        <button class="plan-btn">Start Sharing</button>
                    </div>
                    <div class="plan-card border-brand-500 shadow-lg">
                        <h3>Teams</h3>
                        <p class="price">$12<span class="text-base font-medium">/member</span></p>
                        <ul>
                            <li>Team rooms &amp; presence</li>
                            <li>Audit history</li>
                            <li>Priority TURN relays</li>
                        </ul>
                        <button class="plan-btn bg-brand-500 text-white hover:bg-brand-600">Upgrade</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-4xl px-6 py-24 text-center">
            <h2 class="text-3xl font-semibold">Ready to move files faster?</h2>
            <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">Join thousands who trust N-Share for secure, instant file delivery. Your first transfer takes less than 30 seconds.</p>
            <button class="mt-8 inline-flex items-center justify-center rounded-full bg-brand-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-brand-600" id="ctaStart">Start Sharing Now</button>
        </section>
    </main>

    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-6 py-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <p class="text-sm text-slate-500">© <?php echo date('Y'); ?> N-Share. All rights reserved.</p>
            <div class="flex gap-6 text-sm text-slate-500">
                <a href="#about" class="hover:text-brand-600">About</a>
                <a href="#privacy" class="hover:text-brand-600">Privacy Policy</a>
                <a href="#terms" class="hover:text-brand-600">Terms</a>
            </div>
            <div class="flex gap-4 text-slate-500">
                <a href="https://twitter.com" aria-label="Twitter" class="hover:text-brand-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.24 4.24 0 001.86-2.34 8.41 8.41 0 01-2.68 1.05 4.21 4.21 0 00-7.18 3.84A11.94 11.94 0 013 4.79a4.21 4.21 0 001.3 5.62 4.19 4.19 0 01-1.91-.53v.05a4.21 4.21 0 003.37 4.13 4.24 4.24 0 01-1.9.07 4.22 4.22 0 003.94 2.93A8.45 8.45 0 012 19.54a11.9 11.9 0 006.46 1.89c7.75 0 11.98-6.42 11.98-11.98 0-.18-.01-.35-.02-.53A8.56 8.56 0 0022.46 6z"/></svg>
                </a>
                <a href="https://github.com" aria-label="GitHub" class="hover:text-brand-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2a10 10 0 00-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.77.6-3.36-1.34-3.36-1.34-.45-1.13-1.11-1.43-1.11-1.43-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.53 2.35 1.09 2.92.83.09-.65.35-1.09.64-1.34-2.21-.25-4.55-1.1-4.55-4.89 0-1.08.39-1.96 1.03-2.65-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02a9.55 9.55 0 015 0c1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.69 1.03 1.57 1.03 2.65 0 3.8-2.34 4.64-4.57 4.89.36.31.69.93.69 1.88v2.79c0 .27.18.58.69.48A10 10 0 0012 2z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        </div>
    </footer>
</div>

<div id="roomModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-6">
    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <h3 id="roomModalTitle" class="text-xl font-semibold"></h3>
                <p id="roomModalDescription" class="mt-1 text-sm text-slate-500 dark:text-slate-400"></p>
            </div>
            <button id="closeRoomModal" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                <span class="sr-only">Close</span>
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="roomForm" class="mt-6 space-y-4">
            <div id="roomTitleField" class="hidden">
                <label class="block text-sm font-medium">Room name</label>
                <input type="text" name="roomTitle" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900" placeholder="Design standup" />
            </div>
            <div>
                <label class="block text-sm font-medium">Room ID</label>
                <input type="text" name="roomId" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900" placeholder="Auto-generated" readonly />
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-500 focus:ring-brand-500"> Remember me</label>
                <button id="roomActionButton" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Create room</button>
            </div>
        </form>
    </div>
</div>

<div id="roomInterface" class="fixed inset-0 z-40 hidden bg-white dark:bg-slate-950 lg:grid lg:grid-cols-[280px,1fr]">
    <aside class="hidden border-r border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 lg:flex lg:flex-col">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Active Users (<span id="sidebarActiveCount">0</span>)</h3>
        </div>
        <div id="userList" class="flex-1 overflow-y-auto px-4 py-4 space-y-3"></div>
    </aside>
    <section class="flex h-full flex-col">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-4 py-3">
            <div>
                <p class="text-sm font-semibold" id="roomName">Room</p>
                <p class="text-xs text-slate-500" id="roomIdDisplay"></p>
            </div>
            <div class="flex items-center gap-2">
                <button id="leaveRoom" class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-1.5 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800">Leave</button>
                <button id="copyRoomId" class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-600">Copy ID</button>
            </div>
        </div>
        <div id="feed" class="flex-1 overflow-y-auto space-y-4 bg-slate-50 dark:bg-slate-900 p-6"></div>
        <div class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 p-4">
            <div id="dropZone" class="flex items-center justify-between gap-4 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 p-4">
                <div>
                    <p class="text-sm font-semibold">Drop files here or use the file picker</p>
                    <p class="text-xs text-slate-500">Supported: images, video, pdf, zip, docs</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="file" id="filePicker" class="hidden" multiple>
                    <button id="browseFiles" class="rounded-lg bg-brand-500 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-600">Browse</button>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
window.NSHARE_CONFIG = {
    apiBase: '/backend/api',
    websocket: '<?php echo htmlspecialchars($websocketUrl, ENT_QUOTES); ?>',
    firebase: {
        apiKey: '<?php echo htmlspecialchars(env_get('FIREBASE_API_KEY', ''), ENT_QUOTES); ?>',
        authDomain: '<?php echo htmlspecialchars(env_get('FIREBASE_AUTH_DOMAIN', ''), ENT_QUOTES); ?>',
        projectId: '<?php echo htmlspecialchars(env_get('FIREBASE_PROJECT_ID', ''), ENT_QUOTES); ?>',
        appId: '<?php echo htmlspecialchars(env_get('FIREBASE_APP_ID', ''), ENT_QUOTES); ?>'
    },
    turn: {
        url: '<?php echo htmlspecialchars(env_get('TURN_URL', ''), ENT_QUOTES); ?>',
        username: '<?php echo htmlspecialchars(env_get('TURN_USERNAME', ''), ENT_QUOTES); ?>',
        password: '<?php echo htmlspecialchars(env_get('TURN_PASSWORD', ''), ENT_QUOTES); ?>'
    },
    user: <?php echo json_encode($user, JSON_THROW_ON_ERROR); ?>
};
</script>
<script type="module" src="assets/js/app.js"></script>
<script type="module" src="assets/js/webrtc.js"></script>
<script type="module" src="assets/js/auth.js"></script>
</body>
</html>
