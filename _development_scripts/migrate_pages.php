<?php
$pages = [
    'visi-misi',
    'struktur-organisasi',
    'fasilitas',
    'tata-tertib',
    'layanan-sirkulasi',
    'layanan-referensi',
    'bebas-pustaka'
];

foreach ($pages as $slug) {
    $file = $slug . '.php';
    if (!file_exists($file)) continue;
    
    $html = file_get_contents($file);
    
    // Replace top PHP block
    $new_php = "<?php\nrequire_once 'db.php';\n\$slug = '$slug';\n\$sql = \"SELECT * FROM pages WHERE slug = '\$slug'\";\n\$result = \$conn->query(\$sql);\n\$page_data = \$result->fetch_assoc();\n\$page_title = \$page_data ? htmlspecialchars(\$page_data['title']) : 'Halaman Tidak Ditemukan';\n\$page_content = \$page_data ? \$page_data['content'] : 'Konten belum tersedia.';\n?>";
    $html = preg_replace('/<\?php require_once \'db\.php\'; \?>/', $new_php, $html, 1);
    
    // Replace Title Tag
    $html = preg_replace('/<title>.*?<\/title>/', '<title><?php echo $page_title; ?> - Perpustakaan PKTJ</title>', $html, 1);
    
    // Replace Page Header H1
    $html = preg_replace('/<div class="page-header">\s*<div class="container">\s*<h1 class="animate-up">.*?<\/h1>\s*<\/div>\s*<\/div>/s', '<div class="page-header">
        <div class="container">
            <h1 class="animate-up"><?php echo $page_title; ?></h1>
        </div>
    </div>', $html, 1);
    
    // Replace Content Section
    $html = preg_replace('/<section class="content-section">.*?<div class="container[^"]*">(.*?)<\/div>\s*<\/section>/s', '<section class="content-section">
        <div class="container animate-up delay-1">
            <?php echo $page_content; ?>
        </div>
    </section>', $html, 1);
    
    file_put_contents($file, $html);
}
echo "All frontend files migrated successfully.";
?>
