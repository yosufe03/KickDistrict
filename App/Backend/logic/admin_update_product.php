<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

require_admin();

$data = read_json_input();
$id = (int) ($data['id'] ?? 0);
$name = clean_string($data['name'] ?? '');
$description = clean_string($data['description'] ?? '');
$price = (float) ($data['price'] ?? 0);
$categoryId = (int) ($data['category_id'] ?? 0);
$image = clean_string($data['image'] ?? '');

if ($id <= 0 || $name === '' || $description === '' || $price <= 0 || $categoryId <= 0) {
    send_json(['status' => 'error', 'message' => 'Bitte alle Produktdaten korrekt ausfüllen'], 422);
}

$db = get_db();

if ($image !== '') {
    $stmt = $db->prepare('UPDATE products SET name = ?, image = ?, price = ?, category_id = ?, description = ? WHERE id = ?');
} else {
    $stmt = $db->prepare('UPDATE products SET name = ?, price = ?, category_id = ?, description = ? WHERE id = ?');
}

if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Produkt konnte nicht vorbereitet werden'], 500);
}

if ($image !== '') {
    $stmt->bind_param('ssdisi', $name, $image, $price, $categoryId, $description, $id);
} else {
    $stmt->bind_param('sdisi', $name, $price, $categoryId, $description, $id);
}

if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Produkt konnte nicht aktualisiert werden'], 500);
}

$affected = $stmt->affected_rows;
$stmt->close();
$db->close();

send_json([
    'status' => 'success',
    'message' => $affected >= 0 ? 'Produkt erfolgreich aktualisiert' : 'Keine Änderung gespeichert',
]);
