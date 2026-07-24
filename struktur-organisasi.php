<?php
require_once 'db.php';
$slug = 'struktur-organisasi';
$sql = "SELECT * FROM pages WHERE slug = '$slug'";
$result = $conn->query($sql);
$page_data = $result->fetch_assoc();
$page_title = $page_data ? htmlspecialchars($page_data['title']) : 'Halaman Tidak Ditemukan';
$page_content = $page_data ? $page_data['content'] : 'Konten belum tersedia.';
?>
<?php include 'includes/frontend_header.php'; ?>


    <?php
    // Ekstrak icon di awal konten jika ada
    $header_icon = '';
    if (preg_match('/^\s*(<i class="[^"]+"[^>]*><\/i>)/i', $page_content, $matches)) {
        // Hapus atribut style bawaan agar bisa kita styling ulang
        $header_icon = preg_replace('/style="[^"]*"/i', '', $matches[1]);
        $page_content = preg_replace('/^\s*(<i class="[^"]+"[^>]*><\/i>)\s*/i', '', $page_content);
    }
    ?>
    <div class="page-header">
        <div class="container">
            <h1 class="animate-up">
                <?php if($header_icon) echo "<span class=\"header-icon\" style=\"margin-right: 15px; color: var(--accent-gold);\">$header_icon</span>"; ?>
                <?php echo $page_title; ?>
            </h1>
        </div>
    </div>

    <section class="content-section">
        <div class="container animate-up delay-1">
            
            <?php if (!empty(trim(strip_tags($page_content, '<i><p><div><h3>')))): ?>
                <?php echo $page_content; ?>
            <?php endif; ?>

            <?php
            // Fetch main org chart
            $org_sql = "SELECT content FROM pages WHERE slug = 'org-chart-img'";
            $org_res = $conn->query($org_sql);
            $org_chart = ($org_res && $org_res->num_rows > 0) ? $org_res->fetch_assoc()['content'] : 'Struktur Organisasi.png';
            ?>
            <div class="org-chart">
                <img src="<?php echo htmlspecialchars(strpos($org_chart, 'http') === 0 ? $org_chart : (strpos($org_chart, 'uploads') === 0 ? $org_chart : $org_chart)); ?>" alt="Bagan Struktur Organisasi Perpustakaan PKTJ" style="width: 100%; max-width: 1000px; height: auto; display: block; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            </div>

            <h2 class="section-subtitle">Profil Pustakawan dan Pengelola Perpustakaan PKTJ</h2>
            
            <div class="staff-grid">
                <?php
                $staff_sql = "SELECT * FROM staff_members ORDER BY order_num ASC, id ASC";
                $staff_res = $conn->query($staff_sql);
                if ($staff_res && $staff_res->num_rows > 0):
                    while($s = $staff_res->fetch_assoc()):
                ?>
                <div class="staff-card">
                    <?php if ($s['photo_url']): ?>
                        <img src="<?php echo htmlspecialchars($s['photo_url']); ?>" alt="<?php echo htmlspecialchars($s['name']); ?>" class="staff-photo">
                    <?php else: ?>
                        <div class="staff-photo" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-user" style="font-size: 50px; color: #cbd5e1;"></i></div>
                    <?php endif; ?>
                    <div class="staff-info">
                        <div class="staff-name"><?php echo htmlspecialchars($s['name']); ?></div>
                        <div class="staff-role"><?php echo htmlspecialchars($s['role']); ?></div>
                        <?php if ($s['email']): ?>
                        <div class="staff-email"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($s['email']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else: 
                    echo '<p style="grid-column: 1/-1; text-align: center; color: #666;">Belum ada data staf.</p>';
                endif; 
                ?>
            </div>
        </div>
    </section>

    <!-- Footer (Reused) -->
    <?php include 'includes/frontend_footer.php'; ?>
