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

$pageTitle = 'Order history';
$uid = current_user()['id'];

$stmt = $pdo->prepare(
    'SELECT o.id, o.total_price, o.order_date, o.status FROM orders o WHERE o.user_id = ? ORDER BY o.order_date DESC'
);
$stmt->execute([$uid]);
$orders = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<h1 class="mb-3">Order history</h1>
<?php if (isset($_GET['placed'])): ?>
<div class="alert alert-success">Your order was placed.</div>
<?php endif; ?>
<?php if (!$orders): ?>
<p>No orders yet.</p>
<?php else: ?>
<?php foreach ($orders as $o): ?>
<div class="card mb-3">
  <div class="card-header d-flex justify-content-between">
    <span>Order #<?php echo (int) $o['id']; ?></span>
    <span><?php echo htmlspecialchars($o['order_date']); ?></span>
  </div>
  <div class="card-body">
    <p class="mb-2">Total: $<?php echo number_format((float) $o['total_price'], 2); ?> · <?php echo htmlspecialchars($o['status']); ?></p>
    <?php
    $it = $pdo->prepare(
        'SELECT oi.quantity, oi.unit_price, p.name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?'
    );
    $it->execute([(int) $o['id']]);
    $items = $it->fetchAll();
    ?>
    <ul class="list-group list-group-flush">
      <?php foreach ($items as $i): ?>
      <li class="list-group-item d-flex justify-content-between">
        <span><?php echo htmlspecialchars($i['name']); ?> × <?php echo (int) $i['quantity']; ?></span>
        <span>$<?php echo number_format((float) $i['unit_price'] * (int) $i['quantity'], 2); ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
