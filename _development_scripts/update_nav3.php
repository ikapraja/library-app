<?php
$files = glob("*.php");
foreach ($files as $file) {
    if ($file == 'update_nav3.php' || $file == 'index.php') continue;
    $content = file_get_contents($file);
    
    // Non-active
    $search1 = '<li class="dropdown">
                        <a href="#" class="publikasi-btn">PUBLIKASI <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="berita">Berita & Sorotan</a></li>
                            <li><a href="galeri">Galeri Foto</a></li>
                        </ul>
                    </li>';
    $replace1 = '<li class="dropdown">
                        <a href="#" class="publikasi-btn">INFORMASI <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="berita">Berita</a></li>
                            <li><a href="galeri">Galeri</a></li>
                        </ul>
                    </li>';
                    
    // Active
    $search2 = '<li class="dropdown">
                        <a href="#" class="publikasi-btn active">PUBLIKASI <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="berita">Berita & Sorotan</a></li>
                            <li><a href="galeri">Galeri Foto</a></li>
                        </ul>
                    </li>';
    $replace2 = '<li class="dropdown">
                        <a href="#" class="publikasi-btn active">INFORMASI <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="berita">Berita</a></li>
                            <li><a href="galeri">Galeri</a></li>
                        </ul>
                    </li>';
    
    $new_content = str_replace($search1, $replace1, $content);
    $new_content = str_replace($search2, $replace2, $new_content);
    
    if ($new_content !== $content) {
        file_put_contents($file, $new_content);
        echo "Updated $file\n";
    }
}
?>
