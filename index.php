<?php require_once 'db.php'; ?>
<?php include 'includes/frontend_header.php'; ?>

<?php 
$hero_type = get_setting('hero_type', 'image');
$hero_image = get_setting('hero_image', 'Gedung Splash.png');
$hero_video = get_raw_setting('hero_video', '');

$hero_style = '';
if ($hero_type == 'image') {
    $hero_img_src = strpos($hero_image, 'http') === 0 ? $hero_image : $hero_image;
    $hero_style = 'style="background-image: url(\'' . htmlspecialchars($hero_img_src) . '\');"';
}
?>
    <!-- Hero Section -->
    <section class="hero" <?php echo $hero_style; ?>>
        <?php if ($hero_type == 'video' && !empty($hero_video)): 
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $hero_video, $match);
            $youtube_id = $match[1] ?? '';
            if ($youtube_id):
        ?>
        <div class="video-background">
            <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($youtube_id); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo htmlspecialchars($youtube_id); ?>&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
        <style>
            .hero { background: #000 !important; overflow: hidden; }
            .video-background { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
            .video-background iframe { position: absolute; top: 50%; left: 50%; width: 100vw; height: 56.25vw; min-height: 100vh; min-width: 177.77vh; transform: translate(-50%, -50%); }
            .hero-overlay { z-index: 1; }
            .hero-content { z-index: 2; position: relative; }
        </style>
        <?php endif; endif; ?>
        
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1 class="animate-up hero-title"><?php echo get_setting('site_name'); ?></h1>
            <p class="animate-up delay-1 hero-subtitle"><?php echo get_setting('site_name_long'); ?></p>
            
            <div class="search-container animate-up delay-3">
                <div class="search-tabs">
                    <button class="tab-btn active" onclick="switchSearch('buku')">Buku</button>
                    <button class="tab-btn" onclick="switchSearch('repositori')">Repositori</button>
                    <button class="tab-btn" onclick="switchSearch('ejournal')">E-Journal</button>
                    <button class="tab-btn" onclick="switchSearch('ebook')">E-Book</button>
                </div>
                <div id="formBuku" style="display: block;">
                    <form action="https://opac.pktj.ac.id/index.php" method="GET" target="_blank" class="search-box" style="margin: 0;">
                        <input type="text" name="keywords" placeholder="Cari judul buku atau pengarang ..." required>
                        <input type="hidden" name="search" value="search">
                        <button type="submit" class="search-submit">CARI</button>
                    </form>
                </div>
                <div id="formRepo" style="display: none;">
                    <form action="http://eprints.pktj.ac.id/cgi/search/archive/simple" method="GET" target="_blank" class="search-box" style="margin: 0;">
                        <input type="hidden" name="screen" value="Search">
                        <input type="hidden" name="dataset" value="archive">
                        <input type="hidden" name="order" value="">
                        <input type="hidden" name="q_merge" value="ANY">
                        <input type="hidden" name="_action_search" value="Search">
                        <input type="text" name="q" placeholder="Cari judul skripsi, laporan magang, dan karya ilmiah lainnya ..." required>
                        <button type="submit" class="search-submit">CARI</button>
                    </form>
                </div>
                <div id="formEjournal" style="display: none;">
                    <form action="https://ktj.pktj.ac.id/index.php/ktj/search/search" method="GET" target="_blank" class="search-box" style="margin: 0;">
                        <input type="text" name="query" placeholder="Cari judul artikel jurnal, penulis, atau kata kunci ..." required>
                        <button type="submit" class="search-submit">CARI</button>
                    </form>
                </div>
                <div id="formEbook" style="display: none;">
                    <!-- Untuk E-book Digido/Perpustakaan Digital -->
                    <form action="https://pktj.perpustakaan.co.id/view/buku.ks/koleksi-buku" method="GET" target="_blank" class="search-box" style="margin: 0;">
                        <input type="text" name="cari" placeholder="Cari koleksi E-Book digital Perpustakaan PKTJ ..." required>
                        <input type="hidden" name="filter" value="judul">
                        <button type="submit" class="search-submit">CARI</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Access Services -->
    <section class="quick-access" style="background: #fff;">
        <div class="container">
            <h3 class="section-title text-center">Akses Cepat Layanan</h3>
            <div class="service-grid">
                <a href="bebas-pustaka" class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    <h4>Bebas Pustaka</h4>
                </a>
                <a href="https://forms.gle/BV3GYQULNFRhoALT9" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-lightbulb"></i></div>
                    <h4>Usulan Koleksi</h4>
                </a>
                <a href="https://ktj.pktj.ac.id/index.php/ktj" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-book-journal-whills"></i></div>
                    <h4>E-Journal</h4>
                </a>
                <a href="https://forms.gle/vQoy73BuDApbJzZa8" target="_blank" class="service-card">
                    <div class="service-icon"><i class="fa-solid fa-users-rectangle"></i></div>
                    <h4>Library Class</h4>
                </a>
            </div>
        </div>
    </section>

    <!-- Highlights Section (Blog Posts/News) -->
    <section class="highlights bg-light">
        <div class="container">
            <h3 class="section-title text-center">Berita & Informasi Terkini</h3>
            <div class="news-grid">
                <?php
                // Fetch latest 3 posts
                $sql = "SELECT * FROM posts WHERE is_published = 1 ORDER BY COALESCE(updated_at, publish_date, created_at) DESC LIMIT 3";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $image = !empty($row['image_url']) ? $row['image_url'] : 'https://images.unsplash.com/photo-1541963463532-d68292c34b19?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                        $date = date('d F Y', strtotime($row['updated_at'] ?? $row['publish_date'] ?? $row['created_at']));
                        $excerpt = substr(strip_tags($row['content']), 0, 100) . '...';
                        
                        echo '<article class="news-card" style="transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform=\'translateY(-5px)\'" onmouseout="this.style.transform=\'translateY(0)\'">';
                        $news_link = !empty($row['slug']) ? 'berita/' . htmlspecialchars($row['slug']) : 'detail-berita?id=' . $row['id'];
                        echo '<a href="' . $news_link . '" style="text-decoration: none; color: inherit; display: block;">';
                        echo '<div class="news-img" style="background-image: url(\'' . htmlspecialchars($image) . '\')"></div>';
                        echo '<div class="news-content">';
                        echo '<h4>' . htmlspecialchars($row['title']) . '</h4>';
                        echo '<div class="news-meta"><i class="fa-regular fa-calendar"></i> ' . $date . ' | <i class="fa-regular fa-user"></i> Admin</div>';
                        echo '<p style="color: #666;">' . $excerpt . '</p>';
                        echo '<span class="read-more" style="color: var(--accent-gold); font-weight: 600; margin-top: 10px; display: inline-block;">Read More <i class="fa-solid fa-arrow-right" style="font-size: 0.8em; margin-left: 5px;"></i></span>';
                        echo '</div>';
                        echo '</a>';
                        echo '</article>';
                    }
                } else {
                    echo '<p style="text-align:center; grid-column: 1 / -1;">Belum ada sorotan kegiatan.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Instagram Feed Section -->
    <section class="gallery-section" style="padding: 80px 0; overflow: hidden; background: #fff;">
        <div class="container" style="max-width: 1200px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 15px;">
                <h3 class="section-title" style="margin-bottom: 15px;">Perpustakaan PKTJ on Instagram</h3>
                <a href="https://www.instagram.com/perpustakaan_pktj/" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: var(--accent-gold); color: var(--primary-blue); border: 2px solid var(--accent-gold); padding: 10px 25px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 10px rgba(246, 168, 33, 0.3);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 15px rgba(246, 168, 33, 0.5)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(246, 168, 33, 0.3)';">
                    <i class="fa-brands fa-instagram" style="font-size: 1.2rem;"></i> Follow @perpustakaan_pktj
                </a>
            </div>
            
            <!-- Instagram Feed Widget (Curator.io) -->
            <div id="curator-feed-default-feed-layout"><a href="https://curator.io" target="_blank" class="crt-logo crt-tag">Powered by Curator.io</a></div>
            
            <script type="text/javascript">
            /* curator-feed-default-feed-layout */
            (function(){
            var i,e,d=document,s="script";i=d.createElement("script");i.async=1;i.charset="UTF-8";
            i.src="https://cdn.curator.io/published/ffa15330-e5db-45b4-8e4c-a0af52e646cb.js";
            e=d.getElementsByTagName(s)[0];e.parentNode.insertBefore(i, e);
            })();
            </script>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq bg-light">
        <div class="container">
            <h3 class="section-title text-center">FAQ Perpustakaan PKTJ</h3>
            <div class="accordion">
                <?php
                $homepage_faqs = $conn->query("SELECT * FROM chatbot_faq WHERE show_on_homepage = 1 ORDER BY id ASC");
                if ($homepage_faqs && $homepage_faqs->num_rows > 0) {
                    while ($faq = $homepage_faqs->fetch_assoc()) {
                        echo '<div class="accordion-item">';
                        echo '<button class="accordion-btn">' . htmlspecialchars($faq['question']) . ' <i class="fa-solid fa-plus"></i></button>';
                        echo '<div class="accordion-content">';
                        
                        $answer = $faq['answer'];
                        // Render Markdown bold and italic
                        $answer = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $answer);
                        $answer = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $answer);
                        
                        echo '<p>' . nl2br($answer) . '</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="text-align: center; color: #666;">Belum ada FAQ yang ditampilkan.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <?php include 'includes/frontend_footer.php'; ?>
