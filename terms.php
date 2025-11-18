<?php
require_once __DIR__ . '/partials/layout.php';
startPage('Terms', true);
?>
<section class="vstack gap-3">
  <p class="text-primary fw-semibold mb-1">Terms & FAQ</p>
  <h1 class="h3 fw-bold mb-1">Use NovaRooms responsibly</h1>
  <div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body">
      <h5 class="fw-semibold">FAQ</h5>
      <div class="accordion mt-3" id="termsAccordion">
        <?php
        $items = [
          ['q' => 'What data do you store?', 'a' => 'Rooms are peer-to-peer; messages and media are not stored on our servers.'],
          ['q' => 'How long do rooms last?', 'a' => 'Rooms dissolve when everyone leaves. Share the ID to rejoin or create a fresh room.'],
          ['q' => 'Can I report abuse?', 'a' => 'Yes—reach out through support and include your room ID.'],
        ];
        foreach ($items as $index => $item):
          $id = 'terms-item-' . $index;
        ?>
        <div class="accordion-item border-0">
          <h2 class="accordion-header" id="heading-<?= $id; ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $id; ?>" aria-expanded="false" aria-controls="<?= $id; ?>">
              <?= $item['q']; ?>
            </button>
          </h2>
          <div id="<?= $id; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $id; ?>" data-bs-parent="#termsAccordion">
            <div class="accordion-body text-body-secondary small">
              <?= $item['a']; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php
endPage();
?>
