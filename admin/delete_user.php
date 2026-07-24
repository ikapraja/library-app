<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}
if ($_SESSION['admin_role'] !== 'superadmin') {
    header("Location: index");
    exit;
}

require_once '../db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if ($id != 1 && $id != $_SESSION['admin_id']) {
        $delete_sql = "DELETE FROM users WHERE id = $id";
        $conn->query($delete_sql);
    }
}

header("Location: users");
exit;
?>
