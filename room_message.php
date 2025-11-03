<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

$user = ensure_user($mysqli);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

header('Content-Type: application/json');

function respond_and_exit($status, $payload)
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

function build_response(array $record)
{
    global $user;
    return [
        'id' => $record['id'] ?? null,
        'room_id' => $record['room_id'],
        'sender_id' => $record['user_id'],
        'display_name' => $user['display_name'] ?? ($_SESSION['display_name'] ?? 'Guest'),
        'message_type' => $record['message_type'],
        'message_text' => $record['message_text'] ?? null,
        'file_name' => $record['file_name'] ?? null,
        'file_path' => $record['file_path'] ?? null,
        'file_type' => $record['file_type'] ?? null,
        'transfer_mode' => $record['transfer_mode'] ?? 'webrtc',
        'created_at' => $record['created_at'] ?? date('Y-m-d H:i:s')
    ];
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'multipart/form-data') === 0) {
    $roomId = sanitize_input($_POST['roomId'] ?? '');
    $transferMode = sanitize_input($_POST['transferMode'] ?? 'store');
    $senderId = (int)($_POST['senderId'] ?? 0);

    if (!$roomId || !$senderId || !isset($_FILES['file'])) {
        respond_and_exit(422, ['error' => 'Missing required fields']);
    }

    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        respond_and_exit(400, ['error' => 'Failed to upload file']);
    }

    $safeName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file['name']);
    $uniqueName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $safeName;
    $targetPath = $uploadDir . '/' . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        respond_and_exit(500, ['error' => 'Unable to store file']);
    }

    $relativePath = 'uploads/' . $uniqueName;

    $record = [
        'room_id' => $roomId,
        'user_id' => $senderId,
        'message_type' => 'file',
        'message_text' => null,
        'file_name' => $file['name'],
        'file_path' => $relativePath,
        'file_type' => $file['type'] ?? 'application/octet-stream',
        'transfer_mode' => 'store'
    ];

    $logged = log_room_message($mysqli, $record);
    $response = build_response($logged);
    $response['file_path'] = $relativePath;
    $response['file_url'] = $relativePath;
    respond_and_exit(200, $response);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    respond_and_exit(400, ['error' => 'Invalid JSON payload']);
}

$roomId = sanitize_input($input['roomId'] ?? '');
$messageType = sanitize_input($input['messageType'] ?? 'text');
$senderId = (int)($input['senderId'] ?? 0);
$transferMode = sanitize_input($input['transferMode'] ?? 'webrtc');

if (!$roomId || !$senderId) {
    respond_and_exit(422, ['error' => 'Missing room or sender information']);
}

if ($messageType === 'text') {
    $message = trim($input['message'] ?? '');
    if ($message === '') {
        respond_and_exit(422, ['error' => 'Message body required']);
    }

    $record = [
        'room_id' => $roomId,
        'user_id' => $senderId,
        'message_type' => 'text',
        'message_text' => $message,
        'transfer_mode' => $transferMode
    ];

    $logged = log_room_message($mysqli, $record);
    $response = build_response($logged);
    $response['display_name'] = $_SESSION['display_name'] ?? 'Guest';
    respond_and_exit(200, $response);
}

if ($messageType === 'file') {
    $fileName = sanitize_input($input['fileName'] ?? '');
    $fileType = sanitize_input($input['fileType'] ?? 'application/octet-stream');

    if ($fileName === '') {
        respond_and_exit(422, ['error' => 'File name required']);
    }

    $record = [
        'room_id' => $roomId,
        'user_id' => $senderId,
        'message_type' => 'file',
        'file_name' => $fileName,
        'file_type' => $fileType,
        'file_path' => null,
        'transfer_mode' => $transferMode === 'store' ? 'store' : 'webrtc'
    ];

    $logged = log_room_message($mysqli, $record);
    $response = build_response($logged);
    $response['display_name'] = $_SESSION['display_name'] ?? 'Guest';
    respond_and_exit(200, $response);
}

respond_and_exit(422, ['error' => 'Unsupported message type']);
