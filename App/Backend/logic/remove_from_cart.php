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

if ($productId <= 0) {
    send_json(['status' => 'error', 'message' => 'Ungültige Produkt-ID'], 422);
}

$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();
$db = get_db();

if ($userId) {
    $stmt = $db->prepare('DELETE FROM cartitems WHERE user_id = ? AND product_id = ?');
    $stmt->bind_param('ii', $userId, $productId);
} else {
    $stmt = $db->prepare('DELETE FROM cartitems WHERE session_id = ? AND product_id = ?');
    $stmt->bind_param('si', $sessionId, $productId);
}

$stmt->execute();
$ok = $stmt->affected_rows > 0;
$stmt->close();
$db->close();

if (!$ok) {
    send_json(['status' => 'error', 'message' => 'Artikel nicht gefunden'], 404);
}

send_json(['status' => 'success', 'message' => 'Artikel entfernt']);

