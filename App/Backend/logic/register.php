<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['status' => 'error', 'message' => 'Methode nicht erlaubt'], 405);
}

$data = read_json_input();

$salutation = clean_string($data['salutation'] ?? '');
$firstName = clean_string($data['first_name'] ?? '');
$lastName = clean_string($data['last_name'] ?? '');
$email = clean_string($data['email'] ?? '');
$username = clean_string($data['username'] ?? '');
$password = (string) ($data['password'] ?? '');
$passwordRepeat = (string) ($data['password_repeat'] ?? '');

if ($firstName === '' || $lastName === '' || $email === '' || $username === '' || $password === '') {
    send_json(['status' => 'error', 'message' => 'Bitte alle Pflichtfelder ausfuellen'], 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json(['status' => 'error', 'message' => 'Ungueltiges E-Mail-Format'], 422);
}

if ($password !== $passwordRepeat) {
    send_json(['status' => 'error', 'message' => 'Passwoerter stimmen nicht ueberein'], 422);
}

if (strlen($password) < 8 || !preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).+$/', $password)) {
    send_json(['status' => 'error', 'message' => 'Passwort muss min. 8 Zeichen inkl. Gross-/Kleinbuchstabe und Zahl enthalten'], 422);
}

$db = get_db();

$checkStmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
$checkStmt->bind_param('ss', $username, $email);
$checkStmt->execute();
$exists = $checkStmt->get_result()->fetch_assoc();
$checkStmt->close();

if ($exists) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Benutzername oder E-Mail bereits vergeben'], 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$insertStmt = $db->prepare('INSERT INTO users (salutation, first_name, last_name, email, username, password, role, active) VALUES (?, ?, ?, ?, ?, ?, "user", 1)');
$insertStmt->bind_param('ssssss', $salutation, $firstName, $lastName, $email, $username, $hash);

if (!$insertStmt->execute()) {
    $message = $insertStmt->error;
    $insertStmt->close();
    $db->close();
    send_json(['status' => 'error', 'message' => 'Registrierung fehlgeschlagen: ' . $message], 500);
}

$insertStmt->close();
$db->close();

send_json(['status' => 'success', 'message' => 'Registrierung erfolgreich']);

