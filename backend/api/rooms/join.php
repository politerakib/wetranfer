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
$roomId = $payload['roomId'] ?? null;

if (!$roomId) {
    json_error('Missing roomId');
}

try {
    $room = rooms_get_room($roomId);
} catch (Throwable $throwable) {
    json_error($throwable->getMessage(), 404);
}

$user = auth_user();
$uid = $user['uid'] ?? null;

if ($room['type'] === 'team' && !$uid) {
    json_error('Login required for team rooms', 401);
}

if (!$uid) {
    $uid = guest_uid();
    users_ensure_guest($uid);
}

rooms_ensure_single_active_room($uid);
rooms_add_member($roomId, $uid, $room['owner_uid'] === $uid ? 'owner' : 'member');

json_success([
    'room' => rooms_get_room($roomId),
    'uid' => $uid,
]);
