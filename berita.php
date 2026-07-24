<?php require_once 'db.php'; ?>
<?php include 'includes/frontend_header.php'; ?>


    <div class="page-header">
        <div class="container">
            <h1 class="animate-up"><span class="header-icon" style="margin-right: 15px; color: var(--accent-gold);"><i class="fa-solid fa-newspaper"></i></span>Warta & Pengumuman</h1>
            <p class="animate-up delay-1">Berita, informasi, dan pengumuman terbaru dari Perpustakaan PKTJ</p>
        </div>
    </div>

    <section class="highlights" style="padding: 60px 0;">
        <div class="container">
            <?php
            $search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
            $cat = isset($_GET['cat']) ? $conn->real_escape_string($_GET['cat']) : '';
            
            $where_clause = "WHERE is_published = 1";
            if (!empty($search)) {
                $where_clause .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
            }
            if (!empty($cat)) {
                $where_clause .= " AND category = '$cat'";
            }
            
            $query_params = "";
            if (!empty($search)) $query_params .= "&q=" . urlencode($search);
            if (!empty($cat)) $query_params .= "&cat=" . urlencode($cat);
            ?>
            
            <div class="search-filter-container" style="margin-bottom: 30px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <form action="berita" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari berita..." style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <select name="cat" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
                            <option value="">Semua Kategori</option>
                            <option value="Layanan dan Kebijakan" <?php echo ($cat == 'Layanan dan Kebijakan') ? 'selected' : ''; ?>>Layanan dan Kebijakan</option>
                            <option value="Koleksi dan Sumber Daya Digital" <?php echo ($cat == 'Koleksi dan Sumber Daya Digital') ? 'selected' : ''; ?>>Koleksi dan Sumber Daya Digital</option>
                            <option value="Agenda Akademik" <?php echo ($cat == 'Agenda Akademik') ? 'selected' : ''; ?>>Agenda Akademik</option>
                            <option value="Literasi Informasi" <?php echo ($cat == 'Literasi Informasi') ? 'selected' : ''; ?>>Literasi Informasi</option>
                            <option value="Statistik dan Kinerja" <?php echo ($cat == 'Statistik dan Kinerja') ? 'selected' : ''; ?>>Statistik dan Kinerja</option>
                        </select>
                    </div>
                    <button type="submit" style="background: var(--primary-blue); color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-weight: 600;"><i class="fa-solid fa-search"></i> Cari</button>
                    <?php if(!empty($search) || !empty($cat)): ?>
                        <a href="berita" style="background: #f1f3f4; color: #333; text-decoration: none; padding: 10px 25px; border-radius: 4px; font-weight: 600; display: flex; align-items: center;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="news-grid">
                <?php
                // Pagination logic
                $posts_per_page = 6; // Menampilkan 6 berita per halaman agar terlihat rapi dalam 2 baris (3 kolom)
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($page < 1) $page = 1;
                $offset = ($page - 1) * $posts_per_page;
                
                $total_sql = "SELECT COUNT(*) as total FROM posts $where_clause";
                $total_result = $conn->query($total_sql);
                $total_row = $total_result->fetch_assoc();
                $total_posts = $total_row['total'];
                $total_pages = ceil($total_posts / $posts_per_page);

                // Fetch posts for current page
                $sql = "SELECT * FROM posts $where_clause ORDER BY COALESCE(updated_at, publish_date, created_at) DESC LIMIT $posts_per_page OFFSET $offset";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $image = !empty($row['image_url']) ? $row['image_url'] : 'https://images.unsplash.com/photo-1541963463532-d68292c34b19?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                        $date = date('d F Y', strtotime($row['updated_at'] ?? $row['publish_date'] ?? $row['created_at']));
                        $excerpt = substr(strip_tags($row['content']), 0, 100) . '...';
                        
                        echo '<article class="news-card" style="transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform=\'translateY(-5px)\'" onmouseout="this.style.transform=\'translateY(0)\'">';
                        echo '<a href="berita/' . htmlspecialchars($row['slug']) . '" style="text-decoration: none; color: inherit; display: block;">';
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
                    echo '<p style="text-align:center; grid-column: 1 / -1;">Belum ada berita.</p>';
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

    <?php include 'includes/frontend_footer.php'; ?>
