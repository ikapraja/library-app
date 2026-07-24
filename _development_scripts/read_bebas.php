<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";
$conn = new mysqli($servername, $username, $password, $dbname);
$res = $conn->query("SELECT content FROM pages WHERE slug = 'struktur-organisasi'");
$row = $res->fetch_assoc();
file_put_contents("struktur_clean.txt", $row['content']);
?>
