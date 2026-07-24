<?php
$files = [
    'bebas-pustaka.php',
    'berita.php',
    'detail-berita.php',
    'fasilitas.php',
    'layanan-referensi.php',
    'layanan-sirkulasi.php',
    'struktur-organisasi.php',
    'tata-tertib.php',
    'visi-misi.php'
];

$index_content = file_get_contents('index.php');

// Extract Header block
preg_match('/<header id="header" class="header">.*?<\/header>/s', $index_content, $header_matches);
$header_html = $header_matches[0];

// Extract Footer block
preg_match('/<!-- Footer -->.*?<\/footer>/s', $index_content, $footer_matches);
$footer_html = $footer_matches[0];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // Add db.php if missing
    if (strpos($content, 'db.php') === false) {
        $content = "<?php require_once 'db.php'; ?>\n" . $content;
    }

    // Replace header
    $content = preg_replace('/<header id="header" class="header">.*?<\/header>/s', $header_html, $content);
    
    // Replace old footer with new footer
    // Look for <footer... to </footer>
    if (preg_match('/<!-- Footer -->/s', $content)) {
         $content = preg_replace('/<!-- Footer -->.*?<\/footer>/s', $footer_html, $content);
    } else {
         $content = preg_replace('/<footer class="footer">.*?<\/footer>/s', $footer_html, $content);
    }
    
    // Also replace any lingering .php links with .php
    $content = str_replace('.php', '.php', $content);

    file_put_contents($file, $content);
    echo "Updated $file\n";
}
echo "Done.\n";
?>
