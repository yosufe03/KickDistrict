<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();

$db = get_db();

if ($userId) {
    $stmt = $db->prepare('SELECT p.id AS product_id, p.name, p.image, p.price, ci.quantity FROM cartitems ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ? ORDER BY p.name');
    $stmt->bind_param('i', $userId);
} else {
    $stmt = $db->prepare('SELECT p.id AS product_id, p.name, p.image, p.price, ci.quantity FROM cartitems ci JOIN products p ON ci.product_id = p.id WHERE ci.session_id = ? ORDER BY p.name');
    $stmt->bind_param('s', $sessionId);
}

$stmt->execute();
$result = $stmt->get_result();
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$stmt->close();
$db->close();

send_json(['status' => 'success', 'data' => $items]);

