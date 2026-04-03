<?php
declare(strict_types=1);

require __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $pdo === null) {
    header('Location: products.php');
    exit;
}

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$qty = isset($_POST['quantity']) ? max(1, (int) $_POST['quantity']) : 1;
$uid = current_user()['id'];

$st = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
$st->execute([$productId]);
$row = $st->fetch();
if (!$row || (int) $row['stock'] < $qty) {
    header('Location: product.php?id=' . $productId);
    exit;
}

$st = $pdo->prepare('SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
$st->execute([$uid, $productId]);
$existing = $st->fetch();
$newQty = $qty;
if ($existing) {
    $newQty = (int) $existing['quantity'] + $qty;
    if ($newQty > (int) $row['stock']) {
        $newQty = (int) $row['stock'];
    }
    $pdo->prepare('UPDATE cart SET quantity = ? WHERE id = ?')->execute([$newQty, $existing['id']]);
} else {
    $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)')->execute([$uid, $productId, $qty]);
}

header('Location: cart.php');
exit;
