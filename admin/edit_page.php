<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo "<script>window.location.href='pages';</script>";
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $conn->real_escape_string($_POST['content']);
    $title = $conn->real_escape_string($_POST['title']);
    
    $sql = "UPDATE pages SET content = '$content', title = '$title' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $success = "Halaman berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui: " . $conn->error;
    }
}

$sql = "SELECT * FROM pages WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<script>window.location.href='pages';</script>";
    exit;
}
$page = $result->fetch_assoc();
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
        <h2><i class="fa-solid fa-pen-to-square"></i> Edit Halaman</h2>
        <a href="pages" class="btn btn-outline" style="padding: 8px 15px; border: 1px solid #ddd;">Kembali</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_page">
        <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
        
        <div class="form-section">
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block; font-size: 1.1rem;">Judul Halaman (H1)</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($page['title']); ?>" required style="font-size: 1.2rem; padding: 15px;">
            </div>
            
            <div class="form-group">
                <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block; font-size: 1.1rem;">Isi Halaman</label>
                <textarea id="content" name="content"><?php echo htmlspecialchars($page['content']); ?></textarea>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> SIMPAN PERUBAHAN</button>
                <a href="pages" class="btn-outline">BATAL</a>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
