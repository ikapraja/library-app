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
                            <?php 
                            $logo = get_setting('site_logo');
                            if(strpos($logo, 'http') !== 0) $logo = $logo;
                            ?>
                            <img src="<?php echo $logo; ?>" alt="Logo <?php echo get_setting('site_name'); ?>" style="height: 60px; width: auto; display: block;">
                        </div>
                    </div>
                    <p class="npp-footer">NPP <?php echo get_setting('npp'); ?></p>
                    <ul class="contact-list">
                        <?php if(get_setting('address_1_name')): ?>
                        <li style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fa-solid fa-location-dot fa-fw" style="margin-top: 5px;"></i>
                            <a href="<?php echo get_setting('address_1_map'); ?>" target="_blank" style="color: inherit; text-decoration: none; border-bottom: 1px dashed rgba(255,255,255,0.3); transition: all 0.3s; line-height: 1.5;" onmouseover="this.style.color='#f6a821'; this.style.borderColor='#f6a821';" onmouseout="this.style.color='inherit'; this.style.borderColor='rgba(255,255,255,0.3)';">
                                <strong><?php echo get_setting('address_1_name'); ?>:</strong><br><?php echo get_setting('address_1_text'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if(get_setting('address_2_name')): ?>
                        <li style="margin-bottom: 10px; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fa-solid fa-location-dot fa-fw" style="margin-top: 5px;"></i>
                            <a href="<?php echo get_setting('address_2_map'); ?>" target="_blank" style="color: inherit; text-decoration: none; border-bottom: 1px dashed rgba(255,255,255,0.3); transition: all 0.3s; line-height: 1.5;" onmouseover="this.style.color='#f6a821'; this.style.borderColor='#f6a821';" onmouseout="this.style.color='inherit'; this.style.borderColor='rgba(255,255,255,0.3)';">
                                <strong><?php echo get_setting('address_2_name'); ?>:</strong><br><?php echo get_setting('address_2_text'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li><i class="fa-solid fa-envelope fa-fw"></i> <?php echo get_setting('contact_email'); ?></li>
                        <li><i class="fa-solid fa-phone fa-fw"></i> <?php echo get_setting('contact_phone'); ?></li>
                    </ul>
                </div>

                <div class="footer-hours">
                    <h4>Jam Layanan</h4>
                    <ul>
                        <?php
                        $jam_json = get_raw_setting('jam_layanan_json');
                        $jam_array = $jam_json ? json_decode($jam_json, true) : [];
                        if (is_array($jam_array)) {
                            foreach ($jam_array as $jam) {
                                echo '<li><span class="day">' . htmlspecialchars($jam['day']) . '</span> <span class="time">: ' . htmlspecialchars($jam['time']) . '</span></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Pusat Informasi</h4>
                    <ul>
                        <?php
                        $pusat_json = get_raw_setting('pusat_informasi_json');
                        $pusat_array = $pusat_json ? json_decode($pusat_json, true) : [];
                        if (is_array($pusat_array)) {
                            foreach ($pusat_array as $pusat) {
                                echo '<li><a href="' . htmlspecialchars($pusat['url']) . '" target="_blank"><i class="fa-solid fa-chevron-right"></i> ' . htmlspecialchars($pusat['title']) . '</a></li>';
                            }
                        }
                        ?>
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
                <p>&copy; <?php echo date('Y'); ?> <?php echo get_setting('site_name'); ?> <?php echo get_setting('site_name_long'); ?>.</p>
                <div class="social-links" style="display: flex; align-items: center; gap: 15px;">
                    <span>Temukan Kami:</span>
                    <?php
                    $social_json = get_raw_setting('social_media_json');
                    $social_array = $social_json ? json_decode($social_json, true) : [];
                    if (is_array($social_array)) {
                        foreach ($social_array as $social) {
                            if ($social['icon_type'] == 'fa') {
                                echo '<a href="' . htmlspecialchars($social['url']) . '" target="_blank"><i class="fa-brands ' . htmlspecialchars($social['icon_val']) . '"></i></a>';
                            } else {
                                $img_src = strpos($social['icon_val'], 'http') === 0 ? $social['icon_val'] : $social['icon_val'];
                                echo '<a href="' . htmlspecialchars($social['url']) . '" target="_blank" style="display: inline-flex; align-items: center; justify-content: center;"><img src="' . htmlspecialchars($img_src) . '" alt="Social Icon" style="width: 20px; height: 20px; object-fit: contain; filter: brightness(0) invert(1); opacity: 0.8; transition: 0.3s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8"></a>';
                            }
                        }
                    }
                    ?>

                    <a href="mailto:<?php echo get_setting('contact_email'); ?>"><i class="fa-regular fa-envelope"></i></a>
                    <a href="https://wa.me/<?php echo get_setting('contact_wa'); ?>?text=Halo%20Admin%20<?php echo urlencode(get_setting('site_name')); ?>,%20saya%20ingin%20bertanya%20seputar%20layanan%20perpustakaan" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="script.js"></script>
    <!-- Swiper JS -->
    <!-- Chatbot Widget -->
    <?php include 'chatbot_widget.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
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

        // Inisialisasi Swiper.js untuk Galeri (hanya jika ada elemen .gallery-swiper)
        if(document.querySelector('.gallery-swiper')) {
            const swiper = new Swiper('.gallery-swiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                grabCursor: true,
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: { slidesPerView: 2, spaceBetween: 20 },
                    992: { slidesPerView: 3, spaceBetween: 30 },
                    1200: { slidesPerView: 4, spaceBetween: 30 }
                }
            });
        }
    </script>
</body>
</html>
