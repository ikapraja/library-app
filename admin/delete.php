<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}

require_once '../db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // First, get the image_url to delete the physical file
    $sql = "SELECT image_url FROM posts WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_url = $row['image_url'];
        
        // Delete the record from database
        $delete_sql = "DELETE FROM posts WHERE id = $id";
        if ($conn->query($delete_sql) === TRUE) {
            // If there's an image and it exists on server, delete it
            if (!empty($image_url) && file_exists('../' . $image_url)) {
                unlink('../' . $image_url);
            }
        }
    }
}

header("Location: index");
exit;
?>
