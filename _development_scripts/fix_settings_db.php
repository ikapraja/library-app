<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Fix site_name
$conn->query("UPDATE settings SET setting_value = 'Perpustakaan' WHERE setting_key = 'site_name'");

// Fix jam_layanan
$jam_layanan_html = "<ul>
    <li><span class=\"day\">Senin - Kamis</span> <span class=\"time\">: 08.00 - 16.00 WIB</span></li>
    <li><span class=\"day\">Jumat</span> <span class=\"time\">: 08.30 - 16.30 WIB</span></li>
    <li><span class=\"day\">Sabtu - Minggu</span> <span class=\"time\">: Tutup</span></li>
    <li><span class=\"day\">Libur Nasional</span> <span class=\"time\">: Tutup</span></li>
</ul>";
$safe_jam = $conn->real_escape_string($jam_layanan_html);
$conn->query("UPDATE settings SET setting_value = '$safe_jam' WHERE setting_key = 'jam_layanan'");

// Fix social links
$conn->query("UPDATE settings SET setting_value = 'https://www.youtube.com/@perpustakaan_pktj' WHERE setting_key = 'social_yt'");
$conn->query("UPDATE settings SET setting_value = 'https://www.instagram.com/perpustakaan_pktj/' WHERE setting_key = 'social_ig'");
$conn->query("UPDATE settings SET setting_value = 'https://www.tiktok.com/@perpustakaan_pktj0' WHERE setting_key = 'social_tiktok'");

// Delete FB and X
$conn->query("DELETE FROM settings WHERE setting_key IN ('social_fb', 'social_x')");

// Add Threads
$conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('social_threads', 'https://www.threads.net/@perpustakaan_pktj')");

// Add Pusat Informasi
$pusat_informasi_html = "<ul>
    <li><a href=\"https://pktj.ac.id/\" target=\"_blank\"><i class=\"fa-solid fa-chevron-right\"></i> Website Utama PKTJ</a></li>
    <li><a href=\"http://eprints.pktj.ac.id/\" target=\"_blank\"><i class=\"fa-solid fa-chevron-right\"></i> Repositori Institusi</a></li>
    <li><a href=\"bebas-pustaka.php\"><i class=\"fa-solid fa-chevron-right\"></i> Bebas Pustaka</a></li>
    <li><a href=\"https://opac.pktj.ac.id/\" target=\"_blank\"><i class=\"fa-solid fa-chevron-right\"></i> Katalog Buku (OPAC)</a></li>
    <li><a href=\"tata-tertib.php\"><i class=\"fa-solid fa-chevron-right\"></i> Tata Tertib Perpustakaan</a></li>
</ul>";
$safe_pusat = $conn->real_escape_string($pusat_informasi_html);
$conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('pusat_informasi', '$safe_pusat')");

echo "Settings fixed.";
$conn->close();
?>
