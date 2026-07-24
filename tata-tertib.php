<?php
require_once 'db.php';
$slug = 'tata-tertib';
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
            <div class="content-card">
                <?php echo $page_content; ?>
            </div>
        </div>
    </section>

    <!-- Footer (Reused, keeping it minimal here for brevity but user can see it in index) -->
    <?php include 'includes/frontend_footer.php'; ?>
