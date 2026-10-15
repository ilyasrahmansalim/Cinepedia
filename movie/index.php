<?php
session_start();
include "../conn.php";

// Ambil pencarian, filter genre, dan urutan dari URL.
// "?? ''" artinya: kalau datanya tidak ada di URL, pakai teks kosong.
$q     = mysqli_real_escape_string($conn, trim($_GET['q'] ?? ""));
$genre = mysqli_real_escape_string($conn, $_GET['genre'] ?? "");
$sort  = $_GET['sort'] ?? "";

// Urutan: film terbaru, atau rating tertinggi
$urut = "m.release_year DESC";
if ($sort == "top") {
    $urut = "rata DESC";
}

// Ambil film + rata-rata rating dari tabel reviews.
// LIKE '%%' (genre kosong) cocok dengan semua film.
$result = mysqli_query($conn, "SELECT m.*, AVG(r.rating) AS rata, COUNT(r.id) AS jumlah
                               FROM movies m
                               LEFT JOIN reviews r ON r.movie_id = m.id
                               WHERE (m.title LIKE '%$q%' OR m.genre LIKE '%$q%' OR m.release_year LIKE '%$q%')
                               AND m.genre LIKE '%$genre%'
                               GROUP BY m.id
                               ORDER BY $urut");

// ===== CAROUSEL: 5 film acak yang punya sinopsis =====
// Hanya tampil di halaman utama (saat tidak sedang mencari / memfilter).
$jumlahSlide = 0;
if ($q == "" && $genre == "" && $sort == "") {
    $carousel = mysqli_query($conn, "SELECT id, title, genre, release_year, synopsis, poster_url
                                     FROM movies
                                     WHERE synopsis IS NOT NULL AND synopsis != ''
                                     ORDER BY RAND()
                                     LIMIT 5");
    $jumlahSlide = mysqli_num_rows($carousel);
}

$daftarGenre = array("Action", "Adventure", "Animation", "Biography", "Comedy", "Crime", "Drama", "Mystery", "Sci-Fi", "Thriller");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Explore Movies — Cinepedia</title>
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

  <?php if ($jumlahSlide > 0): ?>
    <!-- Carousel: hanya tampilan (tidak bisa diklik) -->
    <section class="carousel" id="carousel" aria-label="Film pilihan">
      <div class="carousel-track" id="carouselTrack">
        <?php while ($c = mysqli_fetch_assoc($carousel)): ?>
          <div class="carousel-slide">
            <div class="poster-wrap">
              <?php if ($c['poster_url'] != ""): ?>
                <img class="poster" src="<?= htmlspecialchars($c['poster_url']) ?>" alt="Poster <?= htmlspecialchars($c['title']) ?>" onerror="this.style.display='none'">
              <?php endif; ?>
            </div>
            <div class="carousel-info">
              <span class="carousel-label">FILM PILIHAN</span>
              <h2><?= htmlspecialchars($c['title']) ?></h2>
              <p class="carousel-meta"><?= $c['release_year'] ?> · <?= htmlspecialchars($c['genre']) ?></p>
              <p class="carousel-synopsis"><?= htmlspecialchars($c['synopsis']) ?></p>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      <?php if ($jumlahSlide > 1): ?>
        <button type="button" class="carousel-btn prev" id="carouselPrev" aria-label="Sebelumnya">‹</button>
        <button type="button" class="carousel-btn next" id="carouselNext" aria-label="Berikutnya">›</button>
        <div class="carousel-dots">
          <?php for ($i = 0; $i < $jumlahSlide; $i++): ?>
            <button type="button" class="carousel-dot <?= ($i == 0) ? "active" : "" ?>" aria-label="Slide <?= $i + 1 ?>"></button>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <div class="page-heading">
    <h1><span class="bar"></span> EXPLORE MOVIES</h1>

    <nav class="filter-tabs">
      <a class="tab <?= ($genre == "" && $sort == "") ? "active" : "" ?>" href="index.php">ALL</a>
      <a class="tab <?= ($sort == "top") ? "active" : "" ?>" href="index.php?sort=top">TOP RATED</a>
      <?php foreach ($daftarGenre as $g): ?>
        <a class="tab <?= ($genre == $g) ? "active" : "" ?>" href="index.php?genre=<?= $g ?>"><?= strtoupper($g) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>

  <?php if ($q != ""): ?>
    <p class="muted">Hasil pencarian untuk: <strong><?= htmlspecialchars($_GET['q']) ?></strong></p>
  <?php endif; ?>

  <?php if (mysqli_num_rows($result) == 0): ?>
    <div class="empty">
      <h2>Film tidak ditemukan</h2>
      <p>Coba kata kunci atau filter lain.</p>
      <a class="btn btn-primary" href="index.php">LIHAT SEMUA FILM</a>
    </div>
  <?php else: ?>
    <div class="movie-grid">
      <?php while ($m = mysqli_fetch_assoc($result)): ?>
        <a class="movie-card" href="detail.php?id=<?= $m['id'] ?>">
          <div class="poster-wrap">
            <?php if ($m['poster_url'] != ""): ?>
              <img class="poster" src="<?= htmlspecialchars($m['poster_url']) ?>" alt="Poster <?= htmlspecialchars($m['title']) ?>" loading="lazy" onerror="this.style.display='none'">
            <?php endif; ?>
            <span class="badge badge-rating">★ <?= ($m['jumlah'] > 0) ? number_format($m['rata'], 1) : "-" ?></span>
            <span class="badge badge-year"><?= $m['release_year'] ?></span>
          </div>
          <div class="card-info">
            <h2><?= htmlspecialchars($m['title']) ?></h2>
            <div class="card-meta">
              <span><?= htmlspecialchars($m['genre']) ?></span>
              <span><?= htmlspecialchars($m['duration'] ?? "") ?></span>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
  <span><strong>CINEPEDIA</strong> — Movie Catalog</span>
  <span>&copy; <?= date("Y") ?> All Rights Reserved.</span>
</footer>

<script src="../assets/app.js"></script>
</body>
</html>