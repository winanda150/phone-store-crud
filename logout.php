<?php
    session_start();
    include_once 'koneksi.php';

    // Set status (sebagai last_activity) menjadi NULL saat logout
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("UPDATE user SET status = NULL WHERE id = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }

    session_unset();
    session_destroy();

    header("Location: login.php");
    exit();
?>