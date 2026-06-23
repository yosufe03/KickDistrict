<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/response.php';

require_post();

require_admin();

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    send_json(['status' => 'error', 'message' => 'Bild konnte nicht hochgeladen werden'], 422);
}

$file = $_FILES['image'];
$maxSize = 3 * 1024 * 1024;
if ((int) $file['size'] > $maxSize) {
    send_json(['status' => 'error', 'message' => 'Bild darf maximal 3 MB groß sein'], 422);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$extensions = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/avif' => 'avif',
];

if (!isset($extensions[$mime])) {
    send_json(['status' => 'error', 'message' => 'Nur JPG, PNG, WebP oder AVIF sind erlaubt'], 422);
}

$targetDir = dirname(__DIR__) . '/productpictures';
if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
    send_json(['status' => 'error', 'message' => 'Upload-Verzeichnis konnte nicht erstellt werden'], 500);
}

$filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensions[$mime];
$targetPath = $targetDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    send_json(['status' => 'error', 'message' => 'Bild konnte nicht gespeichert werden'], 500);
}

send_json([
    'status' => 'success',
    'message' => 'Bild erfolgreich hochgeladen',
    'data' => ['image' => '../../Backend/productpictures/' . $filename],
]);
