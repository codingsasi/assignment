<?php
declare(strict_types=1);

require __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($pdo === null) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger">Database unavailable.</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Checkout';
$uid = current_user()['id'];
$err = '';

$loadCart = function () use ($pdo, $uid) {
    $st = $pdo->prepare(
        'SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.stock
         FROM cart c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?'
    );
    $st->execute([$uid]);
    return $st->fetchAll();
};

$lines = $loadCart();
if (!$lines) {
    header('Location: cart.php');
    exit;
}

$subtotal = 0.0;
foreach ($lines as $ln) {
    if ((int) $ln['quantity'] > (int) $ln['stock']) {
        $err = 'One or more items no longer have enough stock.';
        break;
    }
    $subtotal += (float) $ln['price'] * (int) $ln['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $err === '') {
    try {
        $pdo->beginTransaction();
        $st = $pdo->prepare(
            'SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.stock
             FROM cart c JOIN products p ON p.id = c.product_id WHERE c.user_id = ? FOR UPDATE'
        );
        $st->execute([$uid]);
        $lines = $st->fetchAll();
        if (!$lines) {
            throw new RuntimeException('empty');
        }
        $total = 0.0;
        foreach ($lines as $ln) {
            if ((int) $ln['quantity'] > (int) $ln['stock']) {
                throw new RuntimeException('stock');
            }
            $total += (float) $ln['price'] * (int) $ln['quantity'];
        }
        $pdo->prepare('INSERT INTO orders (user_id, total_price, status) VALUES (?,?,?)')
            ->execute([$uid, $total, 'completed']);
        $orderId = (int) $pdo->lastInsertId();
        $updStock = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
        $insOi = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?,?,?,?)'
        );
        foreach ($lines as $ln) {
            $q = (int) $ln['quantity'];
            $pid = (int) $ln['product_id'];
            $price = (float) $ln['price'];
            $insOi->execute([$orderId, $pid, $q, $price]);
            $updStock->execute([$q, $pid, $q]);
            if ($updStock->rowCount() !== 1) {
                throw new RuntimeException('stock');
            }
        }
        $pdo->prepare('DELETE FROM cart WHERE user_id = ?')->execute([$uid]);
        $pdo->commit();
        header('Location: orders.php?placed=1');
        exit;
    } catch (Throwable $e) {
        $pdo->rollBack();
        $err = 'Checkout failed. Please review your cart and try again.';
        $lines = $loadCart();
        $subtotal = 0.0;
        foreach ($lines as $ln) {
            $subtotal += (float) $ln['price'] * (int) $ln['quantity'];
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<h1 class="mb-3">Checkout</h1>
<?php if ($err !== ''): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>
<p>Order total: <strong>$<?php echo number_format($subtotal, 2); ?></strong></p>
<ul class="list-group mb-3">
<?php foreach ($lines as $ln): ?>
  <li class="list-group-item d-flex justify-content-between">
    <span><?php echo htmlspecialchars($ln['name']); ?> × <?php echo (int) $ln['quantity']; ?></span>
    <span>$<?php echo number_format((float) $ln['price'] * (int) $ln['quantity'], 2); ?></span>
  </li>
<?php endforeach; ?>
</ul>
<form method="post">
  <button type="submit" name="confirm" value="1" class="btn btn-primary" <?php echo $err !== '' ? 'disabled' : ''; ?>>Place order</button>
  <a href="cart.php" class="btn btn-outline-secondary">Back to cart</a>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
