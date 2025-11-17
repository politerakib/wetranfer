<?php
require_once __DIR__ . '/components/layout.php';

$policies = [
    ['title' => 'Data handling', 'copy' => 'Messages are peer-to-peer and not stored. Minimal analytics capture aggregated usage only.'],
    ['title' => 'Cookies', 'copy' => 'We use functional cookies for session control and theme preference.'],
    ['title' => 'Your control', 'copy' => 'You can request exports or deletion of any stored profile metadata at any time.'],
];

ob_start();
?>
<section class="max-w-4xl space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Privacy</p>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Privacy Policy</h1>
        <p class="text-slate-600 dark:text-slate-300">Transparent policies styled with generous spacing and typographic rhythm.</p>
    </div>
    <div class="space-y-4 rounded-3xl border border-slate-100 bg-white p-6 shadow-md dark:border-slate-800 dark:bg-slate-800/70">
        <?php foreach ($policies as $policy): ?>
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white"><?php echo $policy['title']; ?></h2>
                <p class="text-slate-700 dark:text-slate-200"><?php echo $policy['copy']; ?></p>
            </div>
        <?php endforeach; ?>
        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:bg-slate-900/50 dark:text-slate-200">
            Questions? Email privacy@p2prooms.app for export or deletion requests.
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
render_layout('Privacy', 'privacy-page', $content);
?>
