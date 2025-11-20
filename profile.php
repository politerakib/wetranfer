<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Profile', true, ['name' => 'Skyler Dawn', 'email' => 'skyler@nrooms.com']);
?>
<section class="row g-4">
  <div class="col-lg-4">
    <div class="card border-0 rounded-4 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="rounded-circle text-white fw-bold mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">SD</div>
        <h5 class="fw-semibold mb-1">Skyler Dawn</h5>
        <p class="text-body-secondary small mb-3">team@novarooms.com</p>
        <div class="d-grid gap-2">
          <button class="btn btn-outline-secondary rounded-pill">Update info</button>
          <button class="btn btn-outline-primary rounded-pill">Change password</button>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-8 vstack gap-4">
    <div class="card border-0 rounded-4 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="text-primary fw-semibold mb-1">Profile</p>
            <h5 class="mb-0">Account details</h5>
          </div>
          <span class="badge bg-success-subtle text-success fw-semibold">Verified</span>
        </div>
        <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold small" for="name">Full name</label>
            <input id="name" type="text" class="form-control rounded-3" value="Skyler Dawn">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small" for="email">Email</label>
            <input id="email" type="email" class="form-control rounded-3" value="skyler@nrooms.com">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small" for="avatar">Avatar URL</label>
            <input id="avatar" type="url" class="form-control rounded-3" placeholder="https://...">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small" for="timezone">Timezone</label>
            <select id="timezone" class="form-select rounded-3">
              <option selected>UTC</option>
              <option>GMT</option>
              <option>PST</option>
              <option>EST</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small" for="about">About</label>
            <textarea id="about" class="form-control rounded-3" rows="3" placeholder="Say hello to your collaborators"></textarea>
          </div>
          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill">Save changes</button>
          </div>
        </form>
      </div>
    </div>
    <div class="card border-0 rounded-4 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <p class="text-primary fw-semibold mb-1">Security</p>
            <h6 class="mb-0">Password & sessions</h6>
          </div>
          <span class="badge bg-warning-subtle text-warning fw-semibold">Review</span>
        </div>
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label fw-semibold small" for="password">New password</label>
            <div class="input-group">
              <input id="password" type="password" class="form-control rounded-start-3" placeholder="••••••••">
              <button class="btn btn-outline-secondary" type="button" data-password-toggle="password">
                <i data-eye-open class="bi bi-eye"></i>
                <i data-eye-closed class="bi bi-eye-slash d-none"></i>
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small" for="confirm">Confirm password</label>
            <input id="confirm" type="password" class="form-control rounded-3" placeholder="••••••••">
          </div>
          <div class="col-12">
            <button class="btn btn-primary rounded-pill">Update password</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
renderJoinModal();
endPage();
?>
