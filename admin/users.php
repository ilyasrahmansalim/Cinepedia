<?php
session_start();
include "../conn.php";

// ===== CEK AKSES: hanya admin yang boleh masuk =====
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php"); // belum login
    exit;
}

// Role dicek langsung ke database (bukan dari session), supaya admin yang
// aksesnya baru dicabut langsung ditolak di halaman ini.
$my_id = (int) $_SESSION['user_id'];
$saya  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT role FROM users WHERE id = $my_id"));

if ($saya) {
    $_SESSION['role'] = $saya['role']; // samakan session dengan database
}
if (!$saya || $saya['role'] != "admin") {
    http_response_code(403);
    die("Akses ditolak. Halaman ini hanya untuk admin. <a href='../movie/index.php'>Kembali</a>");
}

$error = "";

// ===== UPDATE: jadikan user terpilih sebagai admin =====
if (isset($_POST['jadikan_admin'])) {
    $id = (int) $_POST['id'];
    mysqli_query($conn, "UPDATE users SET role = 'admin' WHERE id = $id");

    header("Location: users.php?status=admin");
    exit;
}

// ===== UPDATE: cabut akses admin (kembali jadi user biasa) =====
if (isset($_POST['cabut_admin'])) {
    $id = (int) $_POST['id'];

    if ($id == $my_id) {
        // Tidak boleh mencabut diri sendiri, supaya selalu ada minimal satu admin
        $error = "Kamu tidak bisa mencabut akses admin milikmu sendiri.";
    } else {
        mysqli_query($conn, "UPDATE users SET role = 'user' WHERE id = $id");

        header("Location: users.php?status=cabut");
        exit;
    }
}

// Pesan setelah aksi berhasil
$pesan = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == "admin") {
        $pesan = "User berhasil dijadikan admin. User tersebut perlu logout lalu login ulang agar akses admin aktif.";
    }
    if ($_GET['status'] == "cabut") {
        $pesan = "Akses admin berhasil dicabut.";
    }
}

// ===== READ: ambil semua user (admin di atas) =====
$q = mysqli_real_escape_string($conn, trim($_GET['q'] ?? ""));

$result = mysqli_query($conn, "SELECT * FROM users
                               WHERE username LIKE '%$q%' OR email LIKE '%$q%'
                               ORDER BY role ASC, username ASC");
$total = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola User — Cinepedia</title>
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
  <a class="back-link" href="index.php">← Kembali ke database film</a>

  <?php if ($pesan != ""): ?>
    <p class="alert alert-success"><?= $pesan ?></p>
  <?php endif; ?>
  <?php if ($error != ""): ?>
    <p class="alert alert-error"><?= $error ?></p>
  <?php endif; ?>

  <section class="panel">
    <div class="panel-head">
      <h2 class="panel-title">KELOLA USER <span class="muted small"><?= $total ?> akun</span></h2>

      <form method="GET" action="" class="inline-search">
        <input type="search" name="q" value="<?= htmlspecialchars($_GET['q'] ?? "") ?>" placeholder="Cari username atau email...">
      </form>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>USERNAME</th>
            <th>EMAIL</th>
            <th>ROLE</th>
            <th class="right">AKSI</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($total == 0): ?>
            <tr><td colspan="4" class="center muted">User tidak ditemukan.</td></tr>
          <?php endif; ?>

          <?php while ($u = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
              <td class="muted"><?= htmlspecialchars($u['email']) ?></td>
              <td><span class="tag"><?= $u['role'] ?></span></td>
              <td class="right">
                <?php if ($u['role'] == "admin" && $u['id'] == $my_id): ?>
                  <!-- Akun sendiri: tidak bisa dicabut -->
                  <span class="muted small">Akun kamu</span>

                <?php elseif ($u['role'] == "admin"): ?>
                  <form method="POST" action="" onsubmit="return confirm('Yakin ingin mencabut akses admin user ini?')">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" name="cabut_admin" class="btn btn-danger btn-sm">CABUT ADMIN</button>
                  </form>

                <?php else: ?>
                  <form method="POST" action="" onsubmit="return confirm('Yakin ingin menjadikan user ini sebagai admin?')">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" name="jadikan_admin" class="btn btn-primary btn-sm">JADIKAN ADMIN</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
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