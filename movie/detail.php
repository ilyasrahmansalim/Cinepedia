<?php
session_start();
include "../conn.php";

// Ambil id film dari URL. (int) mengubahnya jadi angka, jadi aman dipakai di query.
$id = (int) ($_GET['id'] ?? 0);

$result = mysqli_query($conn, "SELECT * FROM movies WHERE id = $id");
$movie  = mysqli_fetch_assoc($result);

// Kalau film tidak ada, kembali ke katalog
if (!$movie) {
    header("Location: index.php");
    exit;
}

$error  = "";
$sukses = "";

if (isset($_GET['status']) && $_GET['status'] == "ok") {
    $sukses = "Review kamu berhasil disimpan.";
}

// Proses kirim rating + komentar
if (isset($_POST['kirim'])) {

    // Harus login dulu
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login/index.php");
        exit;
    }

    $user_id  = (int) $_SESSION['user_id'];
    $rating   = (int) ($_POST['rating'] ?? 0);
    $komentar = trim($_POST['comment'] ?? "");

    if ($rating < 1 || $rating > 5) {
        $error = "Pilih rating bintang 1 sampai 5.";
    } elseif ($komentar == "" || strlen($komentar) > 500) {
        $error = "Komentar wajib diisi (maksimal 500 karakter).";
    } else {
        $safe_komentar = mysqli_real_escape_string($conn, $komentar);

        // Cek apakah user ini sudah pernah mereview film ini
        $cek = mysqli_query($conn, "SELECT id FROM reviews WHERE movie_id = $id AND user_id = $user_id");

        if (mysqli_num_rows($cek) > 0) {
            // Sudah pernah: perbarui review lama
            mysqli_query($conn, "UPDATE reviews
                                 SET rating = $rating, comment = '$safe_komentar', created_at = NOW()
                                 WHERE movie_id = $id AND user_id = $user_id");
        } else {
            // Belum pernah: buat review baru
            mysqli_query($conn, "INSERT INTO reviews (movie_id, user_id, rating, comment)
                                 VALUES ($id, $user_id, $rating, '$safe_komentar')");
        }

        header("Location: detail.php?id=$id&status=ok");
        exit;
    }
}

// Ambil semua review film ini beserta username penulisnya
$reviews = mysqli_query($conn, "SELECT r.*, u.username
                                FROM reviews r
                                JOIN users u ON u.id = r.user_id
                                WHERE r.movie_id = $id
                                ORDER BY r.created_at DESC");
$total = mysqli_num_rows($reviews);

// Hitung rata-rata rating
$hasilRata = mysqli_query($conn, "SELECT AVG(rating) AS rata FROM reviews WHERE movie_id = $id");
$dataRata  = mysqli_fetch_assoc($hasilRata);
$rata      = $dataRata['rata'];

// Kalau sudah login, ambil review milik sendiri untuk mengisi form
$ratingSaya   = 0;
$komentarSaya = "";
if (isset($_SESSION['user_id'])) {
    $uid  = (int) $_SESSION['user_id'];
    $saya = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM reviews WHERE movie_id = $id AND user_id = $uid"));
    if ($saya) {
        $ratingSaya   = $saya['rating'];
        $komentarSaya = $saya['comment'];
    }
}

// Genre disimpan "Drama, Sci-Fi" -> pecah jadi array
$daftarGenre = explode(",", $movie['genre']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($movie['title']) ?> — Cinepedia</title>
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

  <?php if ($sukses != ""): ?>
    <p class="alert alert-success"><?= $sukses ?></p>
  <?php endif; ?>

  <!-- Info film -->
  <section class="panel movie-hero">
    <div class="poster-wrap hero-poster">
      <?php if ($movie['poster_url'] != ""): ?>
        <img class="poster" src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="Poster <?= htmlspecialchars($movie['title']) ?>" onerror="this.style.display='none'">
      <?php endif; ?>
    </div>

    <div>
      <h1 class="movie-title">
        <?= htmlspecialchars(strtoupper($movie['title'])) ?>
        <small>(<?= $movie['release_year'] ?>)</small>
      </h1>

      <div class="tags">
        <?php foreach ($daftarGenre as $g): ?>
          <span class="tag"><?= htmlspecialchars(trim($g)) ?></span>
        <?php endforeach; ?>
      </div>

      <p class="muted small"><?= htmlspecialchars($movie['duration'] ?? "") ?></p>

      <p class="rating-line">
        <span class="gold">★</span>
        <strong><?= ($total > 0) ? number_format($rata, 1) : "N/A" ?></strong>
        <span class="muted">/5 · <?= $total ?> rating</span>
      </p>

      <h2 class="label">SINOPSIS</h2>
      <p class="synopsis"><?= nl2br(htmlspecialchars($movie['synopsis'] ?? "Belum ada sinopsis.")) ?></p>

      <?php if ($movie['trailer_url'] != ""): ?>
        <a class="btn btn-primary" href="<?= htmlspecialchars($movie['trailer_url']) ?>" target="_blank" rel="noopener noreferrer">▶ TONTON TRAILER</a>
      <?php endif; ?>
    </div>
  </section>

  <section class="detail-columns">

    <!-- Form rating (hanya untuk yang sudah login) -->
    <aside class="panel">
      <h2 class="panel-title">BERI RATING</h2>

      <?php if ($error != ""): ?>
        <p class="alert alert-error"><?= $error ?></p>
      <?php endif; ?>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <p class="muted">Kamu harus <a href="../login/index.php"><strong>log in</strong></a> untuk memberi rating dan komentar.</p>
      <?php else: ?>
        <form method="POST" action="">
          <div class="star-input">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= ($ratingSaya == $i) ? "checked" : "" ?> required>
              <label for="star<?= $i ?>" title="<?= $i ?> bintang">★</label>
            <?php endfor; ?>
          </div>

          <textarea name="comment" rows="4" maxlength="500" placeholder="Tulis komentar kamu tentang film ini..." required><?= htmlspecialchars($komentarSaya) ?></textarea>

          <button type="submit" name="kirim" class="btn btn-primary btn-block">
            <?= ($ratingSaya > 0) ? "PERBARUI REVIEW" : "KIRIM REVIEW" ?>
          </button>
        </form>
      <?php endif; ?>
    </aside>

    <!-- Daftar review -->
    <section class="panel">
      <h2 class="panel-title">REVIEW USER <span class="muted small"><?= $total ?></span></h2>

      <?php if ($total == 0): ?>
        <p class="muted">Belum ada review. Jadilah yang pertama.</p>
      <?php else: ?>
        <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
          <article class="review">
            <div class="review-top">
              <strong><?= htmlspecialchars($r['username']) ?></strong>
              <span class="gold"><?= str_repeat("★", $r['rating']) . str_repeat("☆", 5 - $r['rating']) ?></span>
            </div>
            <p class="muted small"><?= date("d M Y, H:i", strtotime($r['created_at'])) ?></p>
            <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>
    </section>

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
