<?php
session_start();
include "../conn.php";

// ===== CEK AKSES: hanya admin yang boleh masuk =====
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php"); // belum login
    exit;
}
if ($_SESSION['role'] != "admin") {
    http_response_code(403);
    die("Akses ditolak. Halaman ini hanya untuk admin. <a href='../movie/index.php'>Kembali</a>");
}

// ===== DELETE: hapus film =====
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];
    mysqli_query($conn, "DELETE FROM movies WHERE id = $id");

    header("Location: index.php?status=hapus");
    exit;
}

// Pesan setelah tambah / edit / hapus
$pesan = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == "tambah") $pesan = "Film berhasil ditambahkan.";
    if ($_GET['status'] == "edit")   $pesan = "Film berhasil diperbarui.";
    if ($_GET['status'] == "hapus")  $pesan = "Film berhasil dihapus.";
}

// ===== READ: ambil daftar film (dengan filter pencarian) =====
$q = mysqli_real_escape_string($conn, trim($_GET['q'] ?? ""));

$result = mysqli_query($conn, "SELECT m.*, AVG(r.rating) AS rata, COUNT(r.id) AS jumlah
                               FROM movies m
                               LEFT JOIN reviews r ON r.movie_id = m.id
                               WHERE m.title LIKE '%$q%' OR m.genre LIKE '%$q%'
                               GROUP BY m.id
                               ORDER BY m.id DESC");
$total = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — Cinepedia</title>
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

  <?php if ($pesan != ""): ?>
    <p class="alert alert-success"><?= $pesan ?></p>
  <?php endif; ?>

  <section class="panel">
    <div class="panel-head">
      <h2 class="panel-title">DATABASE FILM <span class="muted small"><?= $total ?> judul</span></h2>

      <form method="GET" action="" class="inline-search">
        <input type="search" name="q" value="<?= htmlspecialchars($_GET['q'] ?? "") ?>" placeholder="Filter judul atau genre...">
      </form>

      <a class="btn btn-secondary" href="users.php">KELOLA USER</a>
      <a class="btn btn-primary" href="tambah.php">+ TAMBAH FILM</a>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>POSTER</th>
            <th>JUDUL</th>
            <th>GENRE</th>
            <th>RATING</th>
            <th class="right">AKSI</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($total == 0): ?>
            <tr><td colspan="5" class="center muted">Belum ada data film.</td></tr>
          <?php endif; ?>

          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td>
                <div class="poster-wrap thumb">
                  <?php if ($row['poster_url'] != ""): ?>
                    <img class="poster" src="<?= htmlspecialchars($row['poster_url']) ?>" alt="" onerror="this.style.display='none'">
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                <span class="muted small"><?= $row['release_year'] ?> · <?= htmlspecialchars($row['duration'] ?? "") ?></span>
              </td>
              <td><span class="tag"><?= htmlspecialchars($row['genre']) ?></span></td>
              <td class="gold">★ <?= ($row['jumlah'] > 0) ? number_format($row['rata'], 1) : "-" ?></td>
              <td class="right">
                <div class="actions">
                  <a class="btn btn-secondary btn-sm" href="../movie/detail.php?id=<?= $row['id'] ?>">LIHAT</a>
                  <a class="btn btn-warning btn-sm" href="edit.php?id=<?= $row['id'] ?>">EDIT</a>

                  <form method="POST" action="" onsubmit="return confirm('Yakin ingin menghapus film ini? Review-nya ikut terhapus.')">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" name="hapus" class="btn btn-danger btn-sm">HAPUS</button>
                  </form>
                </div>
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
