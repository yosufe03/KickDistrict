<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

$userId = require_login();
$data = read_json_input();
$code = strtoupper(clean_string($data['code'] ?? ''));

if ($code === '') {
    send_json(['status' => 'error', 'message' => 'Bitte Gutscheincode eingeben'], 422);
}

$db = get_db();
$cartStmt = $db->prepare('SELECT SUM(p.price * ci.quantity) AS total FROM cartitems ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?');
if (!$cartStmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Warenkorb konnte nicht geprüft werden'], 500);
}
$cartStmt->bind_param('i', $userId);
$cartStmt->execute();
$cartTotal = (float) ($cartStmt->get_result()->fetch_assoc()['total'] ?? 0);
$cartStmt->close();

if ($cartTotal <= 0) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Warenkorb ist leer'], 422);
}

$voucherStmt = $db->prepare('SELECT id, code, value, expiry_date FROM vouchers WHERE code = ? LIMIT 1');
if (!$voucherStmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Gutschein konnte nicht geprüft werden'], 500);
}
$voucherStmt->bind_param('s', $code);
$voucherStmt->execute();
$voucher = $voucherStmt->get_result()->fetch_assoc();
$voucherStmt->close();

if (!$voucher) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Gutschein nicht gefunden'], 404);
}

if ($voucher['expiry_date'] < date('Y-m-d')) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Gutschein ist abgelaufen'], 422);
}

$value = (float) $voucher['value'];
if ($value <= 0) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Gutschein wurde bereits verbraucht'], 422);
}

$usageStmt = $db->prepare('SELECT id FROM voucher_usages WHERE voucher_id = ? AND user_id = ? LIMIT 1');
if (!$usageStmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Gutschein konnte nicht geprüft werden'], 500);
}
$voucherId = (int) $voucher['id'];
$usageStmt->bind_param('ii', $voucherId, $userId);
$usageStmt->execute();
$alreadyUsed = $usageStmt->get_result()->fetch_assoc();
$usageStmt->close();
$db->close();

if ($alreadyUsed) {
    send_json(['status' => 'error', 'message' => 'Du hast diesen Gutschein bereits verwendet'], 422);
}

$discount = min($value, $cartTotal);
send_json([
    'status' => 'success',
    'message' => 'Gutschein angewendet',
    'data' => [
        'code' => $voucher['code'],
        'discount_amount' => $discount,
        'total_amount' => max($cartTotal - $discount, 0),
    ],
]);
