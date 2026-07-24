<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$initial_jam = [
    ['day' => 'Senin - Kamis', 'time' => '08.00 - 16.00 WIB'],
    ['day' => 'Jumat', 'time' => '08.30 - 16.30 WIB'],
    ['day' => 'Sabtu - Minggu', 'time' => 'Tutup'],
    ['day' => 'Libur Nasional', 'time' => 'Tutup']
];
$json_jam = $conn->real_escape_string(json_encode($initial_jam));

$check = $conn->query("SELECT * FROM settings WHERE setting_key = 'jam_layanan_json'");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('jam_layanan_json', '$json_jam')");
}

echo "DB updated for JSON jam layanan.";
$conn->close();
?>
