<?php
declare(strict_types=1);

function save_uploaded_product_image(?string $currentImageUrl, string &$err): ?string
{
    if (!isset($_FILES['image'])) {
        return $currentImageUrl;
    }
    $f = $_FILES['image'];
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $currentImageUrl;
    }
    if (($f['error'] ?? 1) !== UPLOAD_ERR_OK || empty($f['tmp_name']) || !is_uploaded_file($f['tmp_name'])) {
        $err = 'Could not upload image.';
        return $currentImageUrl;
    }
    $ext = strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION));
    if ($ext === 'jpeg') {
        $ext = 'jpg';
    }
    if (!in_array($ext, ['jpg', 'png', 'gif', 'webp'], true)) {
        $ext = 'jpg';
    }
    $dir = dirname(__DIR__) . '/uploads/products';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $fn = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $fn)) {
        $err = 'Could not save image.';
        return $currentImageUrl;
    }
    return '/uploads/products/' . $fn;
}

function product_image_url(?string $url): string
{
    if ($url === null || $url === '') {
        return '';
    }
    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
        return $url;
    }
    return str_starts_with($url, '/') ? $url : '/' . $url;
}
