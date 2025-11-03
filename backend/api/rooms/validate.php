<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error('Method not allowed', 405);
}

$uid = auth_user()['uid'] ?? null;
if (!$uid) {
    $uid = guest_uid();
    users_ensure_guest($uid);
}

$room = rooms_active_room_for($uid);

json_success(['room' => $room, 'uid' => $uid]);
