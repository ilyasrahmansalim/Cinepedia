<?php
// Koneksi ke database MySQL (phpMyAdmin).
// Ubah isinya kalau username/password MySQL kamu berbeda.
$conn = mysqli_connect("localhost", "root", "", "cinepedia_db");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Supaya karakter seperti ★ tersimpan dengan benar
mysqli_set_charset($conn, "utf8mb4");
