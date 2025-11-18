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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> · P2P Rooms</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
      tailwind = typeof tailwind !== 'undefined' ? tailwind : {};
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'ui-sans-serif', 'system-ui'],
            },
            colors: {
              brand: {
                50: '#eef2ff',
                100: '#e0e7ff',
                200: '#c7d2fe',
                400: '#818cf8',
                500: '#6366f1',
                600: '#4f46e5'
              },
            },
          }
        }
      }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      html {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      }
      .glass {
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
      }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 transition-colors duration-300 dark:bg-slate-900 dark:text-slate-100">
  <div id="app" class="flex min-h-screen flex-col">
    <header class="sticky top-0 z-40 w-full border-b border-slate-100 bg-white/90 shadow-md transition-colors duration-300 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/80">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 lg:px-8">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30">
            <span class="text-lg font-bold">P2P</span>
          </div>
          <div>
            <p class="text-lg font-semibold text-slate-900 dark:text-white">NovaRooms</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Secure peer-to-peer spaces</p>
          </div>
        </div>
        <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300 lg:flex">
          <a href="index.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>
          <a href="rooms.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">Rooms</a>
          <a href="profile.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">Profile</a>
          <a href="about.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">About</a>
          <a href="terms.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">FAQ</a>
        </nav>
        <div class="flex items-center gap-3">
          <button id="theme-toggle" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:text-indigo-300" aria-label="Toggle dark mode">
            <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v-3m0 21v-3m7.5-7.5h3m-21 0h3m12.29-6.79 2.12-2.12M4.59 19.41l2.12-2.12m0-9.7L4.59 4.59m14.82 14.82-2.12-2.12" />
              <circle cx="12" cy="12" r="5" />
            </svg>
            <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
            </svg>
          </button>
          <?php if ($isLoggedIn): ?>
            <div class="relative">
              <button id="avatar-button" class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2 text-left shadow-sm transition hover:border-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-indigo-500">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-semibold text-white">
                  <?= strtoupper(substr($userName, 0, 2)); ?>
                </div>
                <div class="hidden text-sm lg:block">
                  <p class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($userName); ?></p>
                  <p class="text-xs text-slate-500 dark:text-slate-400">Online</p>
                </div>
              </button>
              <div id="avatar-menu" class="absolute right-0 mt-2 hidden w-48 rounded-xl border border-slate-100 bg-white p-2 shadow-lg dark:border-slate-800 dark:bg-slate-900">
                <a class="block rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-indigo-50 dark:text-slate-200 dark:hover:bg-indigo-500/20" href="#">Billing</a>
                <a class="block rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-indigo-50 dark:text-slate-200 dark:hover:bg-indigo-500/20" href="profile.php">Profile</a>
                <a class="block rounded-lg px-3 py-2 text-sm text-red-600 transition hover:bg-red-50 dark:hover:bg-red-500/10" href="#">Sign Out</a>
              </div>
            </div>
          <?php else: ?>
            <a href="login.php" class="hidden rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-200 dark:hover:border-indigo-500 lg:block">Login</a>
            <a href="signup.php" class="hidden rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40 lg:block">Signup</a>
          <?php endif; ?>
          <button id="mobile-menu-toggle" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:border-indigo-500 lg:hidden" aria-label="Toggle menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
        </div>
      </div>
      <div id="mobile-menu" class="hidden border-t border-slate-100 bg-white px-4 pb-4 pt-3 dark:border-slate-800 dark:bg-slate-900 lg:hidden">
        <nav class="flex flex-col gap-3 text-sm font-medium text-slate-700 dark:text-slate-200">
          <a href="index.php" class="rounded-lg px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-500/10">Home</a>
          <a href="rooms.php" class="rounded-lg px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-500/10">Rooms</a>
          <a href="profile.php" class="rounded-lg px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-500/10">Profile</a>
          <a href="about.php" class="rounded-lg px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-500/10">About</a>
          <a href="terms.php" class="rounded-lg px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-500/10">FAQ</a>
          <?php if (!$isLoggedIn): ?>
            <div class="mt-2 flex gap-2">
              <a href="login.php" class="flex-1 rounded-lg border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-200 dark:hover:border-indigo-500">Login</a>
              <a href="signup.php" class="flex-1 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Signup</a>
            </div>
          <?php endif; ?>
        </nav>
      </div>
    </header>
    <main class="flex-1 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-900">
      <div class="mx-auto max-w-6xl px-4 py-10 lg:px-8 lg:py-14">
