<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitize_input($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function ensure_user(mysqli $mysqli)
{
    if (!isset($_SESSION['user_id'])) {
        $displayName = 'User_' . substr(bin2hex(random_bytes(4)), 0, 8);
        $stmt = mysqli_prepare($mysqli, 'INSERT INTO users (display_name, created_at) VALUES (?, NOW())');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $displayName);
            mysqli_stmt_execute($stmt);
            $_SESSION['user_id'] = mysqli_insert_id($mysqli);
            $_SESSION['display_name'] = $displayName;
            mysqli_stmt_close($stmt);
        }
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'display_name' => $_SESSION['display_name'] ?? 'Guest'
    ];
}

function ensure_room(mysqli $mysqli, $roomId, $roomType = 'direct', $transferMode = 'webrtc')
{
    $stmt = mysqli_prepare($mysqli, 'SELECT room_type, transfer_mode FROM rooms WHERE room_id = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $roomId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$existing) {
            $stmtInsert = mysqli_prepare($mysqli, 'INSERT INTO rooms (room_id, room_type, transfer_mode, created_at) VALUES (?, ?, ?, NOW())');
            if ($stmtInsert) {
                mysqli_stmt_bind_param($stmtInsert, 'sss', $roomId, $roomType, $transferMode);
                mysqli_stmt_execute($stmtInsert);
                mysqli_stmt_close($stmtInsert);
            }
        }
    }
}

function get_room_details(mysqli $mysqli, $roomId)
{
    $stmt = mysqli_prepare($mysqli, 'SELECT room_id, room_type, transfer_mode FROM rooms WHERE room_id = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $roomId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $room = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $room ?: null;
    }

    return null;
}

function update_room_transfer_mode(mysqli $mysqli, $roomId, $transferMode)
{
    $stmt = mysqli_prepare($mysqli, 'UPDATE rooms SET transfer_mode = ? WHERE room_id = ?');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $transferMode, $roomId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function log_room_message(mysqli $mysqli, array $payload)
{
    $query = 'INSERT INTO room_messages (room_id, user_id, message_type, message_text, file_name, file_path, file_type, transfer_mode, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    $stmt = mysqli_prepare($mysqli, $query);

    if ($stmt) {
        $roomId = $payload['room_id'];
        $userId = $payload['user_id'];
        $messageType = $payload['message_type'];
        $messageText = $payload['message_text'] ?? null;
        $fileName = $payload['file_name'] ?? null;
        $filePath = $payload['file_path'] ?? null;
        $fileType = $payload['file_type'] ?? null;
        $transferMode = $payload['transfer_mode'] ?? 'webrtc';

        mysqli_stmt_bind_param(
            $stmt,
            'sissssss',
            $roomId,
            $userId,
            $messageType,
            $messageText,
            $fileName,
            $filePath,
            $fileType,
            $transferMode
        );
        mysqli_stmt_execute($stmt);
        $insertId = mysqli_insert_id($mysqli);
        mysqli_stmt_close($stmt);

        $payload['id'] = $insertId;

        $stmtSelect = mysqli_prepare($mysqli, 'SELECT created_at FROM room_messages WHERE id = ? LIMIT 1');
        if ($stmtSelect) {
            mysqli_stmt_bind_param($stmtSelect, 'i', $insertId);
            mysqli_stmt_execute($stmtSelect);
            $result = mysqli_stmt_get_result($stmtSelect);
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $payload['created_at'] = $row['created_at'];
            }
            mysqli_stmt_close($stmtSelect);
        }

        return $payload;
    }

    return $payload;
}

function fetch_room_history(mysqli $mysqli, $roomId)
{
    $history = [];
    $query = 'SELECT rm.id, rm.message_type, rm.message_text, rm.file_name, rm.file_path, rm.file_type, rm.transfer_mode, rm.created_at, u.display_name, u.id as sender_id FROM room_messages rm JOIN users u ON u.id = rm.user_id WHERE rm.room_id = ? ORDER BY rm.created_at ASC';
    $stmt = mysqli_prepare($mysqli, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $roomId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $history[] = $row;
        }
        mysqli_stmt_close($stmt);
    }

    return $history;
}
?>
