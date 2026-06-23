<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$userId = require_login();
$db = get_db();

$stmt = $db->prepare('SELECT o.id, o.order_date, o.total_amount, o.discount_amount, o.voucher_code, o.status, oi.product_name, oi.quantity, oi.unit_price FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? ORDER BY o.order_date DESC, oi.id ASC');
if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Bestellhistorie konnte nicht geladen werden'], 500);
}
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = (int) $row['id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'id' => $orderId,
            'order_date' => $row['order_date'],
            'total_amount' => (float) $row['total_amount'],
            'discount_amount' => (float) $row['discount_amount'],
            'voucher_code' => $row['voucher_code'],
            'status' => $row['status'],
            'items' => [],
        ];
    }

    if ($row['product_name'] !== null) {
        $orders[$orderId]['items'][] = [
            'product_name' => $row['product_name'],
            'quantity' => (int) $row['quantity'],
            'unit_price' => (float) $row['unit_price'],
        ];
    }
}

$stmt->close();
$db->close();

send_json(['status' => 'success', 'data' => array_values($orders)]);
