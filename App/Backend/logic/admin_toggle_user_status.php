<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

$adminId = require_admin();
$data = read_json_input();
$id = (int) ($data['id'] ?? 0);
$active = (int) ($data['active'] ?? -1);

if ($id <= 0 || !in_array($active, [0, 1], true)) {
    send_json(['status' => 'error', 'message' => 'Ungültiger Status'], 422);
}

if ($id === $adminId && $active === 0) {
    send_json(['status' => 'error', 'message' => 'Das eigene Admin-Konto kann nicht deaktiviert werden'], 422);
}

$db = get_db();
$stmt = $db->prepare('UPDATE users SET active = ? WHERE id = ?');
if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Status konnte nicht vorbereitet werden'], 500);
}

$stmt->bind_param('ii', $active, $id);
if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Status konnte nicht geändert werden'], 500);
}

$stmt->close();
$db->close();
send_json(['status' => 'success', 'message' => 'Status erfolgreich geändert']);
