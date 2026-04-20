<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$userId = require_login();
$db = get_db();

$stmt = $db->prepare('SELECT salutation, first_name, last_name, email, username FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
$db->close();

if (!$data) {
    send_json(['status' => 'error', 'message' => 'Profil nicht gefunden'], 404);
}

send_json(['status' => 'success', 'data' => $data]);

