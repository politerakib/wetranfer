<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

$path = $_GET['path'] ?? null;
$filename = $_GET['name'] ?? null;

if (!$path) {
    http_response_code(400);
    echo 'Missing file path';
    exit;
}

$storage = storage_path(basename($path));
if (!is_file($storage)) {
    http_response_code(404);
    echo 'File not found';
    exit;
}

$uid = auth_user()['uid'] ?? null;
if (!$uid) {
    $uid = guest_uid();
    users_ensure_guest($uid);
}

if (preg_match('/^(?P<room>[a-f0-9]{12})_/', basename($path), $matches)) {
    $roomId = $matches['room'];
    if (!rooms_is_member($roomId, $uid)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($storage));
header('Content-Disposition: attachment; filename="' . ($filename ? basename($filename) : basename($path)) . '"');
readfile($storage);
