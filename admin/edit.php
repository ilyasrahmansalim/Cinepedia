<?php
// Admin: edit film. Hanya role admin.
require_once __DIR__ . '/../includes/init.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    flash('Film tidak ditemukan.', 'error');
    redirect('/admin/');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = movie_input($_POST);
    if (!$errors) {
        $data['id'] = $id;
        $pdo->prepare(
            'UPDATE movies SET title = :title, genre = :genre, release_year = :release_year,
             duration = :duration, poster_url = :poster_url, trailer_url = :trailer_url, synopsis = :synopsis
             WHERE id = :id'
        )->execute($data);
        flash('Film berhasil diperbarui.');
        redirect('/admin/');
    }
    $movie = array_merge($movie, $data); // tampilkan kembali input yang salah
}

$m = $movie;
$pageTitle = 'Edit ' . $movie['title'];
require __DIR__ . '/../includes/header.php';
?>
<a class="back-link" href="<?= e(url('/admin/')) ?>">← Kembali ke database film</a>

<?php foreach ($errors as $err): ?><p class="alert alert-error"><?= e($err) ?></p><?php endforeach; ?>

<section class="panel">
  <h2 class="panel-title">EDIT FILM <span class="muted small">ID #<?= (int)$id ?></span></h2>
  <form method="POST" action="">
    <?= csrf_field() ?>
    <div class="edit-layout">
      <div class="poster-wrap hero-poster preview"><?php if ($m['poster_url']): ?><img class="poster" id="poster-preview" src="<?= e($m['poster_url']) ?>" alt="Preview poster"><?php endif; ?></div>
      <div><?php require __DIR__ . '/../includes/movie_fields.php'; ?></div>
    </div>
    <div class="form-actions">
      <a class="btn btn-secondary" href="<?= e(url('/admin/')) ?>">BATAL</a>
      <button type="submit" class="btn btn-primary">SIMPAN PERUBAHAN</button>
    </div>
  </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
