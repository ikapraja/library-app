<?php require_once 'db.php'; ?>
<?php include 'includes/frontend_header.php'; ?>


    <div class="page-header">
        <div class="container">
            <h1 class="animate-up"><span class="header-icon" style="margin-right: 15px; color: var(--accent-gold);"><i class="fa-solid fa-camera"></i></span>Galeri Visual</h1>
            <p class="animate-up delay-1">Rekam jejak, kegiatan, dan fasilitas di Perpustakaan PKTJ</p>
        </div>
    </div>

    <section class="content-section" style="padding: 60px 0; background: #f4f6f9;">
        <div class="container animate-up delay-1">
            <?php
            $search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
            $filter_type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
            
            $where_clause = "WHERE is_published = 1";
            if (!empty($search)) {
                $where_clause .= " AND title LIKE '%$search%'";
            }
            if (!empty($filter_type)) {
                $where_clause .= " AND type = '$filter_type'";
            }
            
            $query_params = "";
            if (!empty($search)) $query_params .= "&q=" . urlencode($search);
            if (!empty($filter_type)) $query_params .= "&type=" . urlencode($filter_type);
            ?>
            
            <div class="search-filter-container" style="margin-bottom: 40px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <form action="galeri" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari foto atau video..." style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <select name="type" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                            <option value="">Semua Format</option>
                            <option value="foto" <?php echo ($filter_type == 'foto') ? 'selected' : ''; ?>>Foto</option>
                            <option value="video" <?php echo ($filter_type == 'video') ? 'selected' : ''; ?>>Video</option>
                        </select>
                    </div>
                    <button type="submit" style="background: var(--primary-blue); color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-weight: 600;"><i class="fa-solid fa-search"></i> Cari</button>
                    <?php if(!empty($search) || !empty($filter_type)): ?>
                        <a href="galeri" style="background: #f1f3f4; color: #333; text-decoration: none; padding: 10px 25px; border-radius: 4px; font-weight: 600; display: flex; align-items: center;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="gallery-grid">
                <?php
                // Pagination logic
                $posts_per_page = 6;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($page < 1) $page = 1;
                $offset = ($page - 1) * $posts_per_page;
                
                $total_sql = "SELECT COUNT(*) as total FROM gallery $where_clause";
                $total_result = $conn->query($total_sql);
                $total_row = $total_result->fetch_assoc();
                $total_posts = $total_row['total'];
                $total_pages = ceil($total_posts / $posts_per_page);

                $gal_sql = "SELECT * FROM gallery $where_clause ORDER BY created_at DESC LIMIT $posts_per_page OFFSET $offset";
                $gal_result = $conn->query($gal_sql);
                
                if ($gal_result && $gal_result->num_rows > 0) {
                    while($gal_row = $gal_result->fetch_assoc()) {
                        $is_video = (isset($gal_row['type']) && $gal_row['type'] == 'video');
                        $badge_text = $is_video ? 'Video' : 'Foto';
                        $link_href = $is_video ? htmlspecialchars($gal_row['video_url']) : htmlspecialchars($gal_row['image_url']);
                        
                        echo '<a href="' . $link_href . '" class="glightbox g-card" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; background: var(--white); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); border: 1px solid #f1f5f9; transition: var(--transition);">';
                        
                        // Image/Thumbnail Container
                        echo '<div class="g-img-container" style="position: relative; height: 200px; width: 100%; border-bottom: 3px solid var(--accent-gold);">';
                        // Floating Badge
                        echo '<div class="g-badge" style="position: absolute; top: 15px; right: 15px; background: var(--accent-gold); color: var(--primary-blue); font-weight: 700; font-size: 0.8rem; padding: 5px 12px; border-radius: 20px; z-index: 2; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-transform: uppercase;">' . $badge_text . '</div>';
                        
                        echo '<div class="gallery-img" style="background-image: url(\'' . htmlspecialchars($gal_row['image_url']) . '\'); width: 100%; height: 100%; background-size: cover; background-position: center;"></div>';
                        if($is_video) {
                            echo '<div class="play-btn-overlay"><i class="fa-solid fa-play"></i></div>';
                        }
                        echo '</div>';
                        
                        // Card Bottom (Title)
                        echo '<div class="g-card-bottom" style="padding: 20px 15px; flex-grow: 1; display: flex; align-items: center; justify-content: center; background: var(--white);">';
                        echo '<h4 class="g-title" style="margin: 0; text-align: center; width: 100%; color: var(--primary-blue); font-size: 1.1rem; line-height: 1.5; font-weight: 700;">' . htmlspecialchars($gal_row['title']) . '</h4>';
                        echo '</div>';
                        
                        echo '</a>'; // end g-card
                    }
                } else {
                    echo '<p style="text-align:center; color: #777; grid-column: 1 / -1; padding: 40px;">Belum ada foto galeri yang diunggah.</p>';
                }
                ?>
            </div>
            
            <?php if (isset($total_pages) && $total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $query_params; ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $query_params; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $query_params; ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
        </div>
    </section>

    <!-- FOOTER WILL BE SYNCED LATER -->
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

    <!-- Custom JS -->
    <script src="script.js"></script>
    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        // Inisialisasi GLightbox
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });
    </script>
</body>
</html>
