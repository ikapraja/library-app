<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$facs = [
    ['Ruang Mini Theatre', 'Kampus Margadana - Lantai 3', 'Fasilitas audiovisual interaktif modern untuk pemutaran media edukasi pembelajaran, bedah film dokumenter, dan kegiatan literasi digital.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 6],
    ['Ruang Rapat', 'Kampus Margadana - Lantai 3', 'Ruang pertemuan formal dengan representasi profesional yang digunakan untuk mendukung koordinasi internal, seminar kecil, dan presentasi akademik.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 7],
    ['PKTJ Corner', 'Kampus Margadana - Lantai 3', 'Pusat koleksi dan rujukan khusus yang menyajikan literatur esensial, regulasi, serta perkembangan mutakhir di bidang keselamatan transportasi jalan hasil karya civitas akademik PKTJ.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 8],
    ['Pojok Sejarah', 'Kampus Margadana - Lantai 3', 'Area memorabilia visual yang mendokumentasikan rekam jejak garis waktu perkembangan dan nilai-nilai historis institusi PKTJ dari masa ke masa.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 9],
    ['Ruang Baca & Koleksi', 'Kampus Margadana - Lt. 3 & Kampus Perintis', 'Area sentral perpustakaan yang mengintegrasikan rak koleksi buku fisik dengan ruang baca yang tenang guna mendukung retensi fokus pemustaka.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 10]
];

foreach ($facs as $f) {
    // Check if already exists to prevent duplicate insertion
    $check = $conn->query("SELECT * FROM facilities WHERE name = '{$f[0]}'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO facilities (name, location, description, image_url, order_num) VALUES ('{$f[0]}', '{$f[1]}', '{$f[2]}', '{$f[3]}', {$f[4]})");
    }
}

echo "Missing facilities added.";
$conn->close();
?>
