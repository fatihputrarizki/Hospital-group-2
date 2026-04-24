<?php
// ============================================================
// Konfigurasi Koneksi Database
// Sesuaikan dengan pengaturan XAMPP kamu
// ============================================================

$host   = "localhost";
$user   = "root";
$pass   = "";           // Default XAMPP: kosong
$dbname = "hospital_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("<div style='font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border-radius:8px;margin:20px;'>
        <strong>❌ Koneksi Database Gagal</strong><br>
        " . mysqli_connect_error() . "<br><br>
        Pastikan XAMPP sudah berjalan dan database <code>hospital_db</code> sudah diimport.
    </div>");
}

mysqli_set_charset($conn, "utf8mb4");
?>
