<div id="join-modal" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4 py-6">
  <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl shadow-indigo-500/20 transition dark:bg-slate-900">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-lg font-semibold text-slate-900 dark:text-white">Join a P2P Room</p>
        <p class="text-sm text-slate-500 dark:text-slate-400">Enter a room ID to hop in instantly.</p>
      </div>
      <button data-modal-close class="rounded-full p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <form class="mt-6 space-y-3">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="room-id">Enter Room ID</label>
      <input id="room-id" type="text" placeholder="room-9d21" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-900 outline-none ring-2 ring-transparent transition focus:border-indigo-200 focus:bg-white focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-indigo-500/30" required />
      <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Join Now</button>
      <p class="rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700 ring-1 ring-green-100 dark:bg-green-500/10 dark:text-green-200 dark:ring-green-500/20">Connected! Taking you to the room…</p>
      <p class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700 ring-1 ring-red-100 dark:bg-red-500/10 dark:text-red-200 dark:ring-red-500/20">Room not found. Double-check the ID.</p>
    </form>
  </div>
</div>
