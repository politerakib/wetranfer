<?php
if (!isset($pageTitle)) {
    $pageTitle = 'P2P Rooms';
}
if (!isset($isLoggedIn)) {
    $isLoggedIn = false;
}
if (!isset($userName)) {
    $userName = 'Guest';
}
if (!isset($userEmail)) {
    $userEmail = 'guest@example.com';
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> · P2P Rooms</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
      :root {
        --brand-primary: #4f46e5;
        --brand-accent: #7c3aed;
      }
      body {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      }
      .glass-card {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
      }
      .hero-card {
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
      }
      .chat-bubble {
        border-radius: 1.25rem;
      }
      .bg-dotted {
        background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.05) 1px, transparent 0);
        background-size: 12px 12px;
      }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary text-body">
  <div id="app" class="d-flex flex-column min-vh-100">
    <header class="sticky-top bg-body shadow-sm">
      <nav class="navbar navbar-expand-lg navbar-light py-3 border-bottom border-0 border-opacity-10">
        <div class="container">
          <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <div class="rounded-3 text-white fw-bold d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent)); box-shadow:0 10px 30px rgba(79,70,229,0.25);">P2P</div>
            <div>
              <div class="fw-semibold">NovaRooms</div>
              <small class="text-body-secondary">Secure peer-to-peer spaces</small>
            </div>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
              <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Home</a></li>
              <li class="nav-item"><a class="nav-link fw-semibold" href="rooms.php">Rooms</a></li>
              <li class="nav-item"><a class="nav-link fw-semibold" href="profile.php">Profile</a></li>
              <li class="nav-item"><a class="nav-link fw-semibold" href="about.php">About</a></li>
              <li class="nav-item"><a class="nav-link fw-semibold" href="terms.php">FAQ</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
              <button id="theme-toggle" class="btn btn-outline-secondary rounded-pill d-inline-flex align-items-center" type="button" aria-label="Toggle theme">
                <i id="icon-sun" class="bi bi-sun-fill d-none"></i>
                <i id="icon-moon" class="bi bi-moon-stars"></i>
              </button>
              <?php if ($isLoggedIn): ?>
                <div class="dropdown">
                  <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="rounded-circle text-white fw-semibold d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">
                      <?= strtoupper(substr($userName, 0, 2)); ?>
                    </div>
                    <div class="d-none d-lg-block text-start">
                      <div class="fw-semibold"><?= htmlspecialchars($userName); ?></div>
                      <small class="text-body-secondary">Online</small>
                    </div>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="#">Billing</a></li>
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#">Sign Out</a></li>
                  </ul>
                </div>
              <?php else: ?>
                <a href="login.php" class="btn btn-outline-secondary rounded-pill fw-semibold">Login</a>
                <a href="signup.php" class="btn btn-primary rounded-pill fw-semibold">Signup</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </nav>
    </header>
    <main class="flex-grow-1 py-5">
      <div class="container">
