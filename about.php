<?php
require_once __DIR__ . '/partials/layout.php';
startPage('About', true, ['name' => 'Skyler Dawn']);
?>
<section class="vstack gap-3">
  <p class="text-primary fw-semibold mb-1">About NovaRooms</p>
  <h1 class="h3 fw-bold mb-1">Premium peer-to-peer collaboration, thoughtfully designed.</h1>
  <p class="lead text-body-secondary">NovaRooms is a privacy-first room system inspired by modern tools like WhatsApp Web and Discord. We obsess over micro-interactions, responsive layouts, and an adaptive design system you can lift directly into PHP.</p>
  <div class="row g-4 mt-2">
    <div class="col-md-6">
      <div class="card border-0 rounded-4 shadow-sm h-100">
        <div class="card-body">
          <h5 class="fw-semibold">Principles</h5>
          <ul class="mt-3 text-body-secondary small list-unstyled mb-0">
            <li>• Privacy-first with end-to-end thinking</li>
            <li>• Responsive by default, from mobile to desktop</li>
            <li>• Components that can be reused across PHP layouts</li>
            <li>• Cohesive typography and spacing tokens</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 rounded-4 shadow-sm h-100">
        <div class="card-body">
          <h5 class="fw-semibold">Stack-ready</h5>
          <p class="mt-3 text-body-secondary small mb-0">The UI kit pairs Bootstrap styling with PHP components (header.php, footer.php, modal.php, layout.php) so you can slot in authentication, billing, and messaging logic instantly.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
endPage();
?>
