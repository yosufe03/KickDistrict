<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$db = get_db();
$result = $db->query('SELECT id, name FROM categories ORDER BY name');

$categories = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    $result->free();
}

$db->close();
send_json(['status' => 'success', 'data' => $categories]);

