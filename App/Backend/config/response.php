<?php

declare(strict_types=1);

function send_json(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

function read_json_input(): array
{
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function clean_string(mixed $value): string
{
    return htmlspecialchars(trim((string) ($value ?? '')), ENT_QUOTES, 'UTF-8');
}

function require_login(): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        send_json([
            'status' => 'error',
            'message' => 'Nicht angemeldet',
        ], 401);
    }

    return (int) $_SESSION['user_id'];
}

