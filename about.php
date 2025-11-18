<?php
require_once __DIR__ . '/components/layout.php';

ob_start();
?>
<section class="max-w-4xl space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">About</p>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">About P2P Rooms</h1>
        <p class="text-slate-600 dark:text-slate-300">A design-driven toolkit for creating secure peer-to-peer experiences in PHP.</p>
    </div>
    <div class="space-y-4 rounded-3xl border border-slate-100 bg-white p-6 shadow-md dark:border-slate-800 dark:bg-slate-800/70">
        <p class="text-slate-700 dark:text-slate-200">P2P Rooms pairs a cohesive Tailwind design system with reusable PHP components. Each page is responsive, themed, and ready to slot into your application.</p>
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-slate-50 p-4 text-sm font-semibold text-slate-800 dark:bg-slate-900/60 dark:text-white">Reusable header & footer</div>
            <div class="rounded-2xl bg-slate-50 p-4 text-sm font-semibold text-slate-800 dark:bg-slate-900/60 dark:text-white">Join modal & forms</div>
            <div class="rounded-2xl bg-slate-50 p-4 text-sm font-semibold text-slate-800 dark:bg-slate-900/60 dark:text-white">Dark / light theming</div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
render_layout('About', 'about-page', $content);
?>
