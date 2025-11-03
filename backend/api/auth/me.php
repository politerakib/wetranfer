<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

$user = auth_user();
if (!$user) {
    json_error('Not authenticated', 401);
}

json_success(['user' => $user]);
