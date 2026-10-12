<?php
require_once 'db.php';
session_start();

// Ambil ID film dari URL (default ke ID 1 jika tidak ada)
$movieId = isset($_GET['id']) ? intval($_GET['id']) : 1;

$reviewError = '';$reviewSuccess = '';

// Tangani pengiriman ulasan & rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {$reviewError = 'You must be logged in to submit a review and rating.';
    } else {
        $userId =$_SESSION['user_id'];
        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['review'] ?? '');

        if ($rating < 1 || $rating > 5) {$reviewError = 'Please select a star rating between 1 and 5.';
        } elseif (empty($comment)) {$reviewError = 'Please write your review comment.';
        } else {
            try {
                // Simpan ke tabel reviews
                $stmt =$pdo->prepare("INSERT INTO reviews (movie_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt->execute([$movieId, $userId,$rating, $comment]);$reviewSuccess = 'Your review has been successfully posted!';
            } catch (PDOException $e) {
                $reviewError = 'Database error: ' .$e->getMessage();
            }
        }
    }
}

// Ambil data film dari database
$stmt =$pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movieId]);
$movie =$stmt->fetch(PDO::FETCH_ASSOC);

// Jika film tidak ditemukan, ambil film pertama sebagai fallback
if (!$movie) {
    $stmt =$pdo->query("SELECT * FROM movies ORDER BY id ASC LIMIT 1");
    $movie =$stmt->fetch(PDO::FETCH_ASSOC);
    if ($movie) {
        $movieId =$movie['id'];
    }
}

