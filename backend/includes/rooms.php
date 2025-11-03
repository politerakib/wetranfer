<?php

function rooms_create_room(string $type, string $ownerUid, ?string $title = null): array
{
    $db = db();
    $roomId = bin2hex(random_bytes(6));
    $stmt = $db->prepare('INSERT INTO rooms (room_id, owner_uid, type, title, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->bind_param('ssss', $roomId, $ownerUid, $type, $title);
    $stmt->execute();

    rooms_add_member($roomId, $ownerUid, 'owner');

    return rooms_get_room($roomId);
}

function rooms_get_room(string $roomId): array
{
    $db = db();
    $stmt = $db->prepare('SELECT room_id, owner_uid, type, title, created_at FROM rooms WHERE room_id = ?');
    $stmt->bind_param('s', $roomId);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    if (!$room) {
        throw new \RuntimeException('Room not found.');
    }
    $room['members'] = rooms_list_members($roomId);
    return $room;
}

function rooms_add_member(string $roomId, string $uid, string $role = 'member'): void
{
    $db = db();
    $stmt = $db->prepare('INSERT INTO room_members (room_id, user_uid, role, joined_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE role = VALUES(role), joined_at = NOW(), left_at = NULL');
    $stmt->bind_param('sss', $roomId, $uid, $role);
    $stmt->execute();
}

function rooms_is_member(string $roomId, string $uid): bool
{
    $db = db();
    $stmt = $db->prepare('SELECT 1 FROM room_members WHERE room_id = ? AND user_uid = ? AND left_at IS NULL');
    $stmt->bind_param('ss', $roomId, $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    return (bool) $result->fetch_row();
}

function rooms_leave_room(string $roomId, string $uid): void
{
    $db = db();
    $stmt = $db->prepare('UPDATE room_members SET left_at = NOW() WHERE room_id = ? AND user_uid = ? AND left_at IS NULL');
    $stmt->bind_param('ss', $roomId, $uid);
    $stmt->execute();
}

function rooms_list_members(string $roomId): array
{
    $db = db();
    $stmt = $db->prepare('SELECT rm.user_uid, rm.role, rm.joined_at, rm.left_at, u.name, u.avatar_url FROM room_members rm JOIN users u ON u.uid = rm.user_uid WHERE rm.room_id = ? ORDER BY rm.joined_at ASC');
    $stmt->bind_param('s', $roomId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

function rooms_active_members(string $roomId): array
{
    $db = db();
    $stmt = $db->prepare('SELECT rm.user_uid, rm.role, rm.joined_at, u.name, u.avatar_url FROM room_members rm JOIN users u ON u.uid = rm.user_uid WHERE rm.room_id = ? AND rm.left_at IS NULL ORDER BY rm.joined_at ASC');
    $stmt->bind_param('s', $roomId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC) ?: [];
}

function rooms_attach_file(string $roomId, string $uid, string $filename, string $mime, int $size, string $storagePath): void
{
    $db = db();
    $stmt = $db->prepare('INSERT INTO room_files (room_id, user_uid, filename, mime_type, file_size, storage_path, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->bind_param('ssssis', $roomId, $uid, $filename, $mime, $size, $storagePath);
    $stmt->execute();
}

function rooms_prune_inactive(string $uid): void
{
    $db = db();
    $stmt = $db->prepare('UPDATE room_members SET left_at = NOW() WHERE user_uid = ? AND left_at IS NULL');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
}

function rooms_ensure_single_active_room(string $uid): void
{
    $db = db();
    $stmt = $db->prepare('SELECT room_id FROM room_members WHERE user_uid = ? AND left_at IS NULL ORDER BY joined_at DESC');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $rooms = $result->fetch_all(MYSQLI_ASSOC);
    if (!$rooms) {
        return;
    }

    foreach ($rooms as $room) {
        rooms_leave_room($room['room_id'], $uid);
    }
}

function rooms_active_room_for(string $uid): ?array
{
    $db = db();
    $stmt = $db->prepare('SELECT room_id FROM room_members WHERE user_uid = ? AND left_at IS NULL ORDER BY joined_at DESC LIMIT 1');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        return null;
    }

    return rooms_get_room($row['room_id']);
}
