<?php
declare(strict_types=1);

require __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($pdo === null) {
    header('Location: listings.php');
    exit;
}

$pageTitle = 'Edit listing';
$uid = current_user()['id'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$row = null;
if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM products WHERE id = ? AND seller_id = ?');
    $st->execute([$id, $uid]);
    $row = $st->fetch();
    if (!$row) {
        header('Location: listings.php');
        exit;
    }
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $desc = trim((string) ($_POST['description'] ?? ''));
    $price = $_POST['price'] ?? '';
    $category = trim((string) ($_POST['category'] ?? ''));
    $condition = trim((string) ($_POST['condition'] ?? ''));
    $stock = $_POST['stock'] ?? '';
    $priceF = is_numeric($price) ? (float) $price : -1;
    $stockI = is_numeric($stock) ? (int) $stock : -1;
    if ($name === '' || mb_strlen($name) > 200) {
        $err = 'Invalid product name.';
    } elseif ($category === '' || mb_strlen($category) > 80) {
        $err = 'Invalid category.';
    } elseif ($condition === '' || mb_strlen($condition) > 40) {
        $err = 'Invalid condition.';
    } elseif ($priceF < 0) {
        $err = 'Invalid price.';
    } elseif ($stockI < 0) {
        $err = 'Invalid stock.';
    } else {
        $imageUrl = $row['image_url'] ?? null;
        $imageUrl = save_uploaded_product_image($imageUrl, $err);
        if ($err === '') {
            if ($id > 0) {
                $pdo->prepare(
                    'UPDATE products SET name=?, description=?, price=?, image_url=?, category=?, `condition`=?, stock=? WHERE id=? AND seller_id=?'
                )->execute([$name, $desc, $priceF, $imageUrl, $category, $condition, $stockI, $id, $uid]);
            } else {
                $pdo->prepare(
                    'INSERT INTO products (name, description, price, image_url, category, `condition`, stock, seller_id)
                     VALUES (?,?,?,?,?,?,?,?)'
                )->execute([$name, $desc, $priceF, $imageUrl, $category, $condition, $stockI, $uid]);
            }
            header('Location: listings.php');
            exit;
        }
    }
    $row = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'price' => $_POST['price'] ?? '',
        'category' => $_POST['category'] ?? '',
        'condition' => $_POST['condition'] ?? '',
        'stock' => $_POST['stock'] ?? '',
        'image_url' => $row['image_url'] ?? null,
    ];
}

require __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-3"><?php echo $id ? 'Edit listing' : 'New listing'; ?></h1>
<?php if ($err !== ''): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="col-lg-8" id="listingForm" novalidate>
  <div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" required maxlength="200" value="<?php echo htmlspecialchars((string) ($row['name'] ?? '')); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars((string) ($row['description'] ?? '')); ?></textarea>
  </div>
  <div class="row g-2">
    <div class="col-md-4 mb-3">
      <label class="form-label" for="price">Price</label>
      <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required value="<?php echo htmlspecialchars((string) ($row['price'] ?? '')); ?>">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label" for="stock">Stock</label>
      <input type="number" min="0" class="form-control" id="stock" name="stock" required value="<?php echo htmlspecialchars((string) ($row['stock'] ?? '')); ?>">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label" for="category">Category</label>
      <input type="text" class="form-control" id="category" name="category" required maxlength="80" value="<?php echo htmlspecialchars((string) ($row['category'] ?? '')); ?>">
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label" for="condition">Condition</label>
    <input type="text" class="form-control" id="condition" name="condition" required maxlength="40" value="<?php echo htmlspecialchars((string) ($row['condition'] ?? '')); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="image">Image</label>
    <input type="file" class="form-control" id="image" name="image" accept="image/*">
    <?php if (!empty($row['image_url'])): ?>
    <p class="small mt-1">Current: <a href="<?php echo htmlspecialchars(product_image_url($row['image_url'])); ?>">view</a></p>
    <?php endif; ?>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
  <a href="listings.php" class="btn btn-outline-secondary">Cancel</a>
</form>
<script src="../js/listing.js"></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
