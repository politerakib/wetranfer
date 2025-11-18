<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Signup', false);
?>
<section class="flex items-center justify-center">
  <div class="w-full max-w-md rounded-3xl border border-slate-100 bg-white p-8 shadow-2xl shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
    <div class="text-center">
      <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Get started</p>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create your account</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">Launch encrypted rooms and invite your team.</p>
    </div>
    <form class="mt-6 space-y-4">
      <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
        Full name
        <input type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" placeholder="Avery Lee" />
      </label>
      <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
        Email
        <input type="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" placeholder="you@example.com" />
      </label>
      <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
        Password
        <div class="relative">
          <input id="signup-password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" placeholder="••••••" />
          <button type="button" data-password-toggle="signup-password" class="absolute inset-y-0 right-3 my-auto flex items-center text-slate-400" aria-label="Toggle password">
            <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
            <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.032 7.244 19 12 19c.993 0 1.953-.138 2.86-.395M6.228 6.228A10.45 10.45 0 0 1 12 5c4.756 0 8.773 2.968 10.065 7-.458 1.504-1.324 2.842-2.472 3.915M6.228 6.228 3 3m3.228 3.228 3.65 3.65m5.184 5.184L21 21m-5.188-5.188-3.65-3.65" /></svg>
          </button>
        </div>
      </label>
      <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Create account</button>
      <button type="button" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-indigo-500">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="h-5 w-5" />
        Sign up with Google
      </button>
      <p class="text-center text-sm text-slate-500 dark:text-slate-400">Already have an account? <a href="login.php" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Log in</a></p>
    </form>
  </div>
</section>
<?php
endPage();
?>
