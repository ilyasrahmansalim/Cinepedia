<?php
// Admin: daftar film + tambah + hapus (CRUD). Hanya role admin.
require_once __DIR__ . '/../includes/init.php';
require_admin();

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        [$data, $errors] = movie_input($_POST);
        if (!$errors) {
            $pdo->prepare(
                'INSERT INTO movies (title, genre, release_year, duration, poster_url, trailer_url, synopsis)
                 VALUES (:title, :genre, :release_year, :duration, :poster_url, :trailer_url, :synopsis)'
            )->execute($data);
            flash('Film berhasil ditambahkan.');
            redirect('/admin/');
        }
        $old = $data;
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM movies WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
        flash('Film berhasil dihapus.');
        redirect('/admin/');
    }
}

$q = trim($_GET['q'] ?? '');
$stmt = $pdo->prepare(
    'SELECT m.*, COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS review_count
     FROM movies m LEFT JOIN reviews r ON r.movie_id = m.id
     WHERE m.title LIKE :q1 OR m.genre LIKE :q2
     GROUP BY m.id ORDER BY m.id DESC'
);
$stmt->execute([':q1' => "%$q%", ':q2' => "%$q%"]);
$movies = $stmt->fetchAll();

$pageTitle = 'Admin';
require __DIR__ . '/../includes/header.php';
?>
<?php foreach ($errors as $err): ?><p class="alert alert-error"><?= e($err) ?></p><?php endforeach; ?>

<section class="panel">
  <h2 class="panel-title">TAMBAH FILM BARU</h2>
  <form method="POST" action="<?= e(url('/admin/')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <?php $m = $old; require __DIR__ . '/../includes/movie_fields.php'; ?>
    <div class="form-actions"><button type="submit" class="btn btn-primary">TAMBAH FILM</button></div>
  </form>
</section>

<section class="panel">
  <div class="panel-head">
    <h2 class="panel-title">DATABASE FILM <span class="muted small"><?= count($movies) ?> judul</span></h2>
    <form method="GET" action="" class="inline-search">
      <input type="search" name="q" value="<?= e($q) ?>" placeholder="Filter judul atau genre...">
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>POSTER</th><th>JUDUL</th><th>GENRE</th><th>RATING</th><th>TRAILER</th><th class="right">AKSI</th></tr>
      </thead>
      <tbody>
      <?php if (!$movies): ?>
        <tr><td colspan="6" class="center muted">Belum ada data film.</td></tr>
      <?php endif; ?>
      <?php foreach ($movies as $row): ?>
        <tr>
          <td><div class="poster-wrap thumb"><?php if ($row['poster_url']): ?><img class="poster" src="<?= e($row['poster_url']) ?>" alt=""><?php endif; ?></div></td>
          <td><strong><?= e($row['title']) ?></strong><br><span class="muted small"><?= e($row['release_year']) ?> · <?= e($row['duration']) ?></span></td>
          <td><span class="tag"><?= e($row['genre']) ?></span></td>
          <td class="gold">★ <?= $row['review_count'] ? number_format((float)$row['avg_rating'], 1) : '–' ?></td>
          <td><?php if ($row['trailer_url']): ?><a href="<?= e($row['trailer_url']) ?>" target="_blank" rel="noopener noreferrer">Tonton</a><?php else: ?><span class="muted">—</span><?php endif; ?></td>
          <td class="right">
            <div class="actions">
              <a class="btn btn-secondary btn-sm" href="<?= e(url('/movie/?id=' . $row['id'])) ?>">LIHAT</a>
              <a class="btn btn-secondary btn-sm" href="<?= e(url('/admin/edit.php?id=' . $row['id'])) ?>">EDIT</a>
              <form method="POST" action="" data-confirm="Yakin ingin menghapus film ini? Semua review-nya ikut terhapus.">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">HAPUS</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
