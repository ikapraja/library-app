<?php
require_once 'db.php';

$sql = "ALTER TABLE posts ADD COLUMN views INT DEFAULT 0";

if ($conn->query($sql) === TRUE) {
    echo "Column views added successfully.";
} else {
    echo "Error adding column: " . $conn->error;
}
$conn->close();
?>
