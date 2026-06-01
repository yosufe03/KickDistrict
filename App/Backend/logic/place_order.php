<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['status' => 'error', 'message' => 'Methode nicht erlaubt'], 405);
}

$userId = require_login();
$db = get_db();

try {
    $db->begin_transaction();

    $itemsStmt = $db->prepare('SELECT p.id AS product_id, p.name, p.price, ci.quantity FROM cartitems ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ? ORDER BY p.name');
    if (!$itemsStmt) {
        throw new RuntimeException('Statement preparation failed');
    }
    $itemsStmt->bind_param('i', $userId);
    $itemsStmt->execute();
    $result = $itemsStmt->get_result();

    $items = [];
    $total = 0.0;
    while ($row = $result->fetch_assoc()) {
        $rowQuantity = (int) $row['quantity'];
        $rowPrice = (float) $row['price'];
        $total += $rowPrice * $rowQuantity;
        $items[] = [
            'product_id' => (int) $row['product_id'],
            'product_name' => $row['name'],
            'quantity' => $rowQuantity,
            'unit_price' => $rowPrice,
        ];
    }
    $itemsStmt->close();

    if (!$items) {
        $db->rollback();
        $db->close();
        send_json(['status' => 'error', 'message' => 'Warenkorb ist leer'], 422);
    }

    $status = 'confirmed';
    $orderStmt = $db->prepare('INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)');
    if (!$orderStmt) {
        throw new RuntimeException('Statement preparation failed');
    }
    $orderStmt->bind_param('ids', $userId, $total, $status);
    $orderStmt->execute();
    $orderId = $orderStmt->insert_id;
    $orderStmt->close();

    $itemStmt = $db->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');
    if (!$itemStmt) {
        throw new RuntimeException('Statement preparation failed');
    }
    foreach ($items as $item) {
        $itemStmt->bind_param('iisid', $orderId, $item['product_id'], $item['product_name'], $item['quantity'], $item['unit_price']);
        $itemStmt->execute();
    }
    $itemStmt->close();

    $clearStmt = $db->prepare('DELETE FROM cartitems WHERE user_id = ?');
    if (!$clearStmt) {
        throw new RuntimeException('Statement preparation failed');
    }
    $clearStmt->bind_param('i', $userId);
    $clearStmt->execute();
    $clearStmt->close();

    $db->commit();
    $db->close();

    send_json([
        'status' => 'success',
        'message' => 'Bestellung erfolgreich aufgegeben',
        'data' => [
            'order_id' => $orderId,
            'total_amount' => $total,
        ],
    ]);
} catch (Throwable $error) {
    $db->rollback();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Bestellung konnte nicht gespeichert werden'], 500);
}

