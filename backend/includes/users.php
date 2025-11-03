<?php

function users_ensure_guest(string $uid): void
{
    $db = db();
    $label = 'Guest ' . substr($uid, -4);
    $stmt = $db->prepare('INSERT IGNORE INTO users (uid, email, name, avatar_url, last_login_at) VALUES (?, ?, ?, NULL, NOW())');
    $email = sprintf('%s@nshare.local', $uid);
    $stmt->bind_param('sss', $uid, $email, $label);
    $stmt->execute();
}
