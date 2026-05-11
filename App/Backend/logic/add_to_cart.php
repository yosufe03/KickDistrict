<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['status' => 'error', 'message' => 'Methode nicht erlaubt'], 405);
}

$payload = read_json_input();
$productId = (int) ($payload['product_id'] ?? 0);
$quantity = (int) ($payload['quantity'] ?? 1);

if ($productId <= 0 || $quantity <= 0) {
    send_json(['status' => 'error', 'message' => 'Ungültige Produktdaten'], 422);
}

$db = get_db();
$checkProduct = $db->prepare('SELECT id FROM products WHERE id = ? LIMIT 1');
$checkProduct->bind_param('i', $productId);
$checkProduct->execute();
$exists = $checkProduct->get_result()->fetch_assoc();
$checkProduct->close();

if (!$exists) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Produkt nicht gefunden'], 404);
}

$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();

if ($userId) {
    $checkStmt = $db->prepare('SELECT quantity FROM cartitems WHERE user_id = ? AND product_id = ? LIMIT 1');
    $checkStmt->bind_param('ii', $userId, $productId);
} else {
    $checkStmt = $db->prepare('SELECT quantity FROM cartitems WHERE session_id = ? AND product_id = ? LIMIT 1');
    $checkStmt->bind_param('si', $sessionId, $productId);
}

$checkStmt->execute();
$current = $checkStmt->get_result()->fetch_assoc();
$checkStmt->close();

if ($current) {
    $newQuantity = (int) $current['quantity'] + $quantity;
    if ($userId) {
        $updateStmt = $db->prepare('UPDATE cartitems SET quantity = ? WHERE user_id = ? AND product_id = ?');
        $updateStmt->bind_param('iii', $newQuantity, $userId, $productId);
    } else {
        $updateStmt = $db->prepare('UPDATE cartitems SET quantity = ? WHERE session_id = ? AND product_id = ?');
        $updateStmt->bind_param('isi', $newQuantity, $sessionId, $productId);
    }
    $ok = $updateStmt->execute();
    $updateStmt->close();
} else {
    if ($userId) {
        $insertStmt = $db->prepare('INSERT INTO cartitems (user_id, session_id, product_id, quantity) VALUES (?, ?, ?, ?)');
        $insertStmt->bind_param('isii', $userId, $sessionId, $productId, $quantity);
    } else {
        $insertStmt = $db->prepare('INSERT INTO cartitems (session_id, product_id, quantity) VALUES (?, ?, ?)');
        $insertStmt->bind_param('sii', $sessionId, $productId, $quantity);
    }
    $ok = $insertStmt->execute();
    $insertStmt->close();
}

$db->close();

if (!$ok) {
    send_json(['status' => 'error', 'message' => 'Warenkorb konnte nicht aktualisiert werden'], 500);
}

send_json(['status' => 'success', 'message' => 'Produkt im Warenkorb gespeichert']);

