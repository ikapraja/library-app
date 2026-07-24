<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$new_settings = [
    'contact_phone' => '(0283) 351061',
    'jam_1_day' => 'Senin - Kamis',
    'jam_1_time' => '08.00 - 16.00 WIB',
    'jam_2_day' => 'Jumat',
    'jam_2_time' => '08.30 - 16.30 WIB',
    'jam_3_day' => 'Sabtu - Minggu',
    'jam_3_time' => 'Tutup',
    'jam_4_day' => 'Libur Nasional',
    'jam_4_time' => 'Tutup'
];

foreach ($new_settings as $key => $value) {
    $safe_key = $conn->real_escape_string($key);
    $safe_value = $conn->real_escape_string($value);
    
    $check = $conn->query("SELECT * FROM settings WHERE setting_key = '$safe_key'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('$safe_key', '$safe_value')");
    } else {
        $conn->query("UPDATE settings SET setting_value = '$safe_value' WHERE setting_key = '$safe_key'");
    }
}

echo "DB updated for structured hours and phone.";
$conn->close();
?>
