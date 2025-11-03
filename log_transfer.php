<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$roomId = sanitize_input($input['roomId'] ?? '');
$fileName = sanitize_input($input['fileName'] ?? '');
$fileType = sanitize_input($input['fileType'] ?? '');
$userId = (int)($input['senderId'] ?? 0);

if (!$roomId || !$fileName || !$userId) {
    http_response_code(422);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

log_file_transfer($mysqli, $roomId, $userId, $fileName, $fileType);

echo json_encode(['status' => 'ok']);
