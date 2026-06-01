<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$userId = require_login();
$orderId = (int) ($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    send_json(['status' => 'error', 'message' => 'Ungültige Bestellnummer'], 422);
}

$db = get_db();

$orderStmt = $db->prepare('SELECT o.id, o.order_date, o.total_amount, o.status, u.first_name, u.last_name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ? LIMIT 1');
if (!$orderStmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Rechnung konnte nicht geladen werden'], 500);
}
$orderStmt->bind_param('ii', $orderId, $userId);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();
$orderStmt->close();

if (!$order) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Bestellung nicht gefunden'], 404);
}

$itemStmt = $db->prepare('SELECT product_name, quantity, unit_price FROM order_items WHERE order_id = ? ORDER BY id ASC');
if (!$itemStmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Rechnung konnte nicht geladen werden'], 500);
}
$itemStmt->bind_param('i', $orderId);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

$items = [];
while ($row = $itemResult->fetch_assoc()) {
    $items[] = [
        'product_name' => $row['product_name'],
        'quantity' => (int) $row['quantity'],
        'unit_price' => (float) $row['unit_price'],
    ];
}
$itemStmt->close();
$db->close();

$invoiceNumber = 'KD-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);

send_json([
    'status' => 'success',
    'data' => [
        'invoice_number' => $invoiceNumber,
        'order' => [
            'id' => (int) $order['id'],
            'order_date' => $order['order_date'],
            'total_amount' => (float) $order['total_amount'],
            'status' => $order['status'],
        ],
        'customer' => [
            'name' => trim($order['first_name'] . ' ' . $order['last_name']),
            'email' => $order['email'],
        ],
        'items' => $items,
    ],
]);

