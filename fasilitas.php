<?php
require_once 'db.php';
$slug = 'fasilitas';
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
            <div class="facilities-grid">
                <?php
                $fac_sql = "SELECT * FROM facilities ORDER BY order_num ASC, id ASC";
                $fac_res = $conn->query($fac_sql);
                if ($fac_res && $fac_res->num_rows > 0):
                    while($f = $fac_res->fetch_assoc()):
                ?>
                <div class="facility-card">
                    <?php if (!empty($f['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($f['image_url']); ?>" alt="<?php echo htmlspecialchars($f['name']); ?>" class="facility-img">
                    <?php else: ?>
                        <div class="facility-img" style="background: #f8fafc; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-image" style="font-size: 50px; color: #cbd5e1;"></i></div>
                    <?php endif; ?>
                    <div class="facility-content">
                        <span class="facility-location"><?php echo htmlspecialchars($f['location']); ?></span>
                        <h3><?php echo htmlspecialchars($f['name']); ?></h3>
                        <p><?php echo htmlspecialchars($f['description']); ?></p>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else:
                    echo '<p style="grid-column: 1/-1; text-align: center; color: #666;">Belum ada data fasilitas.</p>';
                endif; 
                ?>
            </div>
        </div>
    </section>

    <!-- Footer (Reused, keeping it minimal here for brevity but user can see it in index) -->
    <?php include 'includes/frontend_footer.php'; ?>
