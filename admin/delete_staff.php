<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM staff_members WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Anggota berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . $conn->error;
    }
}
header("Location: staff");
exit;
?>
