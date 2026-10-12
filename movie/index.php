<?php
// Detail film + rating & komentar. Menulis rating/komentar wajib login.
require_once __DIR__ . '/../includes/init.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    http_response_code(404);
    $pageTitle = 'Film tidak ditemukan';
    require __DIR__ . '/../includes/header.php';
    echo '<div class="empty"><h2>Film tidak ditemukan</h2><a class="btn btn-primary" href="' . e(url('/index.php')) . '">KEMBALI KE KATALOG</a></div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$self = '/movie/?id=' . $id;

// --- Kirim / ubah rating & komentar ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!is_logged_in()) {
        redirect('/login.php?next=' . urlencode($self));
    }

    $rating  = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        flash('Pilih rating bintang 1 sampai 5.', 'error');
    } elseif ($comment === '' || mb_strlen($comment) > 500) {
        flash('Komentar wajib diisi (maks 500 karakter).', 'error');
    } else {
        // Satu review per user per film: kirim ulang akan memperbarui review lama.
        $pdo->prepare(
            'INSERT INTO reviews (movie_id, user_id, rating, comment) VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), created_at = NOW()'
        )->execute([$id, current_user()['id'], $rating, $comment]);
        flash('Review kamu berhasil disimpan.');
    }
    redirect($self);
}

// --- Data review & rata-rata ---
$stmt = $pdo->prepare(
    'SELECT r.*, u.username FROM reviews r JOIN users u ON u.id = r.user_id
     WHERE r.movie_id = ? ORDER BY r.created_at DESC'
);
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();

$total = count($reviews);
$avg   = $total ? array_sum(array_column($reviews, 'rating')) / $total : 0;

// Review milik user yang sedang login (untuk prefill form)
$mine = null;
if (is_logged_in()) {
    foreach ($reviews as $r) {
        if ((int)$r['user_id'] === current_user()['id']) { $mine = $r; break; }
    }
}

$genres    = array_filter(array_map('trim', explode(',', $movie['genre'])));
$pageTitle = $movie['title'];
require __DIR__ . '/../includes/header.php';
?>
<section class="panel movie-hero">
  <div class="poster-wrap hero-poster">
    <?php if ($movie['poster_url']): ?>
      <img class="poster" src="<?= e($movie['poster_url']) ?>" alt="Poster <?= e($movie['title']) ?>">
    <?php endif; ?>
  </div>
  <div>
    <h1 class="movie-title"><?= e(strtoupper($movie['title'])) ?> <small>(<?= e($movie['release_year']) ?>)</small></h1>
    <div class="tags"><?php foreach ($genres as $g): ?><span class="tag"><?= e($g) ?></span><?php endforeach; ?></div>
    <?php if ($movie['duration']): ?><p class="muted small"><?= e($movie['duration']) ?></p><?php endif; ?>

    <p class="rating-line">
      <span class="gold">★</span>
      <strong><?= $total ? number_format($avg, 1) : 'N/A' ?></strong><span class="muted">/5</span>
      <span class="muted">· <?= $total ?> rating</span>
    </p>

    <h2 class="label">SINOPSIS</h2>
    <p class="synopsis"><?= $movie['synopsis'] ? nl2br(e($movie['synopsis'])) : 'Belum ada sinopsis.' ?></p>

    <?php if ($movie['trailer_url']): ?>
      <a class="btn btn-primary" href="<?= e($movie['trailer_url']) ?>" target="_blank" rel="noopener noreferrer">▶ TONTON TRAILER</a>
    <?php endif; ?>
  </div>
</section>

<section class="detail-columns">
  <aside class="panel">
    <h2 class="panel-title">BERI RATING</h2>
    <?php if (!is_logged_in()): ?>
      <p class="muted">Kamu harus <a href="<?= e(url('/login.php?next=' . urlencode($self))) ?>"><strong>log in</strong></a> untuk memberi rating dan komentar.</p>
    <?php else: ?>
      <form method="POST" action="">
        <?= csrf_field() ?>
        <div class="star-input" role="radiogroup" aria-label="Rating 1 sampai 5">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= $mine && (int)$mine['rating'] === $i ? 'checked' : '' ?> required>
            <label for="star<?= $i ?>" title="<?= $i ?> bintang">★</label>
          <?php endfor; ?>
        </div>
        <textarea name="comment" rows="4" maxlength="500" placeholder="Tulis komentar kamu tentang film ini..." required><?= e($mine['comment'] ?? '') ?></textarea>
        <button type="submit" class="btn btn-primary btn-block"><?= $mine ? 'PERBARUI REVIEW' : 'KIRIM REVIEW' ?></button>
      </form>
    <?php endif; ?>
  </aside>

  <section class="panel">
    <h2 class="panel-title">REVIEW USER <span class="muted small"><?= $total ?></span></h2>
    <?php if (!$reviews): ?>
      <p class="muted">Belum ada review. Jadilah yang pertama.</p>
    <?php else: ?>
      <?php foreach ($reviews as $r): ?>
        <article class="review">
          <div class="review-top">
            <strong><?= e($r['username']) ?></strong>
            <span class="gold"><?= stars((int)$r['rating']) ?></span>
          </div>
          <p class="muted small"><?= e(date('d M Y, H:i', strtotime($r['created_at']))) ?></p>
          <p><?= nl2br(e($r['comment'])) ?></p>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
