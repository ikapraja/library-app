<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $content = $conn->real_escape_string($_POST['content']);
    
    // Auto-generate slug from title
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // Check if slug already exists
    $check_sql = "SELECT id FROM pages WHERE slug = '$slug'";
    $check_res = $conn->query($check_sql);
    
    if ($check_res->num_rows > 0) {
        $slug = $slug . '-' . time();
    }
    
    if (empty($title)) {
        $error = "Judul halaman wajib diisi.";
    } else {
        $sql = "INSERT INTO pages (title, slug, content) VALUES ('$title', '$slug', '$content')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Halaman baru berhasil ditambahkan!";
            echo "<script>window.location.href='pages';</script>";
            exit;
        } else {
            $error = "Gagal menambahkan halaman: " . $conn->error;
        }
    }
}
?>

<style>
    .form-section { background: white; border: 1px solid #e0e0e0; border-radius: 8px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .btn-primary { background: var(--primary-bg); color: white; border: none; padding: 12px 25px; border-radius: 4px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    .btn-primary:hover { background: var(--accent); color: var(--primary-bg); }
    .btn-outline { background: transparent; color: #555; border: 1px solid #ccc; padding: 12px 25px; border-radius: 4px; font-weight: 600; text-decoration: none; display: inline-block; transition: 0.3s; }
    .btn-outline:hover { background: #eee; }
    .action-buttons { display: flex; gap: 15px; margin-top: 20px; }
</style>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fa-solid fa-plus-circle"></i> Tambah Halaman Baru</h2>
        <a href="pages" class="btn btn-outline" style="padding: 8px 15px; border: 1px solid #ddd;">Kembali</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="create_page">
        <div class="form-section">
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block; font-size: 1.1rem;">Judul Halaman (H1)</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Peraturan Baru Perpustakaan" required style="font-size: 1.2rem; padding: 15px;">
                <small style="color: #666; margin-top: 5px; display: block;">Slug URL akan otomatis dibuat dari judul ini.</small>
            </div>
            
            <div class="form-group">
                <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block; font-size: 1.1rem;">Isi Halaman</label>
                <textarea id="content" name="content" placeholder="Ketik isi halaman di sini..."></textarea>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> SIMPAN HALAMAN</button>
                <a href="pages" class="btn-outline">BATAL</a>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
