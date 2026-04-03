<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/db.php';
require_once __DIR__ . '/product_image_upload.php';
