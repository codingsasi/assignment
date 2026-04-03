<?php
declare(strict_types=1);

require __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Login';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $email = trim((string) ($_POST['email'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    $em = filter_var($email, FILTER_VALIDATE_EMAIL);
    if ($em === false || $pass === '') {
        $err = 'Invalid email or password.';
    } else {
        $st = $pdo->prepare('SELECT id, name, email, password_hash, is_admin FROM users WHERE email = ?');
        $st->execute([$em]);
        $row = $st->fetch();
        if ($row && password_verify($pass, $row['password_hash'])) {
            login_user($row);
            $next = isset($_GET['next']) ? (string) $_GET['next'] : '';
            if ($next !== '' && str_starts_with($next, '/') && !str_starts_with($next, '//')) {
                header('Location: ' . $next);
            } else {
                header('Location: index.php');
            }
            exit;
        }
        $err = 'Invalid email or password.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $err = 'Database unavailable.';
}

require __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">Login</h1>
<?php if ($err !== ''): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>
<form method="post" class="col-md-6 col-lg-4" action="login.php<?php echo isset($_GET['next']) ? '?next=' . rawurlencode((string) $_GET['next']) : ''; ?>" novalidate>
  <div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" required maxlength="255" autocomplete="username"
      value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" required minlength="1" autocomplete="current-password">
  </div>
  <button type="submit" class="btn btn-primary">Log in</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
