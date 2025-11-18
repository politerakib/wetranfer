<?php
require_once __DIR__ . '/components/layout.php';

$user = [
    'name' => 'Jordan Lee',
    'email' => 'jordan@example.com',
];

ob_start();
?>
<section class="max-w-3xl space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Account</p>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Profile</h1>
        <p class="text-slate-600 dark:text-slate-300">Update your details, avatar, and credentials.</p>
    </div>
    <div class="space-y-6 rounded-3xl border border-slate-100 bg-white p-6 shadow-md dark:border-slate-800 dark:bg-slate-800/80">
        <div class="flex flex-wrap items-center gap-4">
            <img src="https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=120&q=80" alt="Avatar" class="h-16 w-16 rounded-2xl object-cover shadow-lg" />
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?php echo $user['name']; ?></p>
                <p class="text-sm text-slate-600 dark:text-slate-300"><?php echo $user['email']; ?></p>
            </div>
            <button class="ml-auto rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:border-brand-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">Change avatar</button>
        </div>
        <form class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    <span>Full name</span>
                    <input type="text" value="<?php echo $user['name']; ?>" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />
                </label>
                <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    <span>Email</span>
                    <input type="email" value="<?php echo $user['email']; ?>" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />
                </label>
            </div>
            <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                <span>Headline</span>
                <input type="text" placeholder="Product designer, realtime UX" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />
            </label>
            <div class="flex flex-wrap items-center gap-3">
                <button class="rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg">Update profile</button>
                <button class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 shadow-sm hover:border-brand-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">Change password</button>
            </div>
        </form>
    </div>
</section>
<?php
$content = ob_get_clean();
render_layout('Profile', 'profile-page', $content);
?>
