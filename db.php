<?php
date_default_timezone_set('Asia/Jakarta');
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "library_app_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Visitor tracking dipindahkan ke includes/visitor_tracker.php agar pengunjung admin tidak ikut terhitung
