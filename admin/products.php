<?php
declare(strict_types=1);

require __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($pdo === null) {
    require __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-danger">Database unavailable.</div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $did = (int) $_POST['delete_id'];
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$did]);
    header('Location: products.php');
    exit;
}

$pageTitle = 'Admin products';
$rows = $pdo->query(
    'SELECT p.*, u.name AS seller_name, u.email AS seller_email FROM products p JOIN users u ON u.id = p.seller_id ORDER BY p.id DESC'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1 class="mb-3">Products</h1>
<p><a class="btn btn-primary" href="product_edit.php">Add product</a></p>
<table class="table table-sm table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>Seller</th><th>Price</th><th>Stock</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php echo (int) $r['id']; ?></td>
      <td><?php echo htmlspecialchars($r['name']); ?></td>
      <td><?php echo htmlspecialchars($r['seller_name']); ?></td>
      <td>$<?php echo number_format((float) $r['price'], 2); ?></td>
      <td><?php echo (int) $r['stock']; ?></td>
      <td>
        <a href="product_edit.php?id=<?php echo (int) $r['id']; ?>">Edit</a>
        <form method="post" class="d-inline" onsubmit="return confirm('Delete this product?');">
          <input type="hidden" name="delete_id" value="<?php echo (int) $r['id']; ?>">
          <button type="submit" class="btn btn-link btn-sm text-danger p-0 ms-2">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
