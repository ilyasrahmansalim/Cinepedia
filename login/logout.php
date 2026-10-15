<?php
// Hapus semua data login, lalu kembali ke katalog.
session_start();
session_unset();
session_destroy();

header("Location: ../movie/index.php");
exit;
