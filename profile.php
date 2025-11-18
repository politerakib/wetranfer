<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Profile', true, ['name' => 'Skyler Dawn', 'email' => 'skyler@nrooms.com']);
?>
<section class="grid gap-6 lg:grid-cols-3">
  <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-center gap-4">
      <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-center text-xl font-semibold text-white">SD</div>
      <div>
        <p class="text-lg font-semibold text-slate-900 dark:text-white">Skyler Dawn</p>
        <p class="text-sm text-slate-500 dark:text-slate-400">Product Designer</p>
      </div>
    </div>
    <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">Your profile is what your collaborators see when you join a room. Keep it fresh and trustworthy.</p>
    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
      <span class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200">Verified</span>
      <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-200">Online</span>
    </div>
  </div>
  <div class="lg:col-span-2 space-y-6">
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Profile</p>
          <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Update your details</h2>
        </div>
        <button class="rounded-xl bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-800 transition hover:border-indigo-200 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-100">Change avatar</button>
      </div>
      <form class="mt-4 grid gap-4 md:grid-cols-2">
        <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
          Full name
          <input type="text" value="Skyler Dawn" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
        </label>
        <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
          Email
          <input type="email" value="skyler@nrooms.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
        </label>
        <label class="md:col-span-2 space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
          Bio
          <textarea rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">Designing seamless, secure P2P experiences.</textarea>
        </label>
        <div class="md:col-span-2 flex gap-3">
          <button type="button" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-200 dark:hover:border-indigo-500">Cancel</button>
          <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Update info</button>
        </div>
      </form>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Security</p>
          <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Change password</h2>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">Never shared</span>
      </div>
      <form class="mt-4 space-y-4 md:grid md:grid-cols-3 md:gap-4 md:space-y-0">
        <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
          Current password
          <div class="relative">
            <input id="current-password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
            <button type="button" data-password-toggle="current-password" class="absolute inset-y-0 right-3 my-auto flex items-center text-slate-400" aria-label="Toggle password">
              <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
              <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.032 7.244 19 12 19c.993 0 1.953-.138 2.86-.395M6.228 6.228A10.45 10.45 0 0 1 12 5c4.756 0 8.773 2.968 10.065 7-.458 1.504-1.324 2.842-2.472 3.915M6.228 6.228 3 3m3.228 3.228 3.65 3.65m5.184 5.184L21 21m-5.188-5.188-3.65-3.65" /></svg>
            </button>
          </div>
        </label>
        <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
          New password
          <div class="relative">
            <input id="new-password" type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
            <button type="button" data-password-toggle="new-password" class="absolute inset-y-0 right-3 my-auto flex items-center text-slate-400" aria-label="Toggle password">
              <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
              <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.032 7.244 19 12 19c.993 0 1.953-.138 2.86-.395M6.228 6.228A10.45 10.45 0 0 1 12 5c4.756 0 8.773 2.968 10.065 7-.458 1.504-1.324 2.842-2.472 3.915M6.228 6.228 3 3m3.228 3.228 3.65 3.65m5.184 5.184L21 21m-5.188-5.188-3.65-3.65" /></svg>
            </button>
          </div>
        </label>
        <label class="space-y-2 text-sm font-medium text-slate-700 dark:text-slate-300">
          Confirm password
          <input type="password" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-200 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" />
        </label>
        <div class="md:col-span-3 flex gap-3">
          <button type="reset" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-200 dark:hover:border-indigo-500">Reset</button>
          <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Update password</button>
        </div>
      </form>
    </div>
  </div>
</section>
<?php
endPage();
?>
