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
// Visitor Tracking Logic (Page Views)
$today = date('Y-m-d');
$track_sql = "INSERT INTO visitor_stats (visit_date, hits) VALUES ('$today', 1) ON DUPLICATE KEY UPDATE hits = hits + 1";
$conn->query($track_sql);
