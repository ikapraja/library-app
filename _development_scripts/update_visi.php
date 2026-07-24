<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_content = '<i class="fa-solid fa-bullseye" style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 20px;"></i>

<div class="content-card">
    <h3><i class="fa-solid fa-eye" style="color: var(--accent-gold);"></i> Visi</h3>
    <p style="font-weight: 500; font-size: 1.2rem;">Menjadi pusat literasi keselamatan transportasi jalan yang terkemuka.</p>
</div>

<div class="content-card">
    <h3><i class="fa-solid fa-rocket" style="color: var(--accent-gold);"></i> Misi</h3>
    <ol>
        <li>Memberikan rujukan informasi ilmiah bagi segenap sivitas akademika PKTJ maupun masyarakat luas.</li>
        <li>Menyelenggarakan pengelolaan perpustakaan yang terakreditasi.</li>
        <li>Menunjang pelaksanaan Tri Dharma Perguruan Tinggi.</li>
    </ol>
</div>';

$stmt = $conn->prepare("UPDATE pages SET content = ? WHERE slug = 'visi-misi'");
$stmt->bind_param("s", $new_content);
if ($stmt->execute()) {
    echo "Content updated successfully!";
} else {
    echo "Error updating content: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
