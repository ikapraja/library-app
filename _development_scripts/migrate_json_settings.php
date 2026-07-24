<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 1. Pusat Informasi JSON
$pusat_informasi = [
    ['title' => 'Website Utama PKTJ', 'url' => 'https://pktj.ac.id/'],
    ['title' => 'Repositori Institusi', 'url' => 'http://eprints.pktj.ac.id/'],
    ['title' => 'Bebas Pustaka', 'url' => 'bebas-pustaka.php'],
    ['title' => 'Katalog Buku (OPAC)', 'url' => 'https://opac.pktj.ac.id/'],
    ['title' => 'Tata Tertib Perpustakaan', 'url' => 'tata-tertib.php']
];
$json_pusat = $conn->real_escape_string(json_encode($pusat_informasi));

$check = $conn->query("SELECT * FROM settings WHERE setting_key = 'pusat_informasi_json'");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('pusat_informasi_json', '$json_pusat')");
} else {
    $conn->query("UPDATE settings SET setting_value = '$json_pusat' WHERE setting_key = 'pusat_informasi_json'");
}

// 2. Social Media JSON
// Since we don't have uploaded icons yet, we'll store a 'type' to know if it's fa-icon or uploaded image
$social_media = [
    ['icon_type' => 'fa', 'icon_val' => 'fa-youtube', 'url' => 'https://www.youtube.com/@perpustakaan_pktj'],
    ['icon_type' => 'fa', 'icon_val' => 'fa-instagram', 'url' => 'https://www.instagram.com/perpustakaan_pktj/'],
    ['icon_type' => 'fa', 'icon_val' => 'fa-tiktok', 'url' => 'https://www.tiktok.com/@perpustakaan_pktj0'],
    ['icon_type' => 'fa', 'icon_val' => 'fa-threads', 'url' => 'https://www.threads.net/@perpustakaan_pktj']
];
$json_social = $conn->real_escape_string(json_encode($social_media));

$check2 = $conn->query("SELECT * FROM settings WHERE setting_key = 'social_media_json'");
if ($check2->num_rows == 0) {
    $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('social_media_json', '$json_social')");
} else {
    $conn->query("UPDATE settings SET setting_value = '$json_social' WHERE setting_key = 'social_media_json'");
}

echo "DB Migration complete.";
$conn->close();
?>
