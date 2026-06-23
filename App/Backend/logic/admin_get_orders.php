<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_admin();

$customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 0;
$db = get_db();

$sql = 'SELECT o.id, o.user_id, o.order_date, o.total_amount, o.discount_amount, o.voucher_code, o.status,
               u.first_name, u.last_name, u.username, u.email,
               oi.id AS item_id, oi.product_id, oi.product_name, oi.quantity, oi.unit_price
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id';

if ($customerId > 0) {
    $sql .= ' WHERE o.user_id = ?';
}

$sql .= ' ORDER BY o.order_date DESC, o.id DESC, oi.id ASC';
$stmt = $db->prepare($sql);
if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Bestellungen konnten nicht geladen werden'], 500);
}

if ($customerId > 0) {
    $stmt->bind_param('i', $customerId);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = [];

while ($row = $result->fetch_assoc()) {
    $orderId = (int) $row['id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'id' => $orderId,
            'user_id' => (int) $row['user_id'],
            'customer_name' => trim($row['first_name'] . ' ' . $row['last_name']),
            'username' => $row['username'],
            'email' => $row['email'],
            'order_date' => $row['order_date'],
            'total_amount' => (float) $row['total_amount'],
            'discount_amount' => (float) $row['discount_amount'],
            'voucher_code' => $row['voucher_code'],
            'status' => $row['status'],
            'items' => [],
        ];
    }

    if ($row['item_id'] !== null) {
        $orders[$orderId]['items'][] = [
            'id' => (int) $row['item_id'],
            'product_id' => $row['product_id'] !== null ? (int) $row['product_id'] : null,
            'product_name' => $row['product_name'],
            'quantity' => (int) $row['quantity'],
            'unit_price' => (float) $row['unit_price'],
        ];
    }
}

$stmt->close();
$db->close();
send_json(['status' => 'success', 'data' => array_values($orders)]);
