<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}

require_once '../db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get image_url to delete the physical file
    $sql = "SELECT image_url FROM gallery WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Delete physical file
        if ($row['image_url'] && file_exists('../' . $row['image_url'])) {
            unlink('../' . $row['image_url']);
        }
        
        // Delete record
        $delete_sql = "DELETE FROM gallery WHERE id = $id";
        $conn->query($delete_sql);
    }
}

header("Location: gallery");
exit;
?>
