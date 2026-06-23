<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

require_admin();

$data = read_json_input();
$id = (int) ($data['id'] ?? 0);

if ($id <= 0) {
    send_json(['status' => 'error', 'message' => 'Ungültiges Produkt'], 422);
}

$db = get_db();
$stmt = $db->prepare('DELETE FROM products WHERE id = ?');
if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Produkt konnte nicht vorbereitet werden'], 500);
}

$stmt->bind_param('i', $id);
if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Produkt konnte nicht gelöscht werden'], 500);
}

$stmt->close();
$db->close();
send_json(['status' => 'success', 'message' => 'Produkt erfolgreich gelöscht']);
