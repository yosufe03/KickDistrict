<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

$userId = require_login();
$data = read_json_input();
$voucherCode = strtoupper(clean_string($data['voucher_code'] ?? ''));
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

    $discount = 0.0;
    $appliedVoucherCode = null;
    if ($voucherCode !== '') {
        $voucherStmt = $db->prepare('SELECT id, code, value, expiry_date FROM vouchers WHERE code = ? LIMIT 1 FOR UPDATE');
        if (!$voucherStmt) {
            throw new RuntimeException('Statement preparation failed');
        }
        $voucherStmt->bind_param('s', $voucherCode);
        $voucherStmt->execute();
        $voucher = $voucherStmt->get_result()->fetch_assoc();
        $voucherStmt->close();

        if (!$voucher) {
            $db->rollback();
            $db->close();
            send_json(['status' => 'error', 'message' => 'Gutschein nicht gefunden'], 404);
        }

        if ($voucher['expiry_date'] < date('Y-m-d')) {
            $db->rollback();
            $db->close();
            send_json(['status' => 'error', 'message' => 'Gutschein ist abgelaufen'], 422);
        }

        $voucherValue = (float) $voucher['value'];
        if ($voucherValue <= 0) {
            $db->rollback();
            $db->close();
            send_json(['status' => 'error', 'message' => 'Gutschein wurde bereits verbraucht'], 422);
        }

        $voucherId = (int) $voucher['id'];
        $usageStmt = $db->prepare('SELECT id FROM voucher_usages WHERE voucher_id = ? AND user_id = ? LIMIT 1');
        if (!$usageStmt) {
            throw new RuntimeException('Statement preparation failed');
        }
        $usageStmt->bind_param('ii', $voucherId, $userId);
        $usageStmt->execute();
        $alreadyUsed = $usageStmt->get_result()->fetch_assoc();
        $usageStmt->close();

        if ($alreadyUsed) {
            $db->rollback();
            $db->close();
            send_json(['status' => 'error', 'message' => 'Du hast diesen Gutschein bereits verwendet'], 422);
        }

        $discount = min($voucherValue, $total);
        $appliedVoucherCode = $voucher['code'];
    }

    $orderTotal = max($total - $discount, 0);
    $status = 'confirmed';
    $orderStmt = $db->prepare('INSERT INTO orders (user_id, total_amount, discount_amount, voucher_code, status) VALUES (?, ?, ?, ?, ?)');
    if (!$orderStmt) {
        throw new RuntimeException('Statement preparation failed');
    }
    $orderStmt->bind_param('iddss', $userId, $orderTotal, $discount, $appliedVoucherCode, $status);
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

    if ($appliedVoucherCode !== null) {
        $usageInsertStmt = $db->prepare('INSERT INTO voucher_usages (voucher_id, user_id, order_id) VALUES (?, ?, ?)');
        if (!$usageInsertStmt) {
            throw new RuntimeException('Statement preparation failed');
        }
        $usageInsertStmt->bind_param('iii', $voucherId, $userId, $orderId);
        $usageInsertStmt->execute();
        $usageInsertStmt->close();
    }

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
            'total_amount' => $orderTotal,
            'discount_amount' => $discount,
            'voucher_code' => $appliedVoucherCode,
        ],
    ]);
} catch (Throwable $error) {
    $db->rollback();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Bestellung konnte nicht gespeichert werden'], 500);
}
