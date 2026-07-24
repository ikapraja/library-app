<?php
require_once '../db.php';
require_once 'includes/header.php';

// Fetch all posts
$sql = "SELECT * FROM posts ORDER BY COALESCE(updated_at, publish_date, created_at) DESC";
$result = $conn->query($sql);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="margin: 0;">Berita & Informasi Terkini</h2>
        <div>
            <input type="text" id="searchNews" class="form-control" placeholder="Cari berita..." style="display: inline-block; width: 250px; margin-right: 10px; padding: 10px 15px;">
            <a href="create" class="btn">+ Tambah Berita Baru</a>
        </div>
    </div>
    
    <table id="newsTable">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Dilihat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if($row['image_url']): ?>
                            <img src="../<?php echo htmlspecialchars($row['image_url']); ?>" style="max-width: 100px; border-radius: 4px;" alt="Thumbnail">
                        <?php else: ?>
                            <span style="color: #999; font-size: 12px;">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                        <span style="font-size: 12px; color: #666;"><i class="fa-solid fa-folder"></i> <?php echo htmlspecialchars($row['category'] ?? 'Tidak Berkategori'); ?></span>
                    </td>
                    <td>
                        <?php if(isset($row['is_published']) && $row['is_published'] == 1): ?>
                            <span style="background: #e6f4ea; color: #1e8e3e; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Terbit</span>
                        <?php else: ?>
                            <span style="background: #f1f3f4; color: #5f6368; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d M Y', strtotime($row['updated_at'] ?? $row['publish_date'] ?? $row['created_at'])); ?></td>
                    <td><span style="background: #eef2f5; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: #444;"><i class="fa-solid fa-eye"></i> <?php echo number_format($row['views'] ?? 0); ?></span></td>
                    <td>
                        <a href="edit?id=<?php echo $row['id']; ?>" class="btn" style="background: #f6a821; color: #fff; padding: 5px 10px; font-size: 13px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 5px;"><i class="fa-solid fa-pen"></i> Edit</a><br>
                        <a href="delete?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 13px;" onclick="return confirm('Yakin ingin menghapus berita ini?');"><i class="fa-solid fa-trash"></i> Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 30px;">Belum ada berita. Silakan tambahkan berita baru.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('searchNews').addEventListener('keyup', function() {
    var searchValue = this.value.toLowerCase();
    var tableRows = document.querySelectorAll('#newsTable tbody tr');
    
    tableRows.forEach(function(row) {
        if (row.children.length > 1) { // abaikan baris kosong
            var textContent = row.textContent.toLowerCase();
            if (textContent.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
