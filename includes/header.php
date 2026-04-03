<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$u = current_user();
$cartCount = 0;
if ($u && isset($pdo) && $pdo !== null) {
    $st = $pdo->prepare('SELECT COALESCE(SUM(quantity),0) AS c FROM cart WHERE user_id = ?');
    $st->execute([$u['id']]);
    $cartCount = (int) $st->fetchColumn();
}
$pageTitle = $pageTitle ?? 'Marketplace';
$d = trim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$np = $d === '' ? '' : str_repeat('../', count(explode('/', $d)));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?php echo $np; ?>index.php">Second-Hand Market</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navmain">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>products.php">Browse</a></li>
        <?php if ($u): ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>cart.php">Cart<?php if ($cartCount > 0): ?> <span class="badge bg-secondary"><?php echo $cartCount; ?></span><?php endif; ?></a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>orders.php">Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>account/listings.php">My listings</a></li>
        <?php endif; ?>
        <?php if ($u && !empty($u['is_admin'])): ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>admin/index.php">Admin</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if ($u): ?>
        <li class="nav-item"><span class="navbar-text me-2"><?php echo htmlspecialchars($u['name']); ?></span></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>logout.php">Logout</a></li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $np; ?>register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container pb-5">
