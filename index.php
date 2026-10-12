<?php
// Koneksi database Cinepedia
$host = 'localhost';
$dbname = 'cinepedia_db';
$username = 'root';
$password = '';

$movies = [];
$dbError = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Rating diambil dari rata-rata review setiap film.
    $sql = "
        SELECT
            m.id,
            m.title,
            m.genre,
            m.release_year,
            m.duration,
            m.synopsis,
            m.poster_url,
            m.trailer_url,
            COALESCE(AVG(r.rating), 0) AS average_rating,
            COUNT(r.id) AS review_count
        FROM movies m
        LEFT JOIN reviews r ON r.movie_id = m.id
        GROUP BY
            m.id, m.title, m.genre, m.release_year, m.duration,
            m.synopsis, m.poster_url, m.trailer_url
        ORDER BY m.release_year DESC, m.title ASC
    ";

    $stmt = $pdo->query($sql);
    $movies = $stmt->fetchAll();
} catch (PDOException $e) {
    // Detail error tidak ditampilkan ke pengunjung demi keamanan.
    $dbError = 'Database belum dapat dihubungkan. Periksa konfigurasi database dan pastikan SQL sudah diimpor.';
}

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function posterUrl($url): string
{
    $url = trim((string)$url);

    // Hanya izinkan URL HTTP/HTTPS untuk poster eksternal.
    if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        if (in_array($scheme, ['http', 'https'], true)) {
            return $url;
        }
    }

    return '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cinepedia - Jelajahi database film, rating, genre, dan informasi film favoritmu.">
    <title>Cinepedia — Explore Movies</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="brand" aria-label="Cinepedia Home">
            <span class="brand-icon">C</span>
            <span>CINE<span class="red-text">PEDIA</span></span>
        </a>

        <div class="search-box">
            <span class="search-icon" aria-hidden="true">⌕</span>
            <input
                type="search"
                id="searchInput"
                placeholder="Search movies, genres..."
                aria-label="Cari film"
            >
            <button type="button" id="clearSearch" class="clear-search" aria-label="Hapus pencarian">×</button>
        </div>