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

$uid = auth_user()['uid'] ?? null;
if (!$uid) {
    $uid = guest_uid();
}

rooms_leave_room($roomId, $uid);

json_success();
