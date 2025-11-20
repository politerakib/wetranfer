<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Login', false);
?>
<section class="d-flex justify-content-center">
  <div class="card border-0 shadow-sm rounded-4" style="max-width:480px; width:100%;">
    <div class="card-body p-4 p-lg-5">
      <div class="text-center mb-4">
        <div class="rounded-3 text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-3" style="width:50px;height:50px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">P2P</div>
        <h4 class="fw-semibold mb-1">Welcome back</h4>
        <p class="text-body-secondary small mb-0">Log in to continue to your rooms.</p>
      </div>
      <form class="vstack gap-3">
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
        <div class="d-flex justify-content-between align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <a href="#" class="small link-primary">Forgot password?</a>
        </div>
        <button class="btn btn-primary rounded-pill w-100">Log in</button>
        <button class="btn btn-outline-secondary rounded-pill w-100 d-flex justify-content-center align-items-center gap-2" type="button">
          <i class="bi bi-google"></i> Continue with Google
        </button>
      </form>
      <p class="text-center text-body-secondary small mt-3 mb-0">No account? <a href="signup.php" class="link-primary">Sign up</a></p>
    </div>
  </div>
</section>
<?php
endPage();
?>
