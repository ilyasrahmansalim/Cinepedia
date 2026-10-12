<?php
require_once __DIR__ . '/includes/init.php';

$next = safe_next($_GET['next'] ?? $_POST['next'] ?? '');
if (is_logged_in()) {
    redirect($next ?: '/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && password_verify($password, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'username' => $u['username'], 'role' => $u['role']];
        redirect($next ?: ($u['role'] === 'admin' ? '/admin/' : '/index.php'));
    }
    $error = 'Email atau password salah.';
}

$pageTitle = 'Log In';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
  <h1>LOG IN</h1>
  <p class="muted">Masuk untuk memberi rating dan komentar.</p>

  <?php if ($error): ?><p class="alert alert-error"><?= e($error) ?></p><?php endif; ?>

  <form method="POST" action="">
    <?= csrf_field() ?>
    <input type="hidden" name="next" value="<?= e($next) ?>">
    <div class="form-group">
      <label for="email">EMAIL</label>
      <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required>
    </div>
    <div class="form-group">
      <label for="password">PASSWORD</label>
      <input type="password" id="password" name="password" autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">LOG IN</button>
  </form>
  <p class="auth-switch">Belum punya akun? <a href="<?= e(url('/register.php')) ?>">Daftar</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
