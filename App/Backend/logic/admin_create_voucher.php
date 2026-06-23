<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_post();

require_admin();

$data = read_json_input();
$code = strtoupper(clean_string($data['code'] ?? ''));
$value = (float) ($data['value'] ?? 0);
$expiryDate = clean_string($data['expiry_date'] ?? '');

if ($code === '') {
    $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
}

if (!preg_match('/^[A-Z0-9]{5,20}$/', $code)) {
    send_json(['status' => 'error', 'message' => 'Code darf nur aus 5-20 Großbuchstaben/Zahlen bestehen'], 422);
}

if ($value <= 0 || $expiryDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiryDate)) {
    send_json(['status' => 'error', 'message' => 'Bitte Wert und Ablaufdatum korrekt ausfüllen'], 422);
}

$db = get_db();
$stmt = $db->prepare('INSERT INTO vouchers (code, value, expiry_date) VALUES (?, ?, ?)');
if (!$stmt) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Gutschein konnte nicht vorbereitet werden'], 500);
}

$stmt->bind_param('sds', $code, $value, $expiryDate);
if (!$stmt->execute()) {
    $message = $db->errno === 1062 ? 'Gutscheincode existiert bereits' : 'Gutschein konnte nicht erstellt werden';
    $stmt->close();
    $db->close();
    send_json(['status' => 'error', 'message' => $message], 409);
}

$stmt->close();
$db->close();
send_json([
    'status' => 'success',
    'message' => 'Gutschein erfolgreich erstellt',
    'data' => ['code' => $code],
]);
