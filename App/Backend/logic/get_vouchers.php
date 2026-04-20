<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$db = get_db();
$sql = 'SELECT code, value, expiry_date FROM vouchers ORDER BY expiry_date ASC';
$result = $db->query($sql);

$vouchers = [];
$today = date('Y-m-d');

while ($row = $result->fetch_assoc()) {
    $status = 'Aktiv';
    if ($row['expiry_date'] < $today) {
        $status = 'Abgelaufen';
    } elseif ((float) $row['value'] <= 0) {
        $status = 'Verbraucht';
    }

    $row['status'] = $status;
    $vouchers[] = $row;
}

$db->close();
send_json(['status' => 'success', 'data' => $vouchers]);

