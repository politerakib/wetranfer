<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

ensure_user($mysqli);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$roomId = sanitize_input($input['roomId'] ?? '');
$transferMode = sanitize_input($input['transferMode'] ?? '');

if (!$roomId || !in_array($transferMode, ['webrtc', 'store'], true)) {
    http_response_code(422);
    echo json_encode(['error' => 'Missing or invalid parameters']);
    exit;
}

update_room_transfer_mode($mysqli, $roomId, $transferMode);

echo json_encode([
    'roomId' => $roomId,
    'transferMode' => $transferMode
]);