// Ambil daftar ulasan dari tabel reviews dan users
$reviewsStmt =$pdo->prepare("
    SELECT r.*, u.username 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.movie_id = ? 
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute([$movieId]);
$reviews =$reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total ulasan dan rata-rata rating (dikonversi ke skala 10 untuk konsistensi tampilan)
$totalVotes = count($reviews);$avgRating = 'N/A';
if ($totalVotes > 0) {$sumRating = array_sum(array_column($reviews, 'rating'));$avgRating = number_format(($sumRating / $totalVotes) * 2, 1);
}

// Pecah genre jika disimpan dengan koma
$genres = $movie ? explode(',', $movie['genre']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#111111">
  <meta name="description" content="Cinepedia movie details, ratings, and reviews.">
  <title><?php echo $movie ? htmlspecialchars($movie['title']) : 'Movie Detail'; ?> — Cinepedia</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body data-page="detail" id="top">
  <div class="app-frame">
    <header class="site-header">
      <a class="site-logo" href="dashboard.php" aria-label="Cinepedia home">
        <span class="logo-badge">CP</span><span class="logo-word">CINEPEDIA</span>
      </a>
      <form class="search-box" id="detail-search-form" role="search" method="GET" action="">
        <span class="search-icon" aria-hidden="true">⌕</span>
        <input id="detail-search" name="search" type="search" placeholder="Search movies..." autocomplete="off" aria-label="Search movies">
      </form>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a class="login-link" href="logout.php">LOGOUT</a>
      <?php else: ?>
        <a class="login-link" href="login.php">LOG IN</a>
      <?php endif; ?>
    </header>

    <main class="detail-main">
      <?php if (!$movie): ?>
        <section class="panel" style="text-align: center; padding: 40px;">
          <h2>Movie not found in the database.</h2>
          <p><a href="dashboard.php" style="color: var(--accent);">Return to Dashboard</a></p>
        </section>
      <?php else: ?>
        <section class="movie-hero" aria-labelledby="movie-title">
          <div class="hero-poster poster-frame">
            <div class="poster-fallback" id="detail-poster-fallback">
              <span>CINEPEDIA</span>
              <strong id="poster-fallback-title"><?php echo htmlspecialchars($movie['title']); ?></strong>
              <small>OFFICIAL MOVIE ARCHIVE</small>
            </div>
            <?php if (!empty($movie['poster_url'])): ?>
              <img id="movie-poster" src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> movie poster" fetchpriority="high">
            <?php endif; ?>
          </div>
          <div class="hero-information">
            <div class="title-line">
              <h1 id="movie-title"><?php echo htmlspecialchars(strtoupper($movie['title'])); ?></h1>
              <span class="movie-year" id="movie-year">(<?php echo htmlspecialchars($movie['release_year']); ?>)</span>
            </div>
            <div class="genre-list" id="movie-genres">
              <?php foreach ($genres as$g): ?>
                <span class="genre-tag"><?php echo htmlspecialchars(trim($g)); ?></span>
              <?php endforeach; ?>
            </div>
            <div class="movie-meta">
              <span id="movie-runtime"><?php echo htmlspecialchars($movie['duration'] ?? 'N/A'); ?></span>
            </div>
            <div class="rating-summary">
              <span class="gold-star" aria-hidden="true">★</span>
              <strong id="movie-rating"><?php echo $avgRating; ?></strong><span class="rating-out-of">/10</span>
              <span class="rating-divider">|</span>
              <span id="movie-votes"><?php echo $totalVotes; ?></span><small>User Ratings</small>
            </div>
            <div class="synopsis-block">
              <h2>SYNOPSIS</h2>
              <p id="movie-synopsis"><?php echo nl2br(htmlspecialchars($movie['synopsis'] ?? 'No synopsis available.')); ?></p>
            </div>
            <?php if (!empty($movie['trailer_url'])): ?>
              <a class="trailer-button" id="trailer-link" href="<?php echo htmlspecialchars($movie['trailer_url']); ?>" target="_blank" rel="noopener noreferrer">
                <span aria-hidden="true">▶</span> WATCH YOUTUBE TRAILER
              </a>
            <?php endif; ?>
          </div>
        </section>

        <section class="detail-columns">
          <aside class="rating-panel panel" aria-labelledby="rate-heading">
            <h2 class="panel-heading" id="rate-heading">RATE THIS MOVIE</h2>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
              <p class="panel-help" style="color: var(--accent);">Please <a href="login.php" style="color: #fff; text-decoration: underline;">log in</a> to rate and review this movie.</p>
            <?php else: ?>
              <p class="panel-help">Select stars and write your review below.</p>
              
              <?php if (!empty($reviewError)): ?>
                <p class="review-form-status is-error" role="status"><?php echo htmlspecialchars($reviewError); ?></p>
              <?php elseif (!empty($reviewSuccess)): ?>
                <p class="review-form-status is-success" role="status"><?php echo htmlspecialchars($reviewSuccess); ?></p>
              <?php endif; ?>

              <form id="review-form" class="review-form" method="POST" action="">
                <div class="personal-rating" id="personal-rating" role="group" aria-label="Rate this movie from one to five stars">
                  <div class="star-buttons">
                    <button type="button" class="rating-star" data-rating="1" aria-label="1 star">★</button>
                    <button type="button" class="rating-star" data-rating="2" aria-label="2 stars">★</button>
                    <button type="button" class="rating-star" data-rating="3" aria-label="3 stars">★</button>
                    <button type="button" class="rating-star" data-rating="4" aria-label="4 stars">★</button>
                    <button type="button" class="rating-star" data-rating="5" aria-label="5 stars">★</button>
                  </div>
                  <input type="hidden" name="rating" id="rating-value" value="5">
                  <p id="rating-prompt">SELECTED: 5 / 5 STARS</p>
                </div>

                <label class="visually-hidden" for="review-text">Your movie review</label>
                <textarea id="review-text" name="review" rows="4" maxlength="500" placeholder="Write your review or comment about this movie..." required></textarea>
                
                <button class="submit-review" type="submit" name="submit_review">SUBMIT COMMENT</button>
              </form>
            <?php endif; ?>
          </aside>

          <section class="reviews-panel panel" aria-labelledby="reviews-heading">
            <div class="reviews-header">
              <h2 class="panel-heading" id="reviews-heading">USER REVIEWS</h2>
              <span class="review-count" id="review-count"><?php echo $totalVotes; ?> <?php echo$totalVotes === 1 ? 'REVIEW' : 'REVIEWS'; ?></span>
            </div>
            <div class="reviews-list" id="reviews-list" aria-live="polite">
              <?php if (empty($reviews)): ?>
                <p class="no-reviews">No reviews yet. Be the first to share your thoughts.</p>
              <?php else: ?>
                <?php foreach ($reviews as$rev): ?>
                  <article class="review-card">
                    <div class="review-card-top">
                      <h3 class="review-author"><?php echo htmlspecialchars($rev['username']); ?></h3>
                      <span class="review-stars">
                        <?php 
                          $rVal = intval($rev['rating']);
                          echo str_repeat('★', $rVal) . str_repeat('☆', 5 - $rVal);
                        ?>
                      </span>
                    </div>
                    <p class="review-date"><?php echo date('d M Y, H:i', strtotime($rev['created_at'])); ?></p>
                    <p class="review-body"><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                  </article>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </section>
        </section>
      <?php endif; ?>
    </main>

    <footer class="site-footer">
      <a class="footer-brand" href="#top"><span class="mini-badge">CP</span> CINEPEDIA</a>
      <span>© <span id="current-year"><?php echo date('Y'); ?></span> All Rights Reserved.</span>
      <nav aria-label="Footer navigation"><a href="#">Privacy</a><a href="#">Terms</a><a href="login.php">Contact</a></nav>
    </footer>
  </div>

  <script>
    // Interaktif pemilihan bintang rating
    const starButtons = document.querySelectorAll(".rating-star");
    const ratingInput = document.querySelector("#rating-value");
    const ratingPrompt = document.querySelector("#rating-prompt");

    if (starButtons.length > 0) {
      let currentRating = 5;

      function updateStars(val) {
        starButtons.forEach(btn => {
          const starVal = Number(btn.dataset.rating);
          if (starVal <= val) {
            btn.classList.add("is-selected");
          } else {
            btn.classList.remove("is-selected");
          }
        });
        if (ratingPrompt) ratingPrompt.textContent = `SELECTED: ${val} / 5 STARS`;
      }

      starButtons.forEach(btn => {
        btn.addEventListener("mouseenter", () => {
          const val = Number(btn.dataset.rating);
          starButtons.forEach(b => {
            b.classList.toggle("is-hover", Number(b.dataset.rating) <= val);
          });
        });
        btn.addEventListener("mouseleave", () => {
          starButtons.forEach(b => b.classList.remove("is-hover"));
        });
        btn.addEventListener("click", () => {
          currentRating = Number(btn.dataset.rating);
          ratingInput.value = currentRating;
          updateStars(currentRating);
        });
      });

      updateStars(currentRating);
    }
  </script>
</body>
</html>