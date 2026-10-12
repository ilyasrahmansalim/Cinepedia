<?php
// Inisialisasi bersama: session, koneksi database, dan fungsi bantu.
session_start();

// BASE_URL dihitung otomatis dari lokasi folder proyek terhadap document root,
// jadi proyek bisa ditaruh di htdocs/cinepedia atau di root domain tanpa ubah apa pun.
$docRoot = rtrim(str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
$appRoot = str_replace('\\', '/', (string)realpath(__DIR__ . '/..'));
define('BASE_URL', ($docRoot !== '' && strpos($appRoot, $docRoot) === 0) ? substr($appRoot, strlen($docRoot)) : '');

const GENRES = ['Action', 'Adventure', 'Animation', 'Biography', 'Comedy', 'Crime', 'Drama', 'Mystery', 'Sci-Fi', 'Thriller'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=cinepedia_db;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database tidak dapat dihubungkan. Pastikan database.sql sudah diimpor.');
}

/* ---------- Helper umum ---------- */

function e($v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . $path;
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flash(?string $msg = null, string $type = 'success'): ?array
{
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function stars(int $n): string
{
    $n = max(0, min(5, $n));
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

function valid_http_url(string $u): bool
{
    return filter_var($u, FILTER_VALIDATE_URL) !== false
        && in_array(strtolower((string)parse_url($u, PHP_URL_SCHEME)), ['http', 'https'], true);
}

/* ---------- Autentikasi ---------- */

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

// Hanya izinkan redirect ke path internal (cegah open redirect).
function safe_next(?string $next): string
{
    if ($next && $next[0] === '/' && strpos($next, '//') !== 0 && strpos($next, '\\') === false) {
        return $next;
    }
    return '';
}

// Admin only: tamu diarahkan ke login, user biasa ditolak 403.
function require_admin(): void
{
    if (!is_logged_in()) {
        redirect('/login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
    }
    if (current_user()['role'] !== 'admin') {
        http_response_code(403);
        die('<h1>403 - Akses ditolak</h1><p>Halaman ini hanya untuk admin. <a href="' . e(url('/index.php')) . '">Kembali</a></p>');
    }
}

/* ---------- CSRF ---------- */

function csrf_field(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}

function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die('Permintaan tidak valid (CSRF). Muat ulang halaman lalu coba lagi.');
    }
}

/* ---------- Validasi input film (dipakai tambah & edit) ---------- */

function movie_input(array $src): array
{
    $d = [
        'title'        => trim($src['title'] ?? ''),
        'genre'        => trim($src['genre'] ?? ''),
        'release_year' => (int)($src['release_year'] ?? 0),
        'duration'     => trim($src['duration'] ?? ''),
        'poster_url'   => trim($src['poster_url'] ?? ''),
        'trailer_url'  => trim($src['trailer_url'] ?? ''),
        'synopsis'     => trim($src['synopsis'] ?? ''),
    ];
    $err = [];
    if ($d['title'] === '' || mb_strlen($d['title']) > 255) $err[] = 'Judul wajib diisi (maks 255 karakter).';
    if ($d['genre'] === '' || mb_strlen($d['genre']) > 100) $err[] = 'Genre wajib diisi (maks 100 karakter).';
    if ($d['release_year'] < 1888 || $d['release_year'] > 2100) $err[] = 'Tahun rilis tidak valid.';
    if (mb_strlen($d['duration']) > 50) $err[] = 'Durasi terlalu panjang.';
    foreach (['poster_url' => 'Poster', 'trailer_url' => 'Trailer'] as $k => $label) {
        if ($d[$k] !== '' && (!valid_http_url($d[$k]) || strlen($d[$k]) > 255)) {
            $err[] = "URL $label harus berupa link http/https yang valid.";
        }
    }
    return [$d, $err];
}
