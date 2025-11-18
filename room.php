<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Room · room-9d21', true, ['name' => 'Skyler Dawn']);
?>
<section class="space-y-6">
  <div class="flex flex-col gap-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Room ID</p>
      <h1 class="text-xl font-bold text-slate-900 dark:text-white">room-9d21</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">Encrypted chat and presence • 6 users online</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <button class="rounded-xl bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-800 transition hover:border-indigo-200 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-100">Copy Invite</button>
      <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Invite Users</button>
    </div>
  </div>

  <div class="grid gap-4 lg:grid-cols-3 lg:gap-6">
    <div class="lg:col-span-2">
      <div class="flex flex-col rounded-2xl border border-slate-100 bg-white shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:text-slate-200">
          <span>Conversation</span>
          <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-200">Live</span>
        </div>
        <div class="flex-1 space-y-4 overflow-y-auto px-4 py-5 lg:py-6" style="max-height: 460px;">
          <div class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">room created 10:20</div>
          <div class="flex items-start gap-3">
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-center text-sm font-semibold text-white">WD</div>
            <div class="max-w-[75%] rounded-2xl bg-slate-50 p-3 shadow-sm dark:bg-slate-800">
              <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400"><span>Willow D.</span><span>10:22</span></div>
              <p class="mt-1 text-sm text-slate-800 dark:text-slate-100">Anyone else seeing lower ping this morning?</p>
            </div>
          </div>
          <div class="flex items-start gap-3 justify-end">
            <div class="max-w-[75%] rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 p-3 text-white shadow-lg">
              <div class="flex items-center justify-between text-xs text-indigo-100"><span>You</span><span>10:23</span></div>
              <p class="mt-1 text-sm">Yes—relay switched to our edge in Paris.</p>
            </div>
            <div class="hidden h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-center text-sm font-semibold text-white lg:flex">SD</div>
          </div>
          <div class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">encryption upgraded</div>
          <div class="flex items-start gap-3">
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-center text-sm font-semibold text-white">JT</div>
            <div class="max-w-[75%] rounded-2xl bg-slate-50 p-3 shadow-sm dark:bg-slate-800">
              <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400"><span>Jules T.</span><span>10:26</span></div>
              <p class="mt-1 text-sm text-slate-800 dark:text-slate-100">Streaming the slides now. Handoff is instant.</p>
            </div>
          </div>
          <div class="flex items-start gap-3 justify-end">
            <div class="max-w-[75%] rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 p-3 text-white shadow-lg">
              <div class="flex items-center justify-between text-xs text-indigo-100"><span>You</span><span>10:27</span></div>
              <p class="mt-1 text-sm">Audio is clean on my side.</p>
            </div>
            <div class="hidden h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-center text-sm font-semibold text-white lg:flex">SD</div>
          </div>
          <div class="flex items-start gap-3">
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-center text-sm font-semibold text-white">AR</div>
            <div class="max-w-[75%] rounded-2xl bg-slate-50 p-3 shadow-sm dark:bg-slate-800">
              <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400"><span>Ari R.</span><span>10:28</span></div>
              <p class="mt-1 text-sm text-slate-800 dark:text-slate-100">Love the new theme toggle ✨</p>
            </div>
          </div>
          <div class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">end-to-end encrypted</div>
        </div>
        <div class="border-t border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/60">
          <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <button class="rounded-xl bg-slate-100 p-2 text-slate-500 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-indigo-400" aria-label="Add emoji">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.5a8.5 8.5 0 1 0 0-17 8.5 8.5 0 0 0 0 17Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 10h.01M15 10h.01M9 15s1.5 1.5 3 1.5 3-1.5 3-1.5" /></svg>
            </button>
            <input type="text" placeholder="Message this room" class="flex-1 bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none dark:text-slate-100 dark:placeholder:text-slate-500" />
            <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Send</button>
          </div>
        </div>
      </div>
    </div>
    <aside class="space-y-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-lg shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-slate-900 dark:text-white">Users in room</p>
          <p class="text-xs text-slate-500 dark:text-slate-400">6 connected</p>
        </div>
        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">Secure</span>
      </div>
      <div class="space-y-3">
        <?php
        $users = [
          ['name' => 'Skyler Dawn', 'status' => 'online'],
          ['name' => 'Willow Drew', 'status' => 'online'],
          ['name' => 'Ari Rivers', 'status' => 'online'],
          ['name' => 'Jules Terra', 'status' => 'away'],
          ['name' => 'Mira Sol', 'status' => 'online'],
          ['name' => 'Ren Vale', 'status' => 'online'],
        ];
        foreach ($users as $user): ?>
          <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-800">
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-center text-xs font-semibold text-white">
                <?= strtoupper(substr($user['name'], 0, 2)); ?>
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= $user['name']; ?></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 capitalize">Status: <?= $user['status']; ?></p>
              </div>
            </div>
            <span class="h-2 w-2 rounded-full <?= $user['status'] === 'online' ? 'bg-emerald-400' : 'bg-amber-400'; ?>"></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="rounded-xl bg-slate-50 p-3 text-xs text-slate-500 dark:bg-slate-800 dark:text-slate-400">
        <p class="font-semibold text-slate-700 dark:text-slate-200">Safety</p>
        <p class="mt-1">We verify peers silently and never store your media or text.</p>
      </div>
    </aside>
  </div>
</section>
<?php
renderJoinModal();
endPage();
?>
