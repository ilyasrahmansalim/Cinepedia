<?php
// Field form film yang dipakai bersama oleh tambah (admin/index.php) dan edit (admin/edit.php).
// Variabel: $m (array nilai saat ini)
?>
<div class="form-grid">
  <div class="form-group full">
    <label for="title">JUDUL FILM</label>
    <input type="text" id="title" name="title" value="<?= e($m['title'] ?? '') ?>" maxlength="255" required>
  </div>
  <div class="form-group">
    <label for="genre">GENRE (pisahkan dengan koma)</label>
    <input type="text" id="genre" name="genre" value="<?= e($m['genre'] ?? '') ?>" list="genre-list" maxlength="100" placeholder="Drama, Sci-Fi" required>
    <datalist id="genre-list"><?php foreach (GENRES as $g): ?><option value="<?= e($g) ?>"><?php endforeach; ?></datalist>
  </div>
  <div class="form-group">
    <label for="release_year">TAHUN RILIS</label>
    <input type="number" id="release_year" name="release_year" value="<?= e($m['release_year'] ?? '') ?>" min="1888" max="2100" required>
  </div>
  <div class="form-group">
    <label for="duration">DURASI</label>
    <input type="text" id="duration" name="duration" value="<?= e($m['duration'] ?? '') ?>" maxlength="50" placeholder="2h 30m">
  </div>
  <div class="form-group">
    <label for="trailer_url">URL TRAILER YOUTUBE</label>
    <input type="url" id="trailer_url" name="trailer_url" value="<?= e($m['trailer_url'] ?? '') ?>" placeholder="https://youtube.com/watch?v=...">
  </div>
  <div class="form-group full">
    <label for="poster_url">URL POSTER</label>
    <input type="url" id="poster_url" name="poster_url" value="<?= e($m['poster_url'] ?? '') ?>" placeholder="https://.../poster.jpg" data-poster-input>
  </div>
  <div class="form-group full">
    <label for="synopsis">SINOPSIS</label>
    <textarea id="synopsis" name="synopsis" rows="4"><?= e($m['synopsis'] ?? '') ?></textarea>
  </div>
</div>
