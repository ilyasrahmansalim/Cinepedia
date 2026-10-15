<?php
session_start();
include "../conn.php";

// Kalau sudah login, tidak perlu daftar
if (isset($_SESSION['user_id'])) {
    header("Location: ../movie/index.php");
    exit;
}

$error = "";

// Proses form daftar
if (isset($_POST['daftar'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username == "" || $email == "" || $password == "") {
        $error = "Semua field wajib diisi.";
    } elseif (strlen($username) > 100) {
        $error = "Username maksimal 100 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        // mysqli_real_escape_string mengamankan teks sebelum masuk query
        $safe_username = mysqli_real_escape_string($conn, $username);
        $safe_email    = mysqli_real_escape_string($conn, $email);

        // Cek apakah email sudah dipakai
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$safe_email'");

        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            // Password disimpan dalam bentuk hash, bukan teks asli
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Role selalu 'user'. Admin dibuat lewat database saja.
            mysqli_query($conn, "INSERT INTO users (username, email, password, role)
                                 VALUES ('$safe_username', '$safe_email', '$hash', 'user')");

            header("Location: index.php?status=daftar");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar — Cinepedia</title>
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
    <h1>DAFTAR</h1>
    <p class="muted">Buat akun untuk memberi rating dan komentar.</p>

    <?php if ($error != ""): ?>
      <p class="alert alert-error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username">USERNAME</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="email">EMAIL</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">PASSWORD</label>
        <input type="password" id="password" name="password" minlength="6" required>
      </div>
      <button type="submit" name="daftar" class="btn btn-primary btn-block">DAFTAR</button>
    </form>

    <p class="auth-switch">Sudah punya akun? <a href="index.php">Log in</a></p>
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
