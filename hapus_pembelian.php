<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'ID transaksi tidak valid.';
    header('Location: pembelian.php');
    exit;
}

$id = intval($_GET['id']);
$delete = mysqli_query($conn, "DELETE FROM pembelian WHERE id=$id");

if ($delete) {
    $_SESSION['success'] = 'Transaksi berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Gagal menghapus transaksi: ' . mysqli_error($conn);
}
header('Location: pembelian.php');
exit;