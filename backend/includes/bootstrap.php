<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/responses.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/guest.php';
require_once __DIR__ . '/rooms.php';
require_once __DIR__ . '/users.php';
require_once __DIR__ . '/storage.php';

load_env(__DIR__ . '/../../.env');

if (session_status() === PHP_SESSION_NONE) {
    session_name('nshare_session');
    session_start([
        'cookie_lifetime' => 86400 * 30,
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}
