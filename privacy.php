<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Privacy', true);
?>
<section class="vstack gap-3">
  <p class="text-primary fw-semibold mb-1">Privacy</p>
  <h1 class="h3 fw-bold mb-1">We keep your sessions private</h1>
  <div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body">
      <div class="vstack gap-3 text-body-secondary small">
        <p>NovaRooms is designed to minimize stored data. Rooms operate peer-to-peer, and we avoid logging content or metadata beyond what is required for abuse prevention.</p>
        <p>We do not sell personal data or inject ads. Session information is ephemeral and tied to your active room.</p>
        <p>For compliance questions, contact our team and include your room identifier.</p>
      </div>
    </div>
  </div>
</section>
<?php
endPage();
?>
