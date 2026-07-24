<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_content = '<i class="fa-solid fa-building" style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 20px;"></i>
<div class="content-card" style="margin-bottom: 40px;">
    <h3><i class="fa-solid fa-map-location-dot" style="color: var(--accent-gold);"></i> Peta Fasilitas Terpadu</h3>
    <p>Infrastruktur spasial dan sarana prasarana modern yang didistribusikan secara strategis di Kampus Margadana dan Kampus Perintis untuk mendukung ekosistem akademik literasi informasi.</p>
</div>';

$stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'fasilitas'");
$stmt->bind_param("s", $new_content);
if ($stmt->execute()) {
    echo "Content updated successfully!";
} else {
    echo "Error updating content: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
