<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Home', true, ['name' => 'Skyler Dawn', 'email' => 'skyler@nrooms.com']);
?>
<section class="hero-card text-white rounded-4 p-4 p-lg-5 shadow-lg">
  <div class="row align-items-center g-4">
    <div class="col-lg-6">
      <div class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 mb-3 fw-semibold">Secure P2P • Low latency</div>
      <h1 class="fw-bold display-5">Create premium peer-to-peer rooms that feel instant and private.</h1>
      <p class="lead mb-4 text-white-50">NovaRooms connects people directly. No accounts needed—just elegant rooms, sleek chat, and smooth streaming.</p>
      <div class="d-flex flex-wrap gap-3 mb-3">
        <button class="btn btn-light text-primary fw-semibold rounded-pill px-4 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#joinModal">
          Join Room <i class="bi bi-plus-circle"></i>
        </button>
        <a href="room.php" class="btn btn-outline-light fw-semibold rounded-pill px-4 d-flex align-items-center gap-2">
          Create P2P Room <i class="bi bi-arrow-right"></i>
        </a>
      </div>
      <div class="d-flex flex-wrap gap-2 small">
        <span class="badge bg-white bg-opacity-10 border border-white border-opacity-25">Live encryption</span>
        <span class="badge bg-white bg-opacity-10 border border-white border-opacity-25">Zero storage</span>
        <span class="badge bg-white bg-opacity-10 border border-white border-opacity-25">One-click join</span>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="bg-white bg-opacity-10 rounded-4 p-4 glass-card shadow-lg">
        <div class="border border-white border-opacity-25 rounded-4 p-3 text-white">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <small class="text-white-50 text-uppercase">Live Room</small>
              <div class="fw-semibold">room-9d21 · 6 online</div>
            </div>
            <div class="d-flex align-items-center gap-2 text-white-50 small">
              <span class="badge bg-success">Secure</span>
            </div>
          </div>
          <div class="mt-4 vstack gap-3">
            <div class="d-flex gap-3">
              <div class="rounded-circle bg-white bg-opacity-25" style="width:42px;height:42px;"></div>
              <div class="flex-grow-1 rounded-4 bg-white bg-opacity-10 p-3">
                <div class="d-flex justify-content-between small text-white-50"><span>Willow</span><span>10:24</span></div>
                <p class="mb-0">Checking latency. Looks smooth! 🔥</p>
              </div>
            </div>
            <div class="d-flex gap-3">
              <div class="rounded-circle bg-white bg-opacity-25" style="width:42px;height:42px;"></div>
              <div class="flex-grow-1 rounded-4 p-3" style="background:rgba(15,23,42,0.45);">
                <div class="d-flex justify-content-between small text-white-50"><span>You</span><span>10:25</span></div>
                <p class="mb-0">Encryption handshake completed.</p>
              </div>
            </div>
            <div class="text-center text-uppercase small fw-semibold text-white-50">End-to-end encrypted</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="mt-5">
  <div class="row g-4">
    <?php
    $features = [
      ['title' => 'Speed-first networking', 'desc' => 'Direct peer relays with smart region routing to keep latency low.', 'icon' => 'bi-lightning-charge-fill'],
      ['title' => 'Privacy native', 'desc' => 'No centralized storage. Rooms dissolve on exit with ephemeral presence.', 'icon' => 'bi-shield-lock-fill'],
      ['title' => 'No signup needed', 'desc' => 'Spin up secure rooms instantly, share the ID, and start collaborating.', 'icon' => 'bi-stars'],
      ['title' => 'Team-friendly', 'desc' => 'Invite-only controls, typed chat, and handoff ready for any device.', 'icon' => 'bi-people-fill'],
      ['title' => 'Responsive by default', 'desc' => 'A mobile-first experience that feels like a native app.', 'icon' => 'bi-phone'],
      ['title' => 'Theme aware', 'desc' => 'Auto dark/light with custom gradients and adaptive glassy surfaces.', 'icon' => 'bi-moon-stars-fill'],
    ];
    foreach ($features as $feature): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0 rounded-4">
          <div class="card-body d-flex gap-3">
            <span class="btn btn-light rounded-4 p-3 fs-5 text-primary"><i class="bi <?= $feature['icon']; ?>"></i></span>
            <div>
              <h6 class="fw-semibold mb-2"><?= $feature['title']; ?></h6>
              <p class="mb-0 text-body-secondary small"><?= $feature['desc']; ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="mt-5 row g-4">
  <div class="col-lg-6">
    <div class="card shadow-sm rounded-4 border-0 h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="text-primary fw-semibold mb-1">FAQ</p>
            <h5 class="mb-0">Everything you need to know</h5>
          </div>
          <span class="badge bg-primary-subtle text-primary fw-semibold">Updated weekly</span>
        </div>
        <div class="accordion" id="faqAccordion">
          <?php
          $faqs = [
            ['q' => 'How secure are the rooms?', 'a' => 'Rooms use peer-to-peer encryption with no central data store. Identities stay on device.'],
            ['q' => 'Do I need to create an account?', 'a' => 'No accounts required. Share your room ID to collaborate instantly.'],
            ['q' => 'Can I switch themes?', 'a' => 'Use the toggle in the navbar. The UI respects your system preference automatically.'],
          ];
          foreach ($faqs as $index => $faq):
            $collapseId = 'faq-' . $index;
          ?>
          <div class="accordion-item border-0">
            <h2 class="accordion-header" id="heading-<?= $collapseId; ?>">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId; ?>" aria-expanded="false" aria-controls="<?= $collapseId; ?>">
                <?= $faq['q']; ?>
              </button>
            </h2>
            <div id="<?= $collapseId; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $collapseId; ?>" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-body-secondary small">
                <?= $faq['a']; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm rounded-4 border-0 h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="text-primary fw-semibold mb-1">Quick Actions</p>
            <h5 class="mb-0">Start collaborating</h5>
          </div>
          <span class="badge bg-success-subtle text-success fw-semibold">Online</span>
        </div>
        <div class="vstack gap-3">
          <button class="btn btn-outline-primary d-flex justify-content-between align-items-center rounded-3" data-bs-toggle="modal" data-bs-target="#joinModal">
            Join with a Room ID <i class="bi bi-plus-circle"></i>
          </button>
          <a href="rooms.php" class="btn btn-outline-secondary d-flex justify-content-between align-items-center rounded-3">
            View live rooms <i class="bi bi-columns-gap"></i>
          </a>
          <a href="profile.php" class="btn btn-outline-secondary d-flex justify-content-between align-items-center rounded-3">
            Update your profile <i class="bi bi-person-circle"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
renderJoinModal();
endPage();
?>
