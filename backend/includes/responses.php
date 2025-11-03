<?php

function json_success(array $data = [], int $status = 200): void
{
    json_send(['status' => 'ok'] + $data, $status);
}

function json_error(string $message, int $status = 400, array $context = []): void
{
    json_send(['status' => 'error', 'message' => $message, 'context' => $context], $status);
}

function json_send(array $payload, int $status): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    echo json_encode($payload);
    exit;
}
