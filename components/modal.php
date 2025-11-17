<?php
function join_room_modal(): string
{
    ob_start();
    ?>
    <div id="joinRoomModal" data-modal class="fixed inset-0 z-30 flex items-center justify-center bg-slate-900/50 px-4 opacity-0 pointer-events-none transition">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 dark:bg-slate-800 dark:ring-white/10">
            <div class="flex items-start justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">Join a room</p>
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">Enter a room ID to connect instantly.</p>
                </div>
                <button data-modal-close class="text-slate-400 hover:text-slate-700 dark:hover:text-white">✕</button>
            </div>
            <div class="px-6 py-5 space-y-3">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="roomId">Enter Room ID</label>
                <input id="roomId" type="text" placeholder="p2p-8djf-2024" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />
                <div class="flex items-center justify-between text-sm text-slate-500 dark:text-slate-300">
                    <span class="flex items-center space-x-2 text-green-600 dark:text-green-400"><span class="inline-block w-2 h-2 rounded-full bg-green-500"></span><span>Validated</span></span>
                    <span>Secure join enabled</span>
                </div>
                <button class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-white shadow-lg hover:shadow-xl">Join Now</button>
            </div>
            <div class="px-6 py-4 bg-slate-50 text-xs text-slate-500 dark:bg-slate-900 dark:text-slate-300">Need help? Visit the FAQ before joining.</div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>
