<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Signup', false);
?>
<section class="d-flex justify-content-center">
  <div class="card border-0 shadow-sm rounded-4" style="max-width:480px; width:100%;">
    <div class="card-body p-4 p-lg-5">
      <div class="text-center mb-4">
        <div class="rounded-3 text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-3" style="width:50px;height:50px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">P2P</div>
        <h4 class="fw-semibold mb-1">Create an account</h4>
        <p class="text-body-secondary small mb-0">Join NovaRooms and start collaborating.</p>
      </div>
      <form class="vstack gap-3">
        <div>
          <label class="form-label fw-semibold small" for="name">Full name</label>
          <input id="name" type="text" class="form-control rounded-3" placeholder="Skyler Dawn" required>
        </div>
        <div>
          <label class="form-label fw-semibold small" for="email">Email</label>
          <input id="email" type="email" class="form-control rounded-3" placeholder="you@example.com" required>
        </div>
        <div>
          <label class="form-label fw-semibold small" for="password">Password</label>
          <div class="input-group">
            <input id="password" type="password" class="form-control rounded-start-3" placeholder="••••••••" required>
            <button class="btn btn-outline-secondary" type="button" data-password-toggle="password">
              <i data-eye-open class="bi bi-eye"></i>
              <i data-eye-closed class="bi bi-eye-slash d-none"></i>
            </button>
          </div>
        </div>
        <button class="btn btn-primary rounded-pill w-100">Sign up</button>
        <button class="btn btn-outline-secondary rounded-pill w-100 d-flex justify-content-center align-items-center gap-2" type="button">
          <i class="bi bi-google"></i> Sign up with Google
        </button>
      </form>
      <p class="text-center text-body-secondary small mt-3 mb-0">Already have an account? <a href="login.php" class="link-primary">Log in</a></p>
    </div>
  </div>
</section>
<?php
endPage();
?>
