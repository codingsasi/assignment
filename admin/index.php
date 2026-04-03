<?php
declare(strict_types=1);

require __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pageTitle = 'Admin';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="mb-3">Admin</h1>
<ul class="list-group col-md-6">
  <li class="list-group-item"><a href="products.php">Manage product listings</a></li>
  <li class="list-group-item"><a href="orders.php">View all orders</a></li>
  <li class="list-group-item"><a href="users.php">Manage user accounts</a></li>
</ul>
<?php require __DIR__ . '/../includes/footer.php'; ?>
