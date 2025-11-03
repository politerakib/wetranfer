<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    json_error('Invalid JSON payload');
}
$idToken = $input['idToken'] ?? null;
$remember = (bool)($input['remember'] ?? false);

if (!$idToken) {
    json_error('Missing idToken');
}

try {
    $user = auth_login_with_token($idToken, $remember);
    json_success(['user' => $user]);
} catch (Throwable $throwable) {
    json_error($throwable->getMessage(), 401);
}
