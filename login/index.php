<?php
session_start();
include "../conn.php";

// Kalau sudah login, tidak perlu login lagi
if (isset($_SESSION['user_id'])) {
    header("Location: ../movie/index.php");
    exit;
}

$error  = "";
$sukses = "";

// Pesan setelah daftar akun
if (isset($_GET['status']) && $_GET['status'] == "daftar") {
    $sukses = "Pendaftaran berhasil. Silakan log in.";
}

// Proses form login
if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user   = mysqli_fetch_assoc($result);

    // password_verify membandingkan password yang diketik dengan hash di database
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // ganti ID session demi keamanan

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        if ($user['role'] == "admin") {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../movie/index.php");
        }
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In — Cinepedia</title>
  <link rel="icon" type="image/png" href="../assets/logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="navbar">
  <a class="brand" href="../movie/index.php"><img src="../assets/logo.png" alt="" width="50px"></span> CINEPEDIA</a>

  <form class="search-box" method="GET" action="../movie/index.php">
    <input type="search" name="q" placeholder="Cari judul, genre, atau tahun...">
  </form>

  <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Sudah login: ikon profil + username + dropdown -->
    <div class="user-dropdown" id="userDropdown">
      <button type="button" class="profile-btn" id="profileBtn">
        <svg class="profile-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7z"/></svg>
        <span class="profile-name"><?= htmlspecialchars($_SESSION['username']) ?></span>
        <span class="caret">▾</span>
      </button>
      <div class="dropdown-menu">
        <div class="dropdown-head">
          <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
          <small><?= $_SESSION['role'] ?></small>
        </div>
        <?php if ($_SESSION['role'] == "admin"): ?>
          <a class="dropdown-item" href="../admin/index.php">Admin Panel</a>
        <?php endif; ?>
        <a class="dropdown-item danger" href="../login/logout.php">Logout</a>
      </div>
    </div>
  <?php else: ?>
    <a class="btn btn-primary btn-nav" href="../login/index.php">LOG IN</a>
  <?php endif; ?>
</header>

<!-- ===== ISI HALAMAN ===== -->
<main class="container">
  <section class="auth-card">
    <h1>LOG IN</h1>
    <p class="muted">Masuk untuk memberi rating dan komentar.</p>

    <?php if ($sukses != ""): ?>
      <p class="alert alert-success"><?= $sukses ?></p>
    <?php endif; ?>
    <?php if ($error != ""): ?>
      <p class="alert alert-error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="email">EMAIL</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">PASSWORD</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" name="login" class="btn btn-primary btn-block">LOG IN</button>
    </form>

    <p class="auth-switch">Belum punya akun? <a href="register.php">Daftar</a></p>
  </section>
</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
  <span><strong>CINEPEDIA</strong> — Movie Catalog</span>
  <span>&copy; <?= date("Y") ?> All Rights Reserved.</span>
</footer>

<script src="../assets/app.js"></script>
</body>
</html>
