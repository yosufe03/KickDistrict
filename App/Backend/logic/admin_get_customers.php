<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

require_admin();

$db = get_db();
$sql = 'SELECT id, salutation, first_name, last_name, email, username, role, active, created_at FROM users ORDER BY created_at DESC, id DESC';
$result = $db->query($sql);

if (!$result) {
    $db->close();
    send_json(['status' => 'error', 'message' => 'Kunden konnten nicht geladen werden'], 500);
}

$customers = [];
while ($row = $result->fetch_assoc()) {
    $row['id'] = (int) $row['id'];
    $row['active'] = (int) $row['active'];
    $customers[] = $row;
}

$db->close();
send_json(['status' => 'success', 'data' => $customers]);
