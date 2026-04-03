<?php
declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id < 1 || $pdo === null) {
    header('Location: products.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON u.id = p.seller_id WHERE p.id = ?'
);
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) {
    header('Location: products.php');
    exit;
}

$pageTitle = $p['name'];
require __DIR__ . '/includes/header.php';
?>
<div class="row">
  <div class="col-md-5">
    <?php if (!empty($p['image_url'])): ?>
    <img src="<?php echo htmlspecialchars(product_image_url($p['image_url'])); ?>" class="img-fluid rounded" alt="">
    <?php else: ?>
    <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center" style="min-height:280px;">No image</div>
    <?php endif; ?>
  </div>
  <div class="col-md-7">
    <h1><?php echo htmlspecialchars($p['name']); ?></h1>
    <p class="text-muted"><?php echo htmlspecialchars($p['category']); ?> · <?php echo htmlspecialchars($p['condition']); ?></p>
    <p class="fs-3 fw-bold">$<?php echo number_format((float) $p['price'], 2); ?></p>
    <p>Stock: <?php echo (int) $p['stock']; ?></p>
    <p>Seller: <?php echo htmlspecialchars($p['seller_name']); ?></p>
    <div class="mb-3"><?php echo nl2br(htmlspecialchars((string) ($p['description'] ?? ''))); ?></div>
    <?php if (current_user()): ?>
      <?php if ((int) $p['stock'] < 1): ?>
      <p class="text-danger">Out of stock</p>
      <?php else: ?>
      <form method="post" action="cart_add.php" class="row g-2 align-items-end">
        <input type="hidden" name="product_id" value="<?php echo (int) $p['id']; ?>">
        <div class="col-auto">
          <label class="form-label" for="qty">Qty</label>
          <input type="number" class="form-control" id="qty" name="quantity" value="1" min="1" max="<?php echo (int) $p['stock']; ?>" required>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Add to cart</button>
        </div>
      </form>
      <?php endif; ?>
    <?php else: ?>
    <p><a href="login.php">Log in</a> to add items to your cart.</p>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
