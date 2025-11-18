<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Home', true, ['name' => 'Skyler Dawn', 'email' => 'skyler@nrooms.com']);
?>
<section class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 p-8 shadow-xl shadow-indigo-500/30 lg:p-12">
  <div class="grid items-center gap-10 lg:grid-cols-2">
    <div class="space-y-6 text-white">
      <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em]">Secure P2P • Low Latency</p>
      <h1 class="text-3xl font-bold leading-tight sm:text-4xl lg:text-5xl">Create premium peer-to-peer rooms that feel instant and private.</h1>
      <p class="text-lg text-indigo-100">NovaRooms connects people directly. No accounts needed, just elegant rooms, sleek chat, and smooth streaming.</p>
      <div class="flex flex-wrap gap-4">
        <button data-modal-toggle="join-modal" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-indigo-700 shadow-lg shadow-indigo-700/20 transition hover:-translate-y-0.5 hover:shadow-indigo-900/30">
          Join Room
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        </button>
        <a href="room.php" class="inline-flex items-center gap-2 rounded-xl bg-slate-900/30 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:-translate-y-0.5 hover:bg-slate-900/40">
          Create P2P Room
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5v14" /></svg>
        </a>
      </div>
      <div class="flex flex-wrap gap-3 text-sm text-indigo-100">
        <span class="flex items-center gap-2 rounded-full bg-white/10 px-3 py-1"><span class="h-2 w-2 rounded-full bg-emerald-400"></span> Live encryption</span>
        <span class="flex items-center gap-2 rounded-full bg-white/10 px-3 py-1"><span class="h-2 w-2 rounded-full bg-sky-300"></span> Zero storage</span>
        <span class="flex items-center gap-2 rounded-full bg-white/10 px-3 py-1"><span class="h-2 w-2 rounded-full bg-amber-300"></span> One-click join</span>
      </div>
    </div>
    <div class="rounded-2xl bg-white/10 p-6 shadow-2xl shadow-indigo-900/30 backdrop-blur">
      <div class="rounded-xl border border-white/20 bg-white/10 p-4 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-[0.25em] text-indigo-100">Live Room</p>
            <p class="text-lg font-semibold">room-9d21 · 6 online</p>
          </div>
          <div class="flex items-center gap-2 text-xs text-indigo-100">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Secure
          </div>
        </div>
        <div class="mt-4 space-y-3">
          <div class="flex gap-3">
            <div class="h-10 w-10 rounded-full bg-white/20"></div>
            <div class="flex-1 rounded-xl bg-white/10 p-3">
              <div class="flex items-center justify-between text-xs text-indigo-100"><span>Willow</span><span>10:24</span></div>
              <p class="text-sm">Checking latency. Looks smooth! 🔥</p>
            </div>
          </div>
          <div class="flex gap-3">
            <div class="h-10 w-10 rounded-full bg-white/20"></div>
            <div class="flex-1 rounded-xl bg-indigo-900/40 p-3">
              <div class="flex items-center justify-between text-xs text-indigo-100"><span>You</span><span>10:25</span></div>
              <p class="text-sm">Encryption handshake completed.</p>
            </div>
          </div>
          <div class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-indigo-100">End-to-end encrypted</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="mt-12 grid gap-6 lg:grid-cols-3">
  <?php
  $features = [
    ['title' => 'Speed-first networking', 'desc' => 'Direct peer relays with smart region routing to keep latency low.', 'icon' => 'bolt'],
    ['title' => 'Privacy native', 'desc' => 'No centralized storage. Rooms dissolve on exit with ephemeral presence.', 'icon' => 'shield'],
    ['title' => 'No signup needed', 'desc' => 'Spin up secure rooms instantly, share the ID, and start collaborating.', 'icon' => 'sparkles'],
    ['title' => 'Team-friendly', 'desc' => 'Invite-only controls, typed chat, and handoff ready for any device.', 'icon' => 'users'],
    ['title' => 'Responsive by default', 'desc' => 'A mobile-first experience that feels like a native app.', 'icon' => 'device'],
    ['title' => 'Theme aware', 'desc' => 'Auto dark/light with custom gradients and adaptive glassy surfaces.', 'icon' => 'moon'],
  ];
  foreach ($features as $feature): ?>
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/5 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200">
          <?php if ($feature['icon'] === 'bolt'): ?>⚡<?php elseif ($feature['icon'] === 'shield'): ?>🛡️<?php elseif ($feature['icon'] === 'sparkles'): ?>✨<?php elseif ($feature['icon'] === 'users'): ?>👥<?php elseif ($feature['icon'] === 'device'): ?>📱<?php else: ?>🌙<?php endif; ?>
        </span>
        <h3 class="text-base font-semibold text-slate-900 dark:text-white"><?= $feature['title']; ?></h3>
      </div>
      <p class="mt-3 text-sm text-slate-600 dark:text-slate-300"><?= $feature['desc']; ?></p>
    </div>
  <?php endforeach; ?>
</section>

<section class="mt-12 grid gap-6 lg:grid-cols-2">
  <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">FAQ</p>
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Everything you need to know</h2>
      </div>
      <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">Updated weekly</span>
    </div>
    <div class="mt-4 space-y-3">
      <?php
      $faqs = [
        ['q' => 'How secure are the rooms?', 'a' => 'Rooms use peer-to-peer encryption with no central data store. Identities stay on device.'],
        ['q' => 'Do I need to create an account?', 'a' => 'No accounts required. Share your room ID to collaborate instantly.'],
        ['q' => 'Can I switch themes?', 'a' => 'Use the toggle in the navbar. The UI respects your system preference automatically.'],
      ];
      foreach ($faqs as $faq): ?>
        <div data-accordion class="overflow-hidden rounded-xl border border-slate-100 bg-white/60 p-4 transition hover:border-indigo-200 dark:border-slate-800 dark:bg-slate-900/60">
          <button data-accordion-header class="flex w-full items-center justify-between text-left text-sm font-semibold text-slate-900 dark:text-white">
            <span><?= $faq['q']; ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
          </button>
          <p data-accordion-content class="mt-3 hidden text-sm text-slate-600 dark:text-slate-300"><?= $faq['a']; ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Quick Actions</p>
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Start collaborating</h2>
      </div>
      <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-200">Online</span>
    </div>
    <div class="mt-6 space-y-3">
      <button data-modal-toggle="join-modal" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-800 shadow-sm transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-indigo-500">
        Join with a Room ID
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
      </button>
      <a href="rooms.php" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-left text-sm font-semibold text-slate-800 shadow-sm transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-indigo-500">
        View live rooms
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8m-4-4v8" /></svg>
      </a>
      <a href="profile.php" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-left text-sm font-semibold text-slate-800 shadow-sm transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-indigo-500">
        Update your profile
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m-7.5-7.5h15" /></svg>
      </a>
    </div>
  </div>
</section>
<?php
renderJoinModal();
endPage();
?>
