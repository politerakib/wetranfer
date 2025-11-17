<?php
require_once __DIR__ . '/components/layout.php';

ob_start();
?>
<section class="grid place-items-center">
    <div class="w-full max-w-md space-y-6 rounded-3xl border border-slate-100 bg-white p-8 shadow-xl dark:border-slate-800 dark:bg-slate-800/80">
        <div class="space-y-2 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Welcome back</p>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Log in</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">Access your rooms, billing, and profile settings.</p>
        </div>
        <form class="space-y-4">
            <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                <span>Email</span>
                <input type="email" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" placeholder="you@example.com" />
            </label>
            <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                <span>Password</span>
                <div class="flex items-center rounded-xl border border-slate-200 bg-white px-3 shadow-sm focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-200 dark:border-slate-700 dark:bg-slate-900">
                    <input id="loginPassword" type="password" class="w-full bg-transparent px-1 py-3 text-slate-900 focus:outline-none dark:text-white" placeholder="••••••••" />
                    <button type="button" data-toggle-password="loginPassword" class="text-sm font-semibold text-brand-600 dark:text-brand-400">Show</button>
                </div>
            </label>
            <button class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg">Sign in</button>
        </form>
        <p class="text-center text-sm text-slate-600 dark:text-slate-300">Don't have an account? <a href="signup.php" class="font-semibold text-brand-600 dark:text-brand-400">Create one</a></p>
    </div>
</section>
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.togglePassword);
            if (!target) return;
            target.type = target.type === 'password' ? 'text' : 'password';
            btn.textContent = target.type === 'password' ? 'Show' : 'Hide';
        });
    });
</script>
<?php
$content = ob_get_clean();
render_layout('Login', 'login-page', $content, '', false);
?>
