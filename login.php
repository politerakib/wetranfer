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
        <button class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-sm hover:border-brand-500 hover:text-brand-700 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6 1.54 7.38 2.84l5.4-5.4C33.7 3.36 29.27 1.5 24 1.5 14.9 1.5 7.17 6.86 3.74 14.35l6.64 5.16C12.08 13.4 17.5 9.5 24 9.5z" />
                <path fill="#4285F4" d="M46.5 24.5c0-1.64-.15-3.22-.42-4.75H24v9h12.7c-.55 3-2.24 5.5-4.77 7.2l7.3 5.64C43.9 37.24 46.5 31.5 46.5 24.5z" />
                <path fill="#FBBC05" d="M10.38 28.66A14.5 14.5 0 0 1 9.5 24c0-1.62.28-3.18.78-4.66l-6.64-5.16A22.45 22.45 0 0 0 1.5 24c0 3.6.86 7 2.4 10l6.48-5.34z" />
                <path fill="#34A853" d="M24 46.5c6.27 0 11.53-2.07 15.38-5.62l-7.3-5.64c-2.02 1.36-4.62 2.16-8.08 2.16-6.5 0-11.92-3.9-13.92-9.66l-6.64 5.16C7.17 41.14 14.9 46.5 24 46.5z" />
                <path fill="none" d="M1.5 1.5h45v45h-45z" />
            </svg>
            Continue with Google
        </button>
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
            <span class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></span>
            or
            <span class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></span>
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
