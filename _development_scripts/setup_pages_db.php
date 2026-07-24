<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$sql = "CREATE TABLE IF NOT EXISTS pages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

$pages = [
    'visi-misi' => 'Visi dan Misi',
    'struktur-organisasi' => 'Struktur Organisasi',
    'fasilitas' => 'Fasilitas',
    'tata-tertib' => 'Tata Tertib',
    'layanan-sirkulasi' => 'Layanan Sirkulasi',
    'layanan-referensi' => 'Layanan Referensi & Serial',
    'bebas-pustaka' => 'Layanan Bebas Pustaka'
];

foreach ($pages as $slug => $title) {
    $file = $slug . '.php';
    if (file_exists($file)) {
        $html = file_get_contents($file);
        // Extract content between <div class="container animate-up delay-1"> and </div>\n    </section>
        if (preg_match('/<section class="content-section">.*?<div class="container[^"]*">(.*?)<\/div>\s*<\/section>/s', $html, $matches)) {
            $content = trim($matches[1]);
            // Some pages might not match exactly, so if empty fallback to something basic
            if (empty($content)) {
                $content = '<h2>' . $title . '</h2><p>Konten untuk ' . $title . '</p>';
            }
        } else {
            $content = '<h2>' . $title . '</h2><p>Konten untuk ' . $title . '</p>';
        }
    } else {
        $content = '<h2>' . $title . '</h2><p>Konten untuk ' . $title . '</p>';
    }
    
    $title_esc = $conn->real_escape_string($title);
    $content_esc = $conn->real_escape_string($content);
    $conn->query("INSERT IGNORE INTO pages (slug, title, content) VALUES ('$slug', '$title_esc', '$content_esc')");
}

echo "Pages table seeded successfully.";
$conn->close();
?>
