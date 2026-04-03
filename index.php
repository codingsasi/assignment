<?php
declare(strict_types=1);

require __DIR__ . '/includes/init.php';
$pageTitle = 'Home';

if ($pdo === null) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger">Database unavailable.</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = $pdo->query(
    'SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON u.id = p.seller_id ORDER BY p.created_at DESC LIMIT 6'
);
$featured = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<h1 class="mb-3">Featured listings</h1>
<div class="row g-4">
<?php foreach ($featured as $p): ?>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm">
      <?php if (!empty($p['image_url'])): ?>
      <img src="<?php echo htmlspecialchars(product_image_url($p['image_url'])); ?>" class="card-img-top" alt="" style="height:180px;object-fit:cover;">
      <?php else: ?>
      <div class="card-img-top bg-secondary-subtle d-flex align-items-center justify-content-center" style="height:180px;">No image</div>
      <?php endif; ?>
      <div class="card-body d-flex flex-column">
        <h2 class="h5 card-title"><?php echo htmlspecialchars($p['name']); ?></h2>
        <p class="card-text small text-muted"><?php echo htmlspecialchars($p['category']); ?> · <?php echo htmlspecialchars($p['condition']); ?></p>
        <p class="mt-auto fw-bold">$<?php echo number_format((float) $p['price'], 2); ?></p>
        <a class="btn btn-primary btn-sm" href="product.php?id=<?php echo (int) $p['id']; ?>">View</a>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
