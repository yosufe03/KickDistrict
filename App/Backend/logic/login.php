<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['status' => 'error', 'message' => 'Methode nicht erlaubt'], 405);
}

$data = read_json_input();
$usernameEmail = clean_string($data['username_email'] ?? '');
$password = (string) ($data['password'] ?? '');
$rememberMe = (bool) ($data['remember_me'] ?? false);

if ($usernameEmail === '' || $password === '') {
    send_json(['status' => 'error', 'message' => 'Benutzername/E-Mail und Passwort sind erforderlich'], 422);
}

$db = get_db();
$stmt = $db->prepare('SELECT id, username, email, password, role, active FROM users WHERE username = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $usernameEmail, $usernameEmail);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password'])) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Ungueltige Anmeldedaten'], 401);
}

if ((int) $user['active'] !== 1) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Konto ist deaktiviert'], 403);
}

session_regenerate_id(true);
$_SESSION['loggedin'] = true;
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

if ($rememberMe) {
    setcookie('kd_user', $user['username'], time() + (86400 * 30), '/');
}

$db->close();
send_json(['status' => 'success', 'message' => 'Login erfolgreich']);

