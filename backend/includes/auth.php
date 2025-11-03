<?php

function auth_login_with_token(string $idToken, bool $remember = false): array
{
    $payload = auth_verify_firebase_token($idToken);
    $user = [
        'uid' => $payload['uid'] ?? $payload['localId'] ?? $payload['sub'] ?? null,
        'email' => $payload['email'] ?? null,
        'name' => $payload['displayName'] ?? $payload['name'] ?? 'N-Share User',
        'avatar' => $payload['photoUrl'] ?? $payload['picture'] ?? null,
    ];

    if (!$user['uid'] || !$user['email']) {
        throw new \RuntimeException('Invalid token payload');
    }

    $db = db();
    $stmt = $db->prepare('INSERT INTO users (uid, email, name, avatar_url, last_login_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE email = VALUES(email), name = VALUES(name), avatar_url = VALUES(avatar_url), last_login_at = NOW()');
    $stmt->bind_param('ssss', $user['uid'], $user['email'], $user['name'], $user['avatar']);
    $stmt->execute();

    $_SESSION['user'] = $user;
    $_SESSION['remember_me'] = $remember;

    if ($remember) {
        setcookie('nshare_remember', $idToken, time() + 60 * 60 * 24 * 30, '/', '', false, true);
    }

    return $user;
}

function auth_remember_from_cookie(): ?array
{
    if (!empty($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    $token = $_COOKIE['nshare_remember'] ?? null;
    if (!$token) {
        return null;
    }

    try {
        return auth_login_with_token($token, true);
    } catch (\Throwable $exception) {
        auth_logout();
        return null;
    }
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_ensure_logged_in(): array
{
    $user = auth_user();
    if ($user) {
        return $user;
    }

    $user = auth_remember_from_cookie();
    if ($user) {
        return $user;
    }

    json_error('Authentication required', 401);
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    setcookie('nshare_remember', '', time() - 3600, '/');
}

function auth_verify_firebase_token(string $idToken): array
{
    $apiKey = env_get('FIREBASE_API_KEY');
    if (!$apiKey) {
        throw new \RuntimeException('Firebase API key is not configured.');
    }

    $endpoint = sprintf('https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=%s', urlencode($apiKey));
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode(['idToken' => $idToken]),
            'timeout' => 10,
        ],
    ]);

    $response = @file_get_contents($endpoint, false, $context);
    if ($response === false) {
        throw new \RuntimeException('Unable to validate Firebase token.');
    }

    $data = json_decode($response, true);
    if (empty($data['users'][0])) {
        throw new \RuntimeException('Invalid token payload');
    }

    return $data['users'][0];
}
