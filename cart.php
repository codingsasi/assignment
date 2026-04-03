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

$pageTitle = 'Cart';
$uid = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_id'])) {
        $rid = (int) $_POST['remove_id'];
        $pdo->prepare('DELETE FROM cart WHERE id = ? AND user_id = ?')->execute([$rid, $uid]);
    }
    if (isset($_POST['update']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $cid => $q) {
            $cid = (int) $cid;
            $q = max(1, (int) $q);
            $chk = $pdo->prepare(
                'SELECT c.id, p.stock FROM cart c JOIN products p ON p.id = c.product_id WHERE c.id = ? AND c.user_id = ?'
            );
            $chk->execute([$cid, $uid]);
            $r = $chk->fetch();
            if ($r) {
                if ($q > (int) $r['stock']) {
                    $q = (int) $r['stock'];
                }
                $pdo->prepare('UPDATE cart SET quantity = ? WHERE id = ?')->execute([$q, $cid]);
            }
        }
    }
    header('Location: cart.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.stock, p.image_url
     FROM cart c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?'
);
$stmt->execute([$uid]);
$lines = $stmt->fetchAll();
$subtotal = 0.0;
foreach ($lines as $ln) {
    $subtotal += (float) $ln['price'] * (int) $ln['quantity'];
}

require __DIR__ . '/includes/header.php';
?>
<h1 class="mb-3">Shopping cart</h1>
<?php if (!$lines): ?>
<p>Your cart is empty. <a href="products.php">Browse products</a>.</p>
<?php else: ?>
<form method="post">
<table class="table align-middle">
  <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($lines as $ln): ?>
    <?php $lineTotal = (float) $ln['price'] * (int) $ln['quantity']; ?>
    <tr>
      <td><?php echo htmlspecialchars($ln['name']); ?></td>
      <td>$<?php echo number_format((float) $ln['price'], 2); ?></td>
      <td style="max-width:120px">
        <input type="number" class="form-control form-control-sm" name="qty[<?php echo (int) $ln['cart_id']; ?>]"
          value="<?php echo (int) $ln['quantity']; ?>" min="1" max="<?php echo (int) $ln['stock']; ?>">
      </td>
      <td>$<?php echo number_format($lineTotal, 2); ?></td>
      <td>
        <button type="submit" name="remove_id" value="<?php echo (int) $ln['cart_id']; ?>" class="btn btn-sm btn-outline-danger">Remove</button>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<button type="submit" name="update" value="1" class="btn btn-secondary mb-3">Update quantities</button>
</form>
<p class="fs-5">Total: <strong>$<?php echo number_format($subtotal, 2); ?></strong></p>
<a class="btn btn-primary" href="checkout.php">Checkout</a>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
