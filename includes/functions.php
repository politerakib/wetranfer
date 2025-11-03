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

function ensure_room(mysqli $mysqli, $roomId, $roomType = 'direct')
{
    $stmt = mysqli_prepare($mysqli, 'SELECT id FROM rooms WHERE room_id = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $roomId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$exists) {
            $stmtInsert = mysqli_prepare($mysqli, 'INSERT INTO rooms (room_id, room_type, created_at) VALUES (?, ?, NOW())');
            if ($stmtInsert) {
                mysqli_stmt_bind_param($stmtInsert, 'ss', $roomId, $roomType);
                mysqli_stmt_execute($stmtInsert);
                mysqli_stmt_close($stmtInsert);
            }
        }
    }
}

function log_file_transfer(mysqli $mysqli, $roomId, $userId, $fileName, $fileType)
{
    $query = 'INSERT INTO file_logs (room_id, user_id, file_name, file_type, created_at) VALUES (?, ?, ?, ?, NOW())';
    $stmt = mysqli_prepare($mysqli, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'siss', $roomId, $userId, $fileName, $fileType);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function fetch_room_history(mysqli $mysqli, $roomId)
{
    $history = [];
    $query = 'SELECT fl.file_name, fl.file_type, fl.created_at, u.display_name FROM file_logs fl JOIN users u ON u.id = fl.user_id WHERE fl.room_id = ? ORDER BY fl.created_at ASC';
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
