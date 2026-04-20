<?php

declare(strict_types=1);

function get_db(): mysqli
{
    $host = getenv('KD_DB_HOST') ?: '127.0.0.1';
    $user = getenv('KD_DB_USER') ?: 'root';
    $password = getenv('KD_DB_PASS') ?: '';
    $database = getenv('KD_DB_NAME') ?: 'kickdistrict';

    $db = new mysqli($host, $user, $password, $database);
    if ($db->connect_errno) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => 'Datenbankverbindung fehlgeschlagen',
        ]);
        exit;
    }

    $db->set_charset('utf8mb4');
    return $db;
}

