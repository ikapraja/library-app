<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Create staff_members table
$sql1 = "CREATE TABLE IF NOT EXISTS staff_members (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    order_num INT(6) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql1);

// Create facilities table
$sql2 = "CREATE TABLE IF NOT EXISTS facilities (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    order_num INT(6) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql2);

// Seed Staff Data
$staffs = [
    ['Supriatin, S.S.T.', 'Kepala Perpustakaan', 'supriatin@pktj.ac.id', 'Supriatin.png', 1],
    ['Nurul Fitriani, S.Pd., M.T.', 'Pustakawan', 'nurul.fitriani@pktj.ac.id', 'Nurul%20Fitriani.png', 2],
    ['Pipin Widyasari, A.Ma. Pust., S.S., M.M.', 'Pustakawan', 'pipin.widyasari@pktj.ac.id', 'Pipin%20Widya%20S.png', 3],
    ['Bagus Ikapraja, S.I.Pust.', 'Pustakawan', 'bagusikapraja@pktj.ac.id', 'Bagus%20Ikapraja.png', 4]
];

$conn->query("TRUNCATE TABLE staff_members");
foreach ($staffs as $s) {
    $conn->query("INSERT INTO staff_members (name, role, email, photo_url, order_num) VALUES ('{$s[0]}', '{$s[1]}', '{$s[2]}', '{$s[3]}', {$s[4]})");
}

// Seed Facilities Data
$facs = [
    ['Ruang Komputer', 'Kampus Perintis', 'Fasilitas perangkat komputasi terpadu untuk memfasilitasi taruna dalam penelusuran katalog digital (OPAC), e-book, dan akses pangkalan data jurnal ilmiah.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 1],
    ['Ruang Kreasi & Kafetaria', 'Kampus Margadana - Lantai 1', 'Zona interaksi sosial yang memadukan ruang berkreasi dan fasilitas refreshment untuk mendukung dinamisasi kegiatan mahasiswa di luar jam studi.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 2],
    ['Ruang Diskusi', 'Kampus Margadana - Lantai 2 & 3', 'Area komunal kolaboratif yang dirancang khusus untuk mengakomodasi kerja kelompok, bertukar gagasan, dan menyelesaikan tugas akademik secara bersama.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 3],
    ['Musala', 'Kampus Margadana - Lantai 2', 'Fasilitas ibadah yang terintegrasi dengan area komparatif terbuka, memberikan suasana yang tenang bagi pemustaka untuk bersosialisasi maupun membaca.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 4],
    ['Ruang Koleksi Karya Cetak', 'Kampus Margadana - Lantai 3', 'Pusat penyimpanan utama koleksi literatur tercetak, mencakup buku teks, referensi, prosiding, hingga koleksi terbitan berkala lainnya.', 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul', 5]
];

$conn->query("TRUNCATE TABLE facilities");
foreach ($facs as $f) {
    $conn->query("INSERT INTO facilities (name, location, description, image_url, order_num) VALUES ('{$f[0]}', '{$f[1]}', '{$f[2]}', '{$f[3]}', {$f[4]})");
}

// Ensure settings record exists for org chart image
$conn->query("INSERT IGNORE INTO pages (slug, title, content) VALUES ('org-chart-img', 'Org Chart Image', 'Struktur Organisasi.png')");

echo "Special modules tables created and seeded.";
$conn->close();
?>
