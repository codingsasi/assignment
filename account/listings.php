<?php
declare(strict_types=1);

require __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($pdo === null) {
    require __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-danger">Database unavailable.</div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = 'My listings';
$uid = current_user()['id'];
$st = $pdo->prepare('SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC');
$st->execute([$uid]);
$rows = $st->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1 class="mb-3">My listings</h1>
<p><a class="btn btn-primary" href="listing_edit.php">Add listing</a></p>
<table class="table table-striped">
  <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['name']); ?></td>
      <td><?php echo htmlspecialchars($r['category']); ?></td>
      <td>$<?php echo number_format((float) $r['price'], 2); ?></td>
      <td><?php echo (int) $r['stock']; ?></td>
      <td><a href="listing_edit.php?id=<?php echo (int) $r['id']; ?>">Edit</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
