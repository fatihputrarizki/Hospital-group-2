<?php
// ============================================================
// Konfigurasi Koneksi Database
// Sesuaikan dengan pengaturan XAMPP kamu
// ============================================================

$host   = "localhost";
$user   = "root";
$pass   = "";           // Default XAMPP: empty
$dbname = "hospital_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("<div style='font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border-radius:8px;margin:20px;'>
        <strong>❌ Database Connection Failed</strong><br>
        " . mysqli_connect_error() . "<br><br>
        Make sure XAMPP is running and the <code>hospital_db</code> database has been imported.
    </div>");
}

mysqli_set_charset($conn, "utf8mb4");
?>
