<?php

declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'loggedin' => isset($_SESSION['user_id']),
    'username' => $_SESSION['username'] ?? null,
    'admin' => ($_SESSION['role'] ?? '') === 'admin',
]);

