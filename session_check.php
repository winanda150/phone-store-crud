<?php
session_start();

// Jika tidak ada sesi login, arahkan ke halaman login.
if (!isset($_SESSION['login']) || !isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

include_once 'koneksi.php';

// Perbarui timestamp 'status' (sebagai last_activity) untuk user yang sedang login.
$current_user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("UPDATE user SET status = NOW() WHERE id = ?");
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$stmt->close();
?>