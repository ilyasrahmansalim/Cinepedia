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

// Nilai awal form (kosong)
$title        = "";
$genre        = "";
$release_year = "";
$duration     = "";
$poster_url   = "";
$trailer_url  = "";
$synopsis     = "";
$error        = "";

// ===== CREATE: simpan film baru =====
if (isset($_POST['simpan'])) {
    $title        = trim($_POST['title']);
    $genre        = trim($_POST['genre']);
    $release_year = trim($_POST['release_year']);
    $duration     = trim($_POST['duration']);
    $poster_url   = trim($_POST['poster_url']);
    $trailer_url  = trim($_POST['trailer_url']);
    $synopsis     = trim($_POST['synopsis']);

    if ($title == "" || $genre == "" || $release_year == "") {
        $error = "Judul, genre, dan tahun rilis wajib diisi.";
    } elseif (!is_numeric($release_year) || $release_year < 1888 || $release_year > 2100) {
        $error = "Tahun rilis tidak valid.";
    } elseif ($poster_url != "" && substr($poster_url, 0, 4) != "http") {
        $error = "URL poster harus diawali http:// atau https://";
    } elseif ($trailer_url != "" && substr($trailer_url, 0, 4) != "http") {
        $error = "URL trailer harus diawali http:// atau https://";
    } else {
        // Amankan teks sebelum masuk query
        $safe_title       = mysqli_real_escape_string($conn, $title);
        $safe_genre       = mysqli_real_escape_string($conn, $genre);
        $safe_duration    = mysqli_real_escape_string($conn, $duration);
        $safe_poster_url  = mysqli_real_escape_string($conn, $poster_url);
        $safe_trailer_url = mysqli_real_escape_string($conn, $trailer_url);
        $safe_synopsis    = mysqli_real_escape_string($conn, $synopsis);
        $tahun            = (int) $release_year;

        mysqli_query($conn, "INSERT INTO movies (title, genre, release_year, duration, poster_url, trailer_url, synopsis)
                             VALUES ('$safe_title', '$safe_genre', $tahun, '$safe_duration', '$safe_poster_url', '$safe_trailer_url', '$safe_synopsis')");

        header("Location: index.php?status=tambah");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Film — Cinepedia</title>
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

  <?php if ($error != ""): ?>
    <p class="alert alert-error"><?= $error ?></p>
  <?php endif; ?>

  <section class="panel">
    <h2 class="panel-title">TAMBAH FILM BARU</h2>

    <form method="POST" action="">
      <div class="form-grid">
        <div class="form-group full">
          <label for="title">JUDUL FILM</label>
          <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" maxlength="255" required>
        </div>
        <div class="form-group">
          <label for="genre">GENRE (pisahkan dengan koma)</label>
          <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($genre) ?>" maxlength="100" placeholder="Drama, Sci-Fi" required>
        </div>
        <div class="form-group">
          <label for="release_year">TAHUN RILIS</label>
          <input type="number" id="release_year" name="release_year" value="<?= htmlspecialchars($release_year) ?>" min="1888" max="2100" required>
        </div>
        <div class="form-group">
          <label for="duration">DURASI</label>
          <input type="text" id="duration" name="duration" value="<?= htmlspecialchars($duration) ?>" maxlength="50" placeholder="2h 30m">
        </div>
        <div class="form-group">
          <label for="trailer_url">URL TRAILER YOUTUBE</label>
          <input type="url" id="trailer_url" name="trailer_url" value="<?= htmlspecialchars($trailer_url) ?>" maxlength="255" placeholder="https://youtube.com/watch?v=...">
        </div>
        <div class="form-group full">
          <label for="poster_url">URL POSTER</label>
          <input type="url" id="poster_url" name="poster_url" value="<?= htmlspecialchars($poster_url) ?>" maxlength="255" placeholder="https://.../poster.jpg">
        </div>
        <div class="form-group full">
          <label for="synopsis">SINOPSIS</label>
          <textarea id="synopsis" name="synopsis" rows="4"><?= htmlspecialchars($synopsis) ?></textarea>
        </div>
      </div>

      <div class="form-actions">
        <a class="btn btn-secondary" href="index.php">BATAL</a>
        <button type="submit" name="simpan" class="btn btn-primary">SIMPAN FILM</button>
      </div>
    </form>
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
