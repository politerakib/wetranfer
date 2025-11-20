<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Recent Rooms', true, ['name' => 'Skyler Dawn']);
?>
<section class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
  <div>
    <p class="text-primary fw-semibold mb-1">Rooms</p>
    <h1 class="h3 fw-bold mb-1">Recent Rooms</h1>
    <p class="text-body-secondary small mb-0">Active peer-to-peer spaces you can jump into.</p>
  </div>
  <a href="room.php" class="btn btn-primary rounded-pill fw-semibold">Create new room</a>
</section>

<div class="row g-4 mt-3">
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
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0 rounded-4">
        <div class="card-body d-flex flex-column gap-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold"><?= $room['id']; ?></div>
              <div class="text-body-secondary small">Created <?= $room['created']; ?></div>
            </div>
            <span class="badge rounded-pill text-bg-light text-body-secondary"><?= $room['users']; ?> users</span>
          </div>
          <div class="d-flex align-items-center gap-2 text-success small">
            <span class="badge bg-success p-1 rounded-circle" style="width:10px;height:10px;"></span> Live now
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary flex-fill rounded-3">Preview</button>
            <button class="btn btn-primary flex-fill rounded-3" data-bs-toggle="modal" data-bs-target="#joinModal">Join Room</button>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php
renderJoinModal();
endPage();
?>
