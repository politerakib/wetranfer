<?php
require_once __DIR__ . '/components/layout.php';
require_once __DIR__ . '/components/modal.php';

$rooms = [
    ['id' => 'p2p-8931', 'created' => '2m ago', 'users' => 6],
    ['id' => 'studio-431', 'created' => '14m ago', 'users' => 3],
    ['id' => 'design-lab', 'created' => '27m ago', 'users' => 9],
    ['id' => 'qa-sync', 'created' => '1h ago', 'users' => 2],
];

ob_start();
?>
<section class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Room hub</p>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Recent Rooms</h1>
            <p class="text-slate-600 dark:text-slate-300">Join live rooms instantly or spin up a new peer-to-peer space.</p>
        </div>
        <div class="flex gap-3">
            <button data-modal-open="joinRoomModal" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:border-brand-400 dark:border-slate-700 dark:bg-slate-800 dark:text-white">Join with ID</button>
            <a href="room.php" class="rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-lg">Create Room</a>
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <?php foreach ($rooms as $room): ?>
            <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-800/80">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">Room ID</p>
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white"><?php echo $room['id']; ?></h3>
                        <p class="text-sm text-slate-500 dark:text-slate-300">Created <?php echo $room['created']; ?></p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-white"><?php echo $room['users']; ?> users</span>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex -space-x-3">
                        <?php for ($i = 0; $i < min($room['users'], 5); $i++): ?>
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-sm font-semibold text-white ring-2 ring-white dark:ring-slate-800"><?php echo $i + 1; ?></span>
                        <?php endfor; ?>
                    </div>
                    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800 dark:bg-brand-500 dark:hover:bg-brand-600">Join Room</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
$modals = join_room_modal();
render_layout('Recent Rooms', 'rooms-page', $content, $modals);
?>
