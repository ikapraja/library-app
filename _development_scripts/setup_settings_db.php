<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Create settings table
$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Seed Data
$settings = [
    'site_name' => 'Perpustakaan PKTJ',
    'site_name_long' => 'POLITEKNIK KESELAMATAN TRANSPORTASI JALAN',
    'site_logo' => 'logopktjdanperpus.png',
    'npp' => '3376022C0000001',
    'contact_email' => 'library@pktj.ac.id',
    'contact_wa' => '6281292760076',
    'address_margadana' => 'Jl. Abdul Syukur No. 17, Kota Tegal',
    'map_margadana' => 'https://maps.app.goo.gl/jPTBNUqwcUVqaw13A',
    'address_perintis' => 'Jl. Perintis Kemerdekaan No. 17, Kota Tegal',
    'map_perintis' => 'https://maps.app.goo.gl/3bCreD37GcxzR65NA',
    'social_ig' => 'https://www.instagram.com/perpustakaan_pktj',
    'social_yt' => 'https://www.youtube.com/@PoliteknikKeselamatanTransport',
    'social_fb' => 'https://www.facebook.com/pktj.tegal',
    'social_tiktok' => 'https://www.tiktok.com/@pktjtegal',
    'social_x' => 'https://x.com/pktjtegal',
    'jam_layanan' => 'Senin - Jumat: 08:00 - 16:00 WIB<br>Sabtu - Minggu: Libur'
];

foreach ($settings as $key => $value) {
    $safe_key = $conn->real_escape_string($key);
    $safe_value = $conn->real_escape_string($value);
    
    // Check if exists
    $check = $conn->query("SELECT * FROM settings WHERE setting_key = '$safe_key'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('$safe_key', '$safe_value')");
    }
}

echo "Settings table created and seeded.";
$conn->close();
?>
