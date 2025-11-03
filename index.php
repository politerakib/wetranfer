<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

$user = ensure_user($mysqli);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WeTransfer Realtime - Share Files Instantly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
    <header class="border-bottom">
        <nav class="navbar navbar-expand-lg bg-body-tertiary py-3">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">
                    <i class="fa-solid fa-paper-plane text-primary me-2"></i>WeTransfer Realtime
                </a>
                <div class="d-flex align-items-center gap-3">
                    <span class="user-badge text-secondary">
                        <i class="fa-solid fa-circle-user"></i>
                        <?php echo htmlspecialchars($user['display_name']); ?>
                    </span>
                    <button id="themeToggle" class="theme-toggle" type="button" aria-label="Toggle theme">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <section class="hero text-center">
            <div class="badge bg-primary-subtle text-primary mb-3">WebRTC + Socket.IO</div>
            <h1 class="display-5 fw-bold">Share files in realtime with your team</h1>
            <p class="lead text-secondary mx-auto" style="max-width: 720px;">
                Create instant peer-to-peer rooms for secure file sharing. Collaborate through live file previews, drag & drop uploads, and realtime notifications.
            </p>
        </section>

        <section class="row g-4 justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-action h-100 shadow-sm" id="btnSend">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-upload fa-3x text-primary mb-3"></i>
                        <h3>Send File</h3>
                        <p class="text-secondary">Create a secure room with a single click and share files instantly.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-action h-100 shadow-sm" id="btnReceive">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-download fa-3x text-success mb-3"></i>
                        <h3>Receive File</h3>
                        <p class="text-secondary">Join an existing room using a shared code and start receiving files.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-action h-100 shadow-sm" id="btnTeam">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-people-group fa-3x text-info mb-3"></i>
                        <h3>Team Share</h3>
                        <p class="text-secondary">Keep a persistent room for your team to collaborate anytime.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-4 d-flex justify-content-center">
            <form id="joinForm" class="w-100" style="max-width: 420px;">
                <div id="roomInputWrapper" class="input-group d-none">
                    <input type="text" class="form-control" name="room_id" placeholder="Enter room ID" required>
                    <button class="btn btn-primary" type="submit">Join</button>
                </div>
            </form>
        </section>
    </main>

    <footer class="bg-body-secondary text-center">
        <div class="container">
            <p class="mb-1">Built with PHP, Node.js, WebRTC, and Socket.IO</p>
            <small class="text-secondary">&copy; <?php echo date('Y'); ?> Realtime Transfer Suite</small>
        </div>
    </footer>

    <script>
        window.APP_CONFIG = {
            apiBase: '<?php echo sanitize_input(getenv('NODE_API_BASE') ?: 'http://localhost:3000'); ?>'
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js" defer></script>
</body>
</html>
