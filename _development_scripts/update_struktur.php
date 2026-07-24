<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_content = '<i class="fa-solid fa-sitemap" style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 20px;"></i>
<div class="content-card" style="margin-bottom: 40px;">
    <h3><i class="fa-solid fa-users-gear" style="color: var(--accent-gold);"></i> Tata Kelola & Organisasi</h3>
    <p>Struktur Organisasi Unit Perpustakaan PKTJ dirancang secara strategis untuk memastikan tata kelola yang profesional, responsif, dan berorientasi pada pelayanan prima kepada seluruh pemustaka. Di bawah naungan institusi Politeknik Keselamatan Transportasi Jalan, perpustakaan kami dikelola oleh tenaga ahli perpustakaan (pustakawan) yang berdedikasi tinggi.</p>
</div>';

$stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'struktur-organisasi'");
$stmt->bind_param("s", $new_content);
if ($stmt->execute()) {
    echo "Content updated successfully!";
} else {
    echo "Error updating content: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
