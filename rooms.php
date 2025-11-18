<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Recent Rooms', true, ['name' => 'Skyler Dawn']);
?>
<section class="flex items-center justify-between">
  <div>
    <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Rooms</p>
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Recent Rooms</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400">Active peer-to-peer spaces you can jump into.</p>
  </div>
  <a href="room.php" class="hidden rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40 sm:inline-flex">Create new room</a>
</section>

<div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
  <?php
  $rooms = [
    ['id' => 'room-9d21', 'created' => '5 mins ago', 'users' => 6],
    ['id' => 'room-4a88', 'created' => '12 mins ago', 'users' => 3],
    ['id' => 'room-7k02', 'created' => '18 mins ago', 'users' => 8],
    ['id' => 'room-1h77', 'created' => '25 mins ago', 'users' => 2],
    ['id' => 'room-8v64', 'created' => '30 mins ago', 'users' => 5],
    ['id' => 'room-2c56', 'created' => '44 mins ago', 'users' => 4],
  ];
  foreach ($rooms as $room): ?>
    <div class="flex flex-col rounded-2xl border border-slate-100 bg-white p-5 shadow-lg shadow-indigo-500/5 transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-indigo-500/10 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= $room['id']; ?></p>
          <p class="text-xs text-slate-500 dark:text-slate-400">Created <?= $room['created']; ?></p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"><?= $room['users']; ?> users</span>
      </div>
      <div class="mt-4 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Live now
      </div>
      <div class="mt-4 flex gap-2">
        <button class="flex-1 rounded-xl bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-800 transition hover:border-indigo-200 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-100">Preview</button>
        <button data-modal-toggle="join-modal" class="flex-1 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:shadow-indigo-600/40">Join Room</button>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php
renderJoinModal();
endPage();
?>
