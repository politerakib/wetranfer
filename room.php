<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

$user = ensure_user($mysqli);
$roomId = isset($_GET['room']) ? sanitize_input($_GET['room']) : '';

if (!$roomId) {
    header('Location: index.php');
    exit;
}

$requestedType = isset($_GET['type']) ? sanitize_input($_GET['type']) : 'direct';
$defaultMode = $requestedType === 'team' ? 'store' : 'webrtc';
ensure_room($mysqli, $roomId, $requestedType, $defaultMode);
$roomDetails = get_room_details($mysqli, $roomId);

$roomType = $roomDetails['room_type'] ?? 'direct';
$transferMode = $roomDetails['transfer_mode'] ?? $defaultMode;
$history = fetch_room_history($mysqli, $roomId);

$peerConfigRaw = [
    'host' => getenv('PEER_HOST') !== false ? getenv('PEER_HOST') : null,
    'port' => getenv('PEER_PORT') !== false ? getenv('PEER_PORT') : null,
    'path' => getenv('PEER_PATH') !== false ? getenv('PEER_PATH') : null,
    'secure' => getenv('PEER_SECURE') !== false ? getenv('PEER_SECURE') : null
];
$peerConfig = array_filter($peerConfigRaw, function ($value) {
    return $value !== null && $value !== '';
});
if (empty($peerConfig)) {
    $peerConfig = null;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Room <?php echo htmlspecialchars($roomId); ?> - Realtime Transfer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
    <header class="border-bottom sticky-top bg-body">
        <nav class="navbar navbar-expand-lg bg-body py-3">
            <div class="container-fluid px-4">
                <a class="navbar-brand fw-semibold" href="index.php">
                    <i class="fa-solid fa-paper-plane text-primary me-2"></i>Realtime Room
                </a>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary-subtle text-primary">
                        Room ID: <?php echo htmlspecialchars($roomId); ?>
                    </span>
                    <span class="user-badge text-secondary">
                        <i class="fa-solid fa-circle-user"></i>
                        <?php echo htmlspecialchars($user['display_name']); ?>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <main class="container-fluid py-4">
        <div class="row g-4">
            <aside class="col-12 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="fw-semibold"><i class="fa-solid fa-users me-2"></i>Active Participants</span>
                        <span class="badge bg-secondary" id="userCount">0</span>
                    </div>
                    <ul class="list-group list-group-flush" id="userList"></ul>
                </div>
            </aside>
            <section class="col-12 col-lg-9">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <span class="badge bg-info-subtle text-info" id="modeStatus">
                                <i class="fa-solid fa-arrows-rotate me-1"></i>
                                Transfer mode: <?php echo $transferMode === 'store' ? 'Server storage' : 'Realtime WebRTC'; ?>
                            </span>
                            <?php if ($roomType === 'direct') : ?>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="modeToggle" <?php echo $transferMode === 'store' ? 'checked' : ''; ?>>
                                    <label class="form-check-label small" for="modeToggle">
                                        Store files on server
                                    </label>
                                </div>
                            <?php else : ?>
                                <span class="text-secondary small">
                                    <i class="fa-solid fa-circle-info me-1"></i>Team rooms use server storage for sharing.
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="drag-drop-zone" id="dropZone">
                            <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-2"></i>
                            <p class="mb-0">Drag & drop files here or <span class="text-primary">click to browse</span></p>
                            <input type="file" id="fileInput" multiple hidden>
                        </div>
                        <div class="chat-feed flex-grow-1" id="chatFeed"></div>
                        <div class="input-group">
                            <input type="text" id="messageInput" class="form-control" placeholder="Send a message to the room">
                            <button class="btn btn-primary" id="sendMessageBtn">Send</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div class="toast align-items-center text-bg-success border-0" id="successToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>File sent successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        window.APP_CONFIG = {
            apiBase: '<?php echo sanitize_input(getenv('NODE_API_BASE') ?: 'http://localhost:3000'); ?>',
            roomId: '<?php echo htmlspecialchars($roomId); ?>',
            user: <?php echo json_encode($user); ?>,
            history: <?php echo json_encode($history); ?>,
            roomType: '<?php echo htmlspecialchars($roomType); ?>',
            transferMode: '<?php echo htmlspecialchars($transferMode); ?>',
            messageEndpoint: 'room_message.php',
            peerConfig: <?php echo json_encode($peerConfig); ?>
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/socket.io-client@4.7.5/dist/socket.io.min.js"></script>
    <script src="https://unpkg.com/peerjs@1.5.5/dist/peerjs.min.js"></script>
    <script src="assets/js/room-context.js" defer></script>
    <script src="assets/js/room-ui.js" defer></script>
    <script src="assets/js/room-webrtc.js" defer></script>
    <script src="assets/js/room-socket.js" defer></script>
    <script src="assets/js/room-main.js" defer></script>
</body>
</html>
