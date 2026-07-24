<?php 
require_once 'db.php'; 

if (!isset($_GET['id']) && !isset($_GET['slug'])) {
    header("Location: berita");
    exit;
}

if (isset($_GET['slug'])) {
    $slug = $conn->real_escape_string($_GET['slug']);
    $sql = "SELECT * FROM posts WHERE slug = '$slug'";
} else {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM posts WHERE id = $id";
}

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $post_data = $result->fetch_assoc();
    $post_id = $post_data['id'];
    $conn->query("UPDATE posts SET views = views + 1 WHERE id = $post_id");
}

$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    header("Location: berita");
    exit;
}

$post = $result->fetch_assoc();
$image = !empty($post['image_url']) ? $post['image_url'] : 'https://images.unsplash.com/photo-1541963463532-d68292c34b19?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
$date = date('d F Y', strtotime($post['updated_at'] ?? $post['publish_date'] ?? $post['created_at']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Perpustakaan PKTJ</title>
    <link rel="icon" href="/library-app/LOGO PERPUS PKTJ.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <base href="/library-app/">
    <link rel="stylesheet" href="style.css">
    <style>
        .post-header { margin-bottom: 30px; }
        .post-title { font-size: 2.5rem; color: var(--primary-blue); margin-bottom: 10px; }
        .post-meta { color: #777; margin-bottom: 20px; font-size: 0.9rem; }
        .post-image { width: 100%; height: auto; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .post-content { font-size: 1.1rem; line-height: 1.8; color: #444; }
        .post-content p { margin-bottom: 20px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--accent-gold); text-decoration: none; font-weight: 600; }
        .back-link:hover { color: var(--primary-blue); }
        .share-buttons { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; align-items: center; }
        .share-btn { width: 40px; height: 40px; display: flex; justify-content: center; align-items: center; border-radius: 50%; color: white; text-decoration: none; transition: 0.3s; }
        .share-wa { background: #25D366; } .share-wa:hover { background: #128C7E; }
        .share-fb { background: #1877F2; } .share-fb:hover { background: #165eab; }
        .share-copy { background: #666; cursor: pointer; } .share-copy:hover { background: #444; }
        .related-news { margin-top: 60px; }
        .related-news h3 { margin-bottom: 20px; color: var(--primary-blue); border-left: 4px solid var(--accent-gold); padding-left: 10px; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <span class="npp-info"><i class="fa-solid fa-id-card"></i> NPP 3376022C0000001</span>
            <span class="akreditasi"><i class="fa-solid fa-award"></i> Akreditasi A</span>
        </div>
    </div>

    <!-- Header & Navigation -->
    <header id="header" class="header">
        <div class="container header-container">
            <a href="index" class="logo-container" style="text-decoration: none; cursor: pointer;">
                <div class="logo-icon">
                    <img src="logopktjdanperpus.png" alt="Logo PKTJ" style="height: 45px; width: auto; display: block;">
                </div>
                <div class="logo-text">
                    <h1>PERPUSTAKAAN</h1>
                    <p>POLITEKNIK KESELAMATAN TRANSPORTASI JALAN</p>
                </div>
            </a>
            
                                    <nav class="main-nav">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <ul class="nav-links" id="navLinks">
                    <li><a href="index">BERANDA</a></li>
                    
                    <li class="dropdown">
                        <a href="#">PROFIL <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="visi-misi">Visi dan Misi</a></li>
                            <li><a href="struktur-organisasi">Struktur Organisasi</a></li>
                            <li><a href="fasilitas">Fasilitas</a></li>
                            <li><a href="tata-tertib">Tata Tertib</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#">LAYANAN <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="layanan-sirkulasi">Layanan Sirkulasi</a></li>
                            <li><a href="layanan-referensi">Layanan Referensi & Serial</a></li>
                            <li><a href="bebas-pustaka">Layanan Bebas Pustaka</a></li>
                            <li><a href="https://forms.gle/Hzaqgq56KghfYmMZ9" target="_blank">Layanan Penyediaan Repository</a></li>
                            <li><a href="https://forms.gle/i1RCTshjuUq1tfEn8" target="_blank">Layanan Delivery Book</a></li>
                            <li><a href="https://forms.gle/C9wEbt492ppe2Fkj7" target="_blank">Layanan Library Class</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#">RESOURCES <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu">
                                <a href="#">E-Journal & Database <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a href="#">Jurnal PKTJ <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="https://ktj.pktj.ac.id/index.php/ktj" target="_blank">Jurnal Keselamatan Transportasi Jalan</a></li>
                                            <li><a href="https://ktj.pktj.ac.id/index.php/jat" target="_blank">Jurnal Abdimas Transjaya</a></li>
                                            <li><a href="https://ejournal.pktj.ac.id/index.php/perintis/beranda" target="_blank">Jurnal Perintis</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a href="#">Jurnal Nasional & Internasional <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-submenu">
                                                <a href="#">Jurnal Internasional (OA) <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="https://www.emerald.com/insight/" target="_blank">Emerald</a></li>
                                                    <li><a href="https://ieeexplore.ieee.org/Xplore/home.jsp" target="_blank">IEEE</a></li>
                                                    <li><a href="https://academic.oup.com/" target="_blank">Oxford Academic</a></li>
                                                    <li><a href="https://www.scirp.org/index" target="_blank">Scientific Research</a></li>
                                                    <li><a href="https://www.springernature.com/gp" target="_blank">Springer Nature</a></li>
                                                    <li><a href="https://onlinelibrary.wiley.com/" target="_blank">Wiley</a></li>
                                                    <li><a href="https://authorservices.taylorandfrancis.com/choose-open/publishing-open-access/open-access-cost-finder/?category=all&journal=all&fulloa=1&openselect=0&notavailable=0&dove=1&routledge=1&tandf=1&numberofresultsperpage=5&pagenumber=1" target="_blank">Taylor & Francis</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a href="#">Jurnal Nasional (OA) <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="https://garuda.kemdiktisaintek.go.id/" target="_blank">Garuda</a></li>
                                                    <li><a href="https://sinta.kemdiktisaintek.go.id/journals" target="_blank">Sinta</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a href="#">Database <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="https://link.gale.com/apps/PPGS?u=fjatsci" target="_blank">Gale: Science</a></li>
                                            <li><a href="https://link.gale.com/apps/SPJ.SP00?u=fjathum" target="_blank">Gale: Humanities</a></li>
                                            <li><a href="https://link.gale.com/apps/ITBC?u=fjatbus" target="_blank">Gale: Business</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#">E-Book <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="https://opac.pktj.ac.id/index.php?title=&author=&subject=&isbn=&colltype=E-book&location=0&gmd=0&search=search" target="_blank">E-Book OPAC PKTJ</a></li>
                                    <li><a href="https://pktj.perpustakaan.co.id/home.ks" target="_blank">E-Book Digido</a></li>
                                    <li><a href="https://www.doabooks.org/en" target="_blank">DOAB</a></li>
                                    <li><a href="https://www.gutenberg.org/" target="_blank">Project Gutenberg</a></li>
                                    <li><a href="https://openlibrary.org/" target="_blank">Open Library</a></li>
                                    <li><a href="https://oapen.org/" target="_blank">OAPEN</a></li>
                                    <li><a href="https://www.intechopen.com/" target="_blank">Intechopen</a></li>
                                    <li><a href="https://www.taylorfrancis.com/search?sortBy=relevance&key=&openAccess=true&fullyOABook=true&OAChapter=true" target="_blank">Taylor & Francis</a></li>
                                </ul>
                            </li>
                            <li><a href="http://eprints.pktj.ac.id/" target="_blank">Repositori Institusi</a></li>
                            <li class="dropdown-submenu">
                                <a href="#">Jejaring & Mitra <i class="fa-solid fa-chevron-right" style="float: right; margin-top: 5px; font-size: 0.8rem;"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="https://www.perpusnas.go.id/layanan" target="_blank">Perpusnas</a></li>
                                    <li><a href="https://fppti-jateng.or.id/" target="_blank">FPPTI Jawa Tengah</a></li>
                                    <li><a href="https://elibrary.dephub.go.id/" target="_blank">Integrated Transport Resource Catalog (Perpustakaan Kemenhub)</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#">RUANG PEMUSTAKA <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="https://forms.gle/UGY3949cTwuh4Egz8" target="_blank" rel="noopener noreferrer">Survei Kebutuhan Pemustaka</a></li>
                            <li><a href="https://forms.gle/H5XaT3Hr3GFV4Vjb9" target="_blank" rel="noopener noreferrer">Survei Kepuasan Pemustaka</a></li>
                            <li><a href="https://forms.gle/NRymXWjqsyBeYa469" target="_blank" rel="noopener noreferrer">Peminjaman Ruang</a></li>
                            <li><a href="https://api.whatsapp.com/send/?phone=6281292760076&text=Halo+Admin+Perpustakaan+PKTJ%2C+saya+ingin+bertanya+seputar+layanan+perpustakaan&type=phone_number&app_absent=0" target="_blank" rel="noopener noreferrer">Tanya Pustakawan</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#" class="publikasi-btn active">INFORMASI <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="berita">Berita</a></li>
                            <li><a href="galeri">Galeri</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container" style="padding: 60px 0; max-width: 900px;">
        <a href="berita" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Berita</a>
        
        <div class="post-header">
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-meta">
                <i class="fa-regular fa-calendar"></i> <?php echo $date; ?> | 
                <i class="fa-regular fa-user"></i> Admin | 
                <i class="fa-solid fa-eye"></i> <?php echo number_format($post['views'] ?? 0); ?> Kali Dilihat
            </div>
        </div>
        
        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image">
        
        <div class="post-content">
            <?php echo $post['content']; ?>
        </div>
        
        <div class="share-buttons">
            <strong style="color: #555;">Bagikan:</strong>
            <?php $current_url = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>
            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($post['title'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn share-wa" title="Bagikan ke WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" target="_blank" class="share-btn share-fb" title="Bagikan ke Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a onclick="navigator.clipboard.writeText('<?php echo urldecode($current_url); ?>'); alert('Link berhasil disalin!');" class="share-btn share-copy" title="Salin Link"><i class="fa-solid fa-link"></i></a>
        </div>
        
        <div class="related-news">
            <h3>Berita Terkait</h3>
            <div class="related-grid">
                <?php
                $category = $conn->real_escape_string($post['category']);
                $related_sql = "SELECT * FROM posts WHERE category = '$category' AND id != $id AND is_published = 1 ORDER BY COALESCE(updated_at, publish_date, created_at) DESC LIMIT 3";
                $related_result = $conn->query($related_sql);
                
                if ($related_result && $related_result->num_rows > 0) {
                    while($r = $related_result->fetch_assoc()) {
                        $r_img = !empty($r['image_url']) ? $r['image_url'] : 'https://images.unsplash.com/photo-1541963463532-d68292c34b19?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                        $r_date = date('d M Y', strtotime($r['updated_at'] ?? $r['publish_date'] ?? $r['created_at']));
                        echo '<article class="news-card" style="box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: 0.3s;" onmouseover="this.style.transform=\'translateY(-5px)\'" onmouseout="this.style.transform=\'translateY(0)\'">';
                        echo '<a href="/library-app/berita/' . htmlspecialchars($r['slug']) . '" style="text-decoration: none; color: inherit; display: block;">';
                        echo '<div class="news-img" style="background-image: url(\'' . htmlspecialchars($r_img) . '\'); height: 150px;"></div>';
                        echo '<div class="news-content" style="padding: 15px;">';
                        echo '<h4 style="font-size: 1.1rem; margin-bottom: 5px;">' . htmlspecialchars($r['title']) . '</h4>';
                        echo '<div class="news-meta" style="font-size: 0.8rem;"><i class="fa-regular fa-calendar"></i> ' . $r_date . '</div>';
                        echo '</div></a></article>';
                    }
                } else {
                    echo '<p style="color: #777; grid-column: 1 / -1;">Belum ada berita terkait.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
    // Calculate Visitor Stats
    $today = date('Y-m-d');
    $this_week_start = date('Y-m-d', strtotime('monday this week'));
    $this_month_start = date('Y-m-01');
    $this_year_start = date('Y-01-01');

    $stat_today = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date = '$today'")->fetch_assoc()['total'] ?? 0;
    $stat_week = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$this_week_start'")->fetch_assoc()['total'] ?? 0;
    $stat_month = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$this_month_start'")->fetch_assoc()['total'] ?? 0;
    $stat_year = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$this_year_start'")->fetch_assoc()['total'] ?? 0;
    ?>
    <footer class="footer">
        <div class="footer-top">
            <div class="container footer-grid">
                
                <div class="footer-info">
                    <div class="footer-logo">
                        <div class="logo-icon">
                            <img src="logopktjdanperpus.png" alt="Logo PKTJ" style="height: 60px; width: auto; display: block;">
                        </div>
                    </div>
                    <p class="npp-footer">NPP 3376022C0000001</p>
                    <ul class="contact-list">
                        <li style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fa-solid fa-location-dot fa-fw" style="margin-top: 5px;"></i>
                            <a href="https://maps.app.goo.gl/jPTBNUqwcUVqaw13A" target="_blank" style="color: inherit; text-decoration: none; border-bottom: 1px dashed rgba(255,255,255,0.3); transition: all 0.3s; line-height: 1.5;" onmouseover="this.style.color='#f6a821'; this.style.borderColor='#f6a821';" onmouseout="this.style.color='inherit'; this.style.borderColor='rgba(255,255,255,0.3)';">
                                <strong>Perpustakaan Margadana:</strong><br>Jl. Abdul Syukur No. 17, Kota Tegal
                            </a>
                        </li>
                        <li style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fa-solid fa-location-dot fa-fw" style="margin-top: 5px;"></i>
                            <a href="https://maps.app.goo.gl/3bCreD37GcxzR65NA" target="_blank" style="color: inherit; text-decoration: none; border-bottom: 1px dashed rgba(255,255,255,0.3); transition: all 0.3s; line-height: 1.5;" onmouseover="this.style.color='#f6a821'; this.style.borderColor='#f6a821';" onmouseout="this.style.color='inherit'; this.style.borderColor='rgba(255,255,255,0.3)';">
                                <strong>Perpustakaan Perintis:</strong><br>Jl. Perintis Kemerdekaan No. 17, Kota Tegal
                            </a>
                        </li>
                        <li><i class="fa-solid fa-envelope fa-fw"></i> library@pktj.ac.id</li>
                        <li><i class="fa-solid fa-phone fa-fw"></i> (0283) 351061</li>
                    </ul>
                </div>

                <div class="footer-hours">
                    <h4>Jam Layanan</h4>
                    <ul>
                        <li><span class="day">Senin - Kamis</span> <span class="time">: 08.00 - 16.00 WIB</span></li>
                        <li><span class="day">Jumat</span> <span class="time">: 08.30 - 16.30 WIB</span></li>
                        <li><span class="day">Sabtu - Minggu</span> <span class="time">: Tutup</span></li>
                        <li><span class="day">Libur Nasional</span> <span class="time">: Tutup</span></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Pusat Informasi</h4>
                    <ul>
                        <li><a href="https://pktj.ac.id/" target="_blank"><i class="fa-solid fa-chevron-right"></i> Website Utama PKTJ</a></li>
                        <li><a href="http://eprints.pktj.ac.id/" target="_blank"><i class="fa-solid fa-chevron-right"></i> Repositori Institusi</a></li>
                        <li><a href="bebas-pustaka"><i class="fa-solid fa-chevron-right"></i> Bebas Pustaka</a></li>
                        <li><a href="https://opac.pktj.ac.id/" target="_blank"><i class="fa-solid fa-chevron-right"></i> Katalog Buku (OPAC)</a></li>
                        <li><a href="tata-tertib"><i class="fa-solid fa-chevron-right"></i> Tata Tertib Perpustakaan</a></li>
                    </ul>
                </div>

                <div class="footer-stats">
                    <h4>Statistik Pengunjung</h4>
                    <table style="width: 100%; color: #ccc; font-size: 0.95rem; margin-top: 15px;">
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 10px 0;"><i class="fa-solid fa-user-clock" style="color: var(--accent-gold); margin-right: 8px; width: 15px;"></i> Hari Ini</td>
                            <td style="text-align: right; font-weight: bold; color: var(--accent-gold);"><?php echo number_format($stat_today, 0, ',', '.'); ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 10px 0;"><i class="fa-solid fa-calendar-week" style="color: var(--accent-gold); margin-right: 8px; width: 15px;"></i> Minggu Ini</td>
                            <td style="text-align: right; font-weight: bold; color: var(--accent-gold);"><?php echo number_format($stat_week, 0, ',', '.'); ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <td style="padding: 10px 0;"><i class="fa-solid fa-calendar-days" style="color: var(--accent-gold); margin-right: 8px; width: 15px;"></i> Bulan Ini</td>
                            <td style="text-align: right; font-weight: bold; color: var(--accent-gold);"><?php echo number_format($stat_month, 0, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0;"><i class="fa-solid fa-calendar-check" style="color: var(--accent-gold); margin-right: 8px; width: 15px;"></i> Tahun Ini</td>
                            <td style="text-align: right; font-weight: bold; color: var(--accent-gold);"><?php echo number_format($stat_year, 0, ',', '.'); ?></td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
        <div class="footer-bottom">
            <div class="container bottom-container">
                <p>&copy; 2026 Perpustakaan Politeknik Keselamatan Transportasi Jalan.</p>
                <div class="social-links">
                    <span>Temukan Kami:</span>
                    <a href="https://www.youtube.com/@perpustakaan_pktj" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                    <a href="https://www.instagram.com/perpustakaan_pktj/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@perpustakaan_pktj0" target="_blank"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://www.threads.net/@perpustakaan_pktj" target="_blank"><i class="fa-brands fa-threads"></i></a>
                    <a href="mailto:library@pktj.ac.id"><i class="fa-regular fa-envelope"></i></a>
                    <a href="https://wa.me/6281292760076?text=Halo%20Admin%20Perpustakaan%20PKTJ,%20saya%20ingin%20bertanya%20seputar%20layanan%20perpustakaan" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>
