<?php
require_once __DIR__ . '/components/layout.php';

$sections = [
    ['title' => 'Usage', 'copy' => 'Use rooms for collaborative communication and avoid abusive content. Sessions may close after extended inactivity.'],
    ['title' => 'Payments', 'copy' => 'Billing is powered by secure providers; invoices and history are available in your profile.'],
    ['title' => 'Liability', 'copy' => 'P2P Rooms offers the UI kit as-is without warranties; ensure your deployment meets compliance needs.'],
];

ob_start();
?>
<section class="max-w-4xl space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Terms</p>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Terms of Service</h1>
        <p class="text-slate-600 dark:text-slate-300">Clean, readable policies styled for light and dark mode.</p>
    </div>
    <div class="space-y-4 rounded-3xl border border-slate-100 bg-white p-6 shadow-md dark:border-slate-800 dark:bg-slate-800/70">
        <?php foreach ($sections as $section): ?>
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white"><?php echo $section['title']; ?></h2>
                <p class="text-slate-700 dark:text-slate-200"><?php echo $section['copy']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
render_layout('Terms', 'terms-page', $content);
?>
