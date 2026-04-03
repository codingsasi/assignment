<?php
declare(strict_types=1);

require __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Register';
$err = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    $pass2 = (string) ($_POST['password2'] ?? '');
    if ($name === '' || mb_strlen($name) > 120) {
        $err = 'Enter a valid name (1–120 characters).';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $err = 'Enter a valid email.';
    } elseif (strlen($pass) < 8) {
        $err = 'Password must be at least 8 characters.';
    } elseif ($pass !== $pass2) {
        $err = 'Passwords do not match.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $st = $pdo->prepare('INSERT INTO users (name, email, password_hash, is_admin) VALUES (?,?,?,0)');
            $st->execute([$name, $email, $hash]);
            $ok = true;
        } catch (PDOException $e) {
            $err = 'That email may already be registered.';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $err = 'Database unavailable.';
}

require __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">Register</h1>
<?php if ($ok): ?>
<div class="alert alert-success">Account created. <a href="login.php">Log in</a>.</div>
<?php else: ?>
<?php if ($err !== ''): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>
<form method="post" class="col-md-6 col-lg-4" id="regForm" novalidate>
  <div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" required maxlength="120"
      value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" required maxlength="255"
      value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" required minlength="8" autocomplete="new-password">
  </div>
  <div class="mb-3">
    <label class="form-label" for="password2">Confirm password</label>
    <input type="password" class="form-control" id="password2" name="password2" required minlength="8" autocomplete="new-password">
    <div class="invalid-feedback" id="pw2fb">Passwords must match.</div>
  </div>
  <button type="submit" class="btn btn-primary">Create account</button>
</form>
<script src="js/register.js"></script>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
