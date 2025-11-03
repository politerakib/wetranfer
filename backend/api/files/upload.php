<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$roomId = $_POST['roomId'] ?? null;
if (!$roomId || empty($_FILES['file'])) {
    json_error('Invalid payload');
}

$uid = auth_user()['uid'] ?? null;
if (!$uid) {
    $uid = guest_uid();
    users_ensure_guest($uid);
}

if (!rooms_is_member($roomId, $uid)) {
    json_error('You are not part of this room', 403);
}

try {
    $stored = storage_store_uploaded_file($_FILES['file'], $roomId, $uid);
    rooms_attach_file($roomId, $uid, $_FILES['file']['name'], $_FILES['file']['type'] ?? 'application/octet-stream', (int) $_FILES['file']['size'], $stored);
    json_success([
        'filename' => $_FILES['file']['name'],
        'path' => $stored,
    ]);
} catch (Throwable $throwable) {
    json_error($throwable->getMessage(), 500);
}
