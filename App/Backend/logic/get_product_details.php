<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($productId <= 0) {
    send_json(['status' => 'error', 'message' => 'Produkt-ID fehlt'], 422);
}

$db = get_db();
$stmt = $db->prepare('SELECT id, name, image, price, description, category_id FROM products WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
$db->close();

if (!$product) {
    send_json(['status' => 'error', 'message' => 'Produkt nicht gefunden'], 404);
}

send_json(['status' => 'success', 'data' => $product]);

