<?php
require_once __DIR__ . '/partials/layout.php';
startPage('About', true, ['name' => 'Skyler Dawn']);
?>
<section class="space-y-4">
  <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">About NovaRooms</p>
  <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Premium peer-to-peer collaboration, thoughtfully designed.</h1>
  <p class="text-lg text-slate-600 dark:text-slate-300">NovaRooms is a privacy-first room system inspired by modern tools like WhatsApp Web and Discord. We obsess over micro-interactions, responsive layouts, and an adaptive design system you can lift directly into PHP.</p>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-md dark:border-slate-800 dark:bg-slate-900">
      <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Principles</h2>
      <ul class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-300">
        <li>• Privacy-first with end-to-end thinking</li>
        <li>• Responsive by default, from mobile to desktop</li>
        <li>• Components that can be reused across PHP layouts</li>
        <li>• Cohesive typography and spacing tokens</li>
      </ul>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-md dark:border-slate-800 dark:bg-slate-900">
      <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Stack-ready</h2>
      <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">The UI kit pairs Tailwind utility classes with PHP components (header.php, footer.php, modal.php, layout.php) so you can slot in authentication, billing, and messaging logic instantly.</p>
    </div>
  </div>
</section>
<?php
endPage();
?>
