<?php
require_once __DIR__ . '/includes/init.php';

// Logout hanya lewat POST + CSRF agar tidak bisa dipicu dari link luar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $_SESSION = [];
    session_destroy();
}
header('Location: ' . url('/index.php'));
exit;
