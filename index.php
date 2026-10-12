<?php
// Katalog film (publik). Pencarian & filter diproses di PHP lewat query string.
require_once __DIR__ . '/includes/init.php';

$q     = trim($_GET['q'] ?? '');
$genre = in_array($_GET['genre'] ?? '', GENRES, true) ? $_GET['genre'] : '';
$sort  = ($_GET['sort'] ?? '') === 'top' ? 'top' : 'new';

$where  = [];
$params = [];
if ($q !== '') {
    $where[] = '(m.title LIKE :q1 OR m.genre LIKE :q2 OR m.release_year LIKE :q3)';
    $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $q . '%';
}
if ($genre !== '') {
    $where[] = 'm.genre LIKE :g';
    $params[':g'] = '%' . $genre . '%';
}

$sql = 'SELECT m.*, COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS review_count
        FROM movies m LEFT JOIN reviews r ON r.movie_id = m.id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . ' GROUP BY m.id '
     . ($sort === 'top' ? 'ORDER BY avg_rating DESC, review_count DESC, m.title' : 'ORDER BY m.release_year DESC, m.title');

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();

// Bangun link tab filter sambil mempertahankan kata kunci pencarian.
function tab_url(string $genre, string $sort, string $q): string
{
    $p = array_filter(['q' => $q, 'genre' => $genre, 'sort' => $sort === 'top' ? 'top' : '']);
    return url('/index.php') . ($p ? '?' . http_build_query($p) : '');
}

$pageTitle = 'Explore Movies';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
  <h1><span class="bar"></span> EXPLORE MOVIES</h1>
  <nav class="filter-tabs" aria-label="Filter">
    <a class="tab <?= $genre === '' && $sort === 'new' ? 'active' : '' ?>" href="<?= e(tab_url('', 'new', $q)) ?>">ALL</a>
    <a class="tab <?= $sort === 'top' ? 'active' : '' ?>" href="<?= e(tab_url('', 'top', $q)) ?>">TOP RATED</a>
    <?php foreach (GENRES as $g): ?>
      <a class="tab <?= $genre === $g ? 'active' : '' ?>" href="<?= e(tab_url($g, 'new', $q)) ?>"><?= e(strtoupper($g)) ?></a>
    <?php endforeach; ?>
  </nav>
</div>

<?php if (!$movies): ?>
  <div class="empty">
    <h2>Film tidak ditemukan</h2>
    <p>Coba kata kunci atau filter lain.</p>
    <a class="btn btn-primary" href="<?= e(url('/index.php')) ?>">LIHAT SEMUA FILM</a>
  </div>
<?php else: ?>
  <div class="movie-grid">
    <?php foreach ($movies as $m): ?>
      <a class="movie-card" href="<?= e(url('/movie/?id=' . $m['id'])) ?>">
        <div class="poster-wrap">
          <?php if ($m['poster_url']): ?>
            <img class="poster" src="<?= e($m['poster_url']) ?>" alt="Poster <?= e($m['title']) ?>" loading="lazy">
          <?php endif; ?>
          <span class="badge badge-rating">★ <?= $m['review_count'] ? number_format((float)$m['avg_rating'], 1) : '–' ?></span>
          <span class="badge badge-year"><?= e($m['release_year']) ?></span>
        </div>
        <div class="card-info">
          <h2><?= e($m['title']) ?></h2>
          <div class="card-meta"><span><?= e($m['genre']) ?></span><span><?= e($m['duration']) ?></span></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
