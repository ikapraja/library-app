<?php
$files = ['bebas-pustaka.php', 'berita.php', 'detail-berita.php', 'fasilitas.php', 'layanan-referensi.php', 'layanan-sirkulasi.php', 'struktur-organisasi.php', 'tata-tertib.php', 'visi-misi.php'];
foreach($files as $file) {
    if (file_exists($file)) {
        echo $file . ': ' . (strpos(file_get_contents($file), '<footer') !== false ? "Yes\n" : "No\n");
    }
}
?>
