<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error('Method not allowed', 405);
}

$roomId = $_GET['roomId'] ?? null;
if (!$roomId) {
    json_error('Missing roomId');
}

try {
    $members = rooms_active_members($roomId);
    json_success(['members' => $members]);
} catch (Throwable $throwable) {
    json_error($throwable->getMessage(), 404);
}
