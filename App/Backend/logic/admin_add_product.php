<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

require_admin();

$data = read_json_input();
$name = clean_string($data['name'] ?? '');
$description = clean_string($data['description'] ?? '');
$price = (float) ($data['price'] ?? 0);
$categoryId = (int) ($data['category_id'] ?? 0);
$image = clean_string($data['image'] ?? '../res/img/product-placeholder.svg');

if ($name === '' || $description === '' || $price <= 0 || $categoryId <= 0) {
    send_json(['status' => 'error', 'message' => 'Bitte alle Produktdaten korrekt ausfüllen'], 422);
}

$db = get_db();
$stmt = $db->prepare('INSERT INTO products (name, image, price, category_id, description) VALUES (?, ?, ?, ?, ?)');
if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Produkt konnte nicht vorbereitet werden'], 500);
}

$stmt->bind_param('ssdis', $name, $image, $price, $categoryId, $description);
if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Produkt konnte nicht erstellt werden'], 500);
}

$productId = $stmt->insert_id;
$stmt->close();
$db->close();

send_json([
    'status' => 'success',
    'message' => 'Produkt erfolgreich hinzugefügt',
    'data' => ['id' => $productId],
]);
