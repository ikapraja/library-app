<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$conn->query("DELETE FROM facilities WHERE name = 'Ruang Koleksi Karya Cetak'");

echo "Extra facility removed.";
$conn->close();
?>
