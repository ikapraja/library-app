<?php
$files = [
    'index.php', 'visi-misi.php', 'struktur-organisasi.php', 'fasilitas.php',
    'tata-tertib.php', 'layanan-sirkulasi.php', 'layanan-referensi.php',
    'bebas-pustaka.php', 'berita.php', 'berita_detail.php', 'galeri.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    // Find the end of the header (</header>)
    $header_pos = strpos($content, '</header>');
    if ($header_pos !== false) {
        // Find the start of the file or after the DB include
        // Actually, we can just replace everything from '<!DOCTYPE html>' up to '</header>'
        $start_pos = strpos($content, '<!DOCTYPE html>');
        if ($start_pos !== false) {
            $header_str = substr($content, $start_pos, ($header_pos + 9) - $start_pos);
            $content = str_replace($header_str, "<?php include 'includes/frontend_header.php'; ?>\n", $content);
        }
    }
    
    // Find the start of the footer (<!-- Footer -->)
    $footer_start = strpos($content, '<!-- Footer -->');
    if ($footer_start !== false) {
        $footer_str = substr($content, $footer_start); // Everything to the end of the file
        $content = str_replace($footer_str, "<?php include 'includes/frontend_footer.php'; ?>\n", $content);
    }
    
    file_put_contents($file, $content);
    echo "Refactored $file\n";
}
?>
