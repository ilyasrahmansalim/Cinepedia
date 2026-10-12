<?php
// Variabel opsional dari halaman: $pageTitle
$user = current_user();
$flash = flash();
?><!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Cinepedia') ?> — Cinepedia</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('/assets/style.css')) ?>">
</head>
<body>
<header class="navbar">
  <a class="brand" href="<?= e(url('/index.php')) ?>"><span class="brand-badge">C</span> CINEPEDIA</a>

  <form class="search-box" method="GET" action="<?= e(url('/index.php')) ?>" role="search">
    <input type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Cari judul, genre, atau tahun..." aria-label="Cari film">
  </form>

  <?php if ($user): ?>
    <div class="user-dropdown">
      <button type="button" class="profile-btn" data-dropdown>
        <svg class="profile-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7z"/></svg>
        <span class="profile-name"><?= e($user['username']) ?></span>
        <span class="caret">▾</span>
      </button>
      <div class="dropdown-menu">
        <div class="dropdown-head">
          <strong><?= e($user['username']) ?></strong>
          <small><?= e($user['role']) ?></small>
        </div>
        <?php if ($user['role'] === 'admin'): ?>
          <a class="dropdown-item" href="<?= e(url('/admin/')) ?>">Admin Panel</a>
        <?php endif; ?>
        <form method="POST" action="<?= e(url('/logout.php')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="dropdown-item danger">Logout</button>
        </form>
      </div>
    </div>
  <?php else: ?>
    <a class="btn btn-primary btn-nav" href="<?= e(url('/login.php?next=' . urlencode($_SERVER['REQUEST_URI']))) ?>">LOG IN</a>
  <?php endif; ?>
</header>

<main class="container">
<?php if ($flash): ?>
  <p class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></p>
<?php endif; ?>
