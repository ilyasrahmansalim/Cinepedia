<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (mb_strlen($username) > 100) {
        $error = 'Username maksimal 100 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            // Role selalu 'user'; admin hanya dibuat lewat database.
            $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')")
                ->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Pendaftaran berhasil. Silakan log in.');
            redirect('/login.php');
        }
    }
}

$pageTitle = 'Daftar';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
  <h1>DAFTAR</h1>
  <p class="muted">Buat akun untuk memberi rating dan komentar.</p>

  <?php if ($error): ?><p class="alert alert-error"><?= e($error) ?></p><?php endif; ?>

  <form method="POST" action="">
    <?= csrf_field() ?>
    <div class="form-group">
      <label for="username">USERNAME</label>
      <input type="text" id="username" name="username" value="<?= e($_POST['username'] ?? '') ?>" autocomplete="username" required>
    </div>
    <div class="form-group">
      <label for="email">EMAIL</label>
      <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required>
    </div>
    <div class="form-group">
      <label for="password">PASSWORD</label>
      <input type="password" id="password" name="password" minlength="6" autocomplete="new-password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">DAFTAR</button>
  </form>
  <p class="auth-switch">Sudah punya akun? <a href="<?= e(url('/login.php')) ?>">Log in</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
