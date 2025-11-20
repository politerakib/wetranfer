<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Room · room-9d21', true, ['name' => 'Skyler Dawn']);
?>
<section class="vstack gap-4">
  <div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between">
      <div>
        <p class="text-primary fw-semibold mb-1">Room ID</p>
        <h1 class="h4 fw-bold mb-1">room-9d21</h1>
        <p class="text-body-secondary small mb-0">Encrypted chat and presence • 6 users online</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-outline-secondary rounded-pill">Copy Invite</button>
        <button class="btn btn-primary rounded-pill">Invite Users</button>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 rounded-4 shadow-sm h-100">
        <div class="card-header bg-body border-0 d-flex justify-content-between align-items-center">
          <span class="fw-semibold">Conversation</span>
          <span class="badge bg-success-subtle text-success fw-semibold">Live</span>
        </div>
        <div class="card-body bg-body-secondary rounded-bottom-4" style="max-height:460px; overflow-y:auto;">
          <div class="text-center text-uppercase small fw-semibold text-body-secondary mb-3">room created 10:20</div>
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="rounded-circle text-white fw-semibold d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">WD</div>
            <div class="chat-bubble p-3 bg-body text-body shadow-sm w-75">
              <div class="d-flex justify-content-between small text-body-secondary"><span>Willow D.</span><span>10:22</span></div>
              <p class="mb-0 mt-1">Anyone else seeing lower ping this morning?</p>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3 justify-content-end mb-3">
            <div class="chat-bubble p-3 text-white w-75" style="background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent)); box-shadow:0 10px 30px rgba(79,70,229,0.25);">
              <div class="d-flex justify-content-between small text-white-50"><span>You</span><span>10:23</span></div>
              <p class="mb-0 mt-1">Yes—relay switched to our edge in Paris.</p>
            </div>
            <div class="d-none d-lg-flex rounded-circle text-white fw-semibold align-items-center justify-content-center" style="width:42px;height:42px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">SD</div>
          </div>
          <div class="text-center text-uppercase small fw-semibold text-body-secondary mb-3">encryption upgraded</div>
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="rounded-circle text-white fw-semibold d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:linear-gradient(135deg,#10b981,#0ea5e9);">JT</div>
            <div class="chat-bubble p-3 bg-body text-body shadow-sm w-75">
              <div class="d-flex justify-content-between small text-body-secondary"><span>Jules T.</span><span>10:26</span></div>
              <p class="mb-0 mt-1">Streaming the slides now. Handoff is instant.</p>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3 justify-content-end mb-3">
            <div class="chat-bubble p-3 text-white w-75" style="background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent)); box-shadow:0 10px 30px rgba(79,70,229,0.25);">
              <div class="d-flex justify-content-between small text-white-50"><span>You</span><span>10:27</span></div>
              <p class="mb-0 mt-1">Audio is clean on my side.</p>
            </div>
            <div class="d-none d-lg-flex rounded-circle text-white fw-semibold align-items-center justify-content-center" style="width:42px;height:42px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">SD</div>
          </div>
          <div class="d-flex align-items-start gap-3 mb-3">
            <div class="rounded-circle text-white fw-semibold d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:linear-gradient(135deg,#0ea5e9,#22d3ee);">AR</div>
            <div class="chat-bubble p-3 bg-body text-body shadow-sm w-75">
              <div class="d-flex justify-content-between small text-body-secondary"><span>Ari R.</span><span>10:28</span></div>
              <p class="mb-0 mt-1">Love the new theme toggle ✨</p>
            </div>
          </div>
          <div class="text-center text-uppercase small fw-semibold text-body-secondary">end-to-end encrypted</div>
        </div>
        <div class="card-footer border-0 bg-body">
          <div class="d-flex align-items-center gap-2 p-2 rounded-3 border">
            <button class="btn btn-light rounded-circle" aria-label="Add emoji"><i class="bi bi-emoji-smile"></i></button>
            <input type="text" class="form-control border-0 bg-transparent" placeholder="Message this room">
            <button class="btn btn-primary rounded-pill px-4">Send</button>
          </div>
        </div>
      </div>
    </div>
    <aside class="col-lg-4">
      <div class="card border-0 rounded-4 shadow-sm h-100">
        <div class="card-body vstack gap-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="fw-semibold mb-1">Users in room</p>
              <p class="text-body-secondary small mb-0">6 connected</p>
            </div>
            <span class="badge bg-primary-subtle text-primary fw-semibold">Secure</span>
          </div>
          <div class="vstack gap-2">
            <?php
            $users = [
              ['name' => 'Skyler Dawn', 'status' => 'online'],
              ['name' => 'Willow Drew', 'status' => 'online'],
              ['name' => 'Ari Rivers', 'status' => 'online'],
              ['name' => 'Jules Terra', 'status' => 'away'],
              ['name' => 'Mira Sol', 'status' => 'online'],
              ['name' => 'Ren Vale', 'status' => 'online'],
            ];
            foreach ($users as $user): ?>
              <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border bg-body-secondary">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle text-white fw-semibold d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));">
                    <?= strtoupper(substr($user['name'], 0, 2)); ?>
                  </div>
                  <div>
                    <div class="fw-semibold small"><?= $user['name']; ?></div>
                    <div class="text-body-secondary text-capitalize small">Status: <?= $user['status']; ?></div>
                  </div>
                </div>
                <span class="rounded-circle <?= $user['status'] === 'online' ? 'bg-success' : 'bg-warning'; ?>" style="width:10px;height:10px;"></span>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="p-3 rounded-3 bg-body-secondary text-body-secondary small">
            <div class="fw-semibold text-body">Safety</div>
            <p class="mb-0">We verify peers silently and never store your media or text.</p>
          </div>
        </div>
      </div>
    </aside>
  </div>
</section>
<?php
renderJoinModal();
endPage();
?>
