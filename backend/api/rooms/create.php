<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload)) {
    json_error('Invalid JSON payload');
}
$type = $payload['type'] ?? 'p2p';
$title = $payload['title'] ?? null;

if (!in_array($type, ['p2p', 'team'], true)) {
    json_error('Unsupported room type');
}

$user = auth_user();
$uid = $user['uid'] ?? null;

if ($type === 'team' && !$uid) {
    json_error('Login required for team rooms', 401);
}

if (!$uid) {
    $uid = guest_uid();
    users_ensure_guest($uid);
}

rooms_ensure_single_active_room($uid);

try {
    $room = rooms_create_room($type, $uid, $title);
    json_success(['room' => $room, 'uid' => $uid]);
} catch (Throwable $throwable) {
    json_error($throwable->getMessage(), 500);
}
