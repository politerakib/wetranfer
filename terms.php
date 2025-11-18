<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Terms & FAQ', true, ['name' => 'Skyler Dawn']);
?>
<section class="space-y-4">
  <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Terms & FAQ</p>
  <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Plain-language policies built for privacy.</h1>
  <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Terms of Use</h2>
    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">NovaRooms enables peer-to-peer communication. By using the app you agree to keep room links private, respect participant privacy, and avoid storing sensitive data on shared screens. Rooms are ephemeral and auto-expire when all participants leave.</p>
  </div>
  <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">FAQ</h2>
    <div class="mt-3 space-y-2">
      <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300"><strong class="text-slate-900 dark:text-white">Is data stored?</strong> No, messages and media are not persisted on servers.</div>
      <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300"><strong class="text-slate-900 dark:text-white">Can I export chats?</strong> Export is optional and disabled by default; enable it per room.</div>
      <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300"><strong class="text-slate-900 dark:text-white">How is support handled?</strong> Status and contact links are in the footer for quick help.</div>
    </div>
  </div>
</section>
<?php
endPage();
?>
