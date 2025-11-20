<div class="modal fade" id="joinModal" tabindex="-1" aria-labelledby="joinModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0">
        <div>
          <p class="mb-1 text-secondary small">Join a P2P Room</p>
          <h5 class="mb-0 fw-semibold">Enter a room ID to hop in instantly.</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="vstack gap-3">
          <div>
            <label for="room-id" class="form-label small fw-semibold">Enter Room ID</label>
            <input id="room-id" type="text" class="form-control form-control-lg rounded-3" placeholder="room-9d21" required>
          </div>
          <button type="submit" class="btn btn-primary btn-lg rounded-3 w-100">Join Now</button>
          <div class="alert alert-success d-flex align-items-center gap-2 mb-0" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>Connected! Taking you to the room…</div>
          </div>
          <div class="alert alert-danger d-flex align-items-center gap-2 mb-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>Room not found. Double-check the ID.</div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
