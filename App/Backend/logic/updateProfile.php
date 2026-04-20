<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$userId = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['status' => 'error', 'message' => 'Methode nicht erlaubt'], 405);
}

// Unterstuetzt FormData (Frontend) und JSON (Tests/API-Client)
$payload = !empty($_POST) ? $_POST : read_json_input();

$salutation = clean_string($payload['salutation'] ?? '');
$firstName = clean_string($payload['first_name'] ?? '');
$lastName = clean_string($payload['last_name'] ?? '');
$email = clean_string($payload['email'] ?? '');
$currentPassword = (string) ($payload['current_password'] ?? '');
$newPassword = (string) ($payload['new_password'] ?? '');

if ($firstName === '' || $lastName === '' || $email === '' || $currentPassword === '') {
    send_json(['status' => 'error', 'message' => 'Pflichtfelder fehlen'], 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json(['status' => 'error', 'message' => 'Ungueltiges E-Mail-Format'], 422);
}

$db = get_db();
$pwStmt = $db->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
$pwStmt->bind_param('i', $userId);
$pwStmt->execute();
$user = $pwStmt->get_result()->fetch_assoc();
$pwStmt->close();

if (!$user || !password_verify($currentPassword, $user['password'])) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Aktuelles Passwort ist falsch'], 401);
}

$updateStmt = $db->prepare('UPDATE users SET salutation = ?, first_name = ?, last_name = ?, email = ? WHERE id = ?');
$updateStmt->bind_param('ssssi', $salutation, $firstName, $lastName, $email, $userId);
$ok = $updateStmt->execute();
$err = $updateStmt->error;
$updateStmt->close();

if (!$ok) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Profil konnte nicht aktualisiert werden: ' . $err], 500);
}

if ($newPassword !== '') {
    if (strlen($newPassword) < 8 || !preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).+$/', $newPassword)) {
        $db->close();
        send_json(['status' => 'error', 'message' => 'Neues Passwort erfuellt die Regeln nicht'], 422);
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $pwUpdateStmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
    $pwUpdateStmt->bind_param('si', $newHash, $userId);
    $pwOk = $pwUpdateStmt->execute();
    $pwError = $pwUpdateStmt->error;
    $pwUpdateStmt->close();

    if (!$pwOk) {
        $db->close();
        send_json(['status' => 'error', 'message' => 'Passwort konnte nicht aktualisiert werden: ' . $pwError], 500);
    }
}

$db->close();
send_json(['status' => 'success', 'message' => 'Profil aktualisiert']);

