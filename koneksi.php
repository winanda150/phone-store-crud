<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_toko";

// Matikan exception otomatis agar pesan error ramah pengguna bisa ditampilkan
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Asia/Jakarta');
