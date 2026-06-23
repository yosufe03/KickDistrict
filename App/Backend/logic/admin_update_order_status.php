<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

require_admin();

$data = read_json_input();
$id = (int) ($data['id'] ?? 0);
$status = clean_string($data['status'] ?? '');
$allowed = ['pending', 'confirmed', 'paid', 'shipped', 'cancelled'];

if ($id <= 0 || !in_array($status, $allowed, true)) {
    send_json(['status' => 'error', 'message' => 'Ungültiger Bestellstatus'], 422);
}

$db = get_db();
$stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Bestellstatus konnte nicht vorbereitet werden'], 500);
}

$stmt->bind_param('si', $status, $id);
if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Bestellstatus konnte nicht geändert werden'], 500);
}

$stmt->close();
$db->close();
send_json(['status' => 'success', 'message' => 'Bestellstatus erfolgreich geändert']);
