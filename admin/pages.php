<?php
require_once '../db.php';
include 'includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check if it's a default page to prevent deletion
    $check_sql = "SELECT slug FROM pages WHERE id = $id";
    $check_res = $conn->query($check_sql);
    if ($check_res && $check_res->num_rows > 0) {
        $slug = $check_res->fetch_assoc()['slug'];
        $protected_slugs = ['visi-misi', 'tata-tertib', 'layanan-sirkulasi', 'layanan-referensi', 'bebas-pustaka', 'struktur-organisasi', 'fasilitas', 'org-chart-img'];
        
        if (in_array($slug, $protected_slugs)) {
            $_SESSION['error'] = "Halaman utama (default) tidak boleh dihapus!";
        } else {
            $del_sql = "DELETE FROM pages WHERE id = $id";
            if ($conn->query($del_sql)) {
                $_SESSION['success'] = "Halaman berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus halaman: " . $conn->error;
            }
        }
    }
    echo "<script>window.location.href='pages';</script>";
    exit;
}

// Fetch all regular pages
$sql = "SELECT * FROM pages WHERE slug NOT IN ('struktur-organisasi', 'fasilitas', 'org-chart-img') ORDER BY id ASC";
$result = $conn->query($sql);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fa-regular fa-file-lines"></i> Manajemen Halaman</h2>
        <a href="create_page" class="btn btn-primary" style="background: var(--accent); color: var(--primary-bg); font-weight: bold; padding: 10px 15px; border-radius: 5px;"><i class="fa-solid fa-plus"></i> Tambah Halaman Baru</a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error" style="background-color: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #dc2626;">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Judul Halaman</th>
                    <th>Slug URL</th>
                    <th>Terakhir Diperbarui</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0) {
                    $no = 1;
                    $protected_slugs = ['visi-misi', 'tata-tertib', 'layanan-sirkulasi', 'layanan-referensi', 'bebas-pustaka'];
                    while($row = $result->fetch_assoc()): 
                        $updated = date('d M Y, H:i', strtotime($row['updated_at']));
                        $is_protected = in_array($row['slug'], $protected_slugs);
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td style="color: #666; font-family: monospace;"><?php echo htmlspecialchars($row['slug']); ?></td>
                    <td style="color: #666; font-size: 0.9em;"><?php echo $updated; ?></td>
                    <td>
                        <a href="edit_page?id=<?php echo $row['id']; ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px; margin-right: 5px;"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                        <?php if (!$is_protected): ?>
                            <a href="pages?delete=<?php echo $row['id']; ?>" class="btn" style="background-color: #dc2626; color: white; padding: 5px 10px; font-size: 12px; border-radius: 4px;" onclick="return confirm('Apakah Anda yakin ingin menghapus halaman ini secara permanen?');"><i class="fa-solid fa-trash"></i> Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                } else {
                    echo '<tr><td colspan="5" style="text-align:center;">Belum ada halaman.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
