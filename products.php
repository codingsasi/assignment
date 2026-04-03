<?php
declare(strict_types=1);

require __DIR__ . '/includes/init.php';
$pageTitle = 'Browse';

if ($pdo === null) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger">Database unavailable.</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$cat = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$cond = isset($_GET['condition']) ? trim((string) $_GET['condition']) : '';
$minP = isset($_GET['min_price']) ? trim((string) $_GET['min_price']) : '';
$maxP = isset($_GET['max_price']) ? trim((string) $_GET['max_price']) : '';

$sql = 'SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON u.id = p.seller_id WHERE 1=1';
$params = [];
if ($cat !== '') {
    $sql .= ' AND p.category = ?';
    $params[] = $cat;
}
if ($q !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
}
if ($cond !== '') {
    $sql .= ' AND p.`condition` = ?';
    $params[] = $cond;
}
if ($minP !== '' && is_numeric($minP)) {
    $sql .= ' AND p.price >= ?';
    $params[] = (float) $minP;
}
if ($maxP !== '' && is_numeric($maxP)) {
    $sql .= ' AND p.price <= ?';
    $params[] = (float) $maxP;
}
$sql .= ' ORDER BY p.name ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$cats = $pdo->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$conds = $pdo->query('SELECT DISTINCT `condition` FROM products ORDER BY `condition`')->fetchAll(PDO::FETCH_COLUMN);

require __DIR__ . '/includes/header.php';
?>
<h1 class="mb-3">Browse listings</h1>
<form class="row g-2 mb-4" method="get" action="products.php">
  <div class="col-md-3">
    <input type="search" class="form-control" name="q" placeholder="Search" value="<?php echo htmlspecialchars($q); ?>">
  </div>
  <div class="col-md-2">
    <select name="category" class="form-select">
      <option value="">All categories</option>
      <?php foreach ($cats as $c): ?>
      <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $cat === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="condition" class="form-select">
      <option value="">Any condition</option>
      <?php foreach ($conds as $c): ?>
      <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $cond === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <input type="number" step="0.01" class="form-control" name="min_price" placeholder="Min $" value="<?php echo htmlspecialchars($minP); ?>">
  </div>
  <div class="col-md-2">
    <input type="number" step="0.01" class="form-control" name="max_price" placeholder="Max $" value="<?php echo htmlspecialchars($maxP); ?>">
  </div>
  <div class="col-md-1">
    <button type="submit" class="btn btn-primary w-100">Filter</button>
  </div>
</form>
<div class="row g-4">
<?php foreach ($rows as $p): ?>
  <div class="col-md-4 col-lg-3">
    <div class="card h-100 shadow-sm">
      <?php if (!empty($p['image_url'])): ?>
      <img src="<?php echo htmlspecialchars(product_image_url($p['image_url'])); ?>" class="card-img-top" alt="" style="height:160px;object-fit:cover;">
      <?php else: ?>
      <div class="card-img-top bg-secondary-subtle d-flex align-items-center justify-content-center" style="height:160px;">No image</div>
      <?php endif; ?>
      <div class="card-body d-flex flex-column">
        <h2 class="h6 card-title"><?php echo htmlspecialchars($p['name']); ?></h2>
        <p class="small mb-1">$<?php echo number_format((float) $p['price'], 2); ?></p>
        <a class="btn btn-outline-primary btn-sm mt-auto" href="product.php?id=<?php echo (int) $p['id']; ?>">Details</a>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php if (!$rows): ?>
<p class="text-muted mt-3">No products match your filters.</p>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
