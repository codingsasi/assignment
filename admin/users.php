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

$me = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_admin'])) {
    $tid = (int) $_POST['toggle_admin'];
    if ($tid !== $me && $tid > 0) {
        $st = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
        $st->execute([$tid]);
        $u = $st->fetch();
        if ($u) {
            $new = (int) $u['is_admin'] ? 0 : 1;
            $pdo->prepare('UPDATE users SET is_admin = ? WHERE id = ?')->execute([$new, $tid]);
        }
    }
    header('Location: users.php');
    exit;
}

$pageTitle = 'Users';
$rows = $pdo->query('SELECT id, name, email, is_admin, created_at FROM users ORDER BY id ASC')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1 class="mb-3">Users</h1>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php echo (int) $r['id']; ?></td>
      <td><?php echo htmlspecialchars($r['name']); ?></td>
      <td><?php echo htmlspecialchars($r['email']); ?></td>
      <td><?php echo (int) $r['is_admin'] ? 'Yes' : 'No'; ?></td>
      <td>
        <?php if ((int) $r['id'] !== $me): ?>
        <form method="post" class="d-inline">
          <input type="hidden" name="toggle_admin" value="<?php echo (int) $r['id']; ?>">
          <button type="submit" class="btn btn-sm btn-outline-secondary">Toggle admin</button>
        </form>
        <?php else: ?>
        —
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
