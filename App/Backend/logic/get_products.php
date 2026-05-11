<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/dbaccess.php';
require_once __DIR__ . '/../config/response.php';

$search = clean_string($_GET['q'] ?? '');
$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;

$db = get_db();
$query = 'SELECT id, name, image, price, category_id FROM products';
$conditions = [];
$params = [];
$types = '';

if ($search !== '') {
    $conditions[] = '(name LIKE ? OR description LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($categoryId > 0) {
    $conditions[] = 'category_id = ?';
    $params[] = $categoryId;
    $types .= 'i';
}

if ($conditions) {
    $query .= ' WHERE ' . implode(' AND ', $conditions);
}

$query .= ' ORDER BY name';
$stmt = $db->prepare($query);

if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$db->close();

send_json(['status' => 'success', 'data' => $products]);

