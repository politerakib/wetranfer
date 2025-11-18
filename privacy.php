<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Privacy', true, ['name' => 'Skyler Dawn']);
?>
<section class="space-y-4">
  <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Privacy Policy</p>
  <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Your data stays on your device.</h1>
  <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
    <p class="text-sm text-slate-600 dark:text-slate-300">NovaRooms uses peer-to-peer connections so media and chat content never persist on centralized servers. Room IDs are short-lived, encryption keys never leave your browser, and activity logs remain on your device unless you export them.</p>
    <ul class="mt-4 list-disc space-y-2 pl-5 text-sm text-slate-600 dark:text-slate-300">
      <li>No trackers or hidden analytics.</li>
      <li>Dark/light theme preference stored locally only.</li>
      <li>Invite links expire when rooms close.</li>
    </ul>
  </div>
</section>
<?php
endPage();
?>
