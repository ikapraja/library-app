<?php
require_once '../db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: index");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $category = $conn->real_escape_string($_POST['category'] ?? 'Tidak Berkategori');
    $tags = $conn->real_escape_string($_POST['tags'] ?? '');
    $publish_date = $conn->real_escape_string($_POST['publish_date'] ?? date('Y-m-d'));
    $is_published = (int)($_POST['is_published'] ?? 1);
    
    // Default image clause is empty, meaning keep old image
    $image_clause = "";
    
    // Handle file upload if new image provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
                $image_clause = ", image_url = '$image_url'";
            } else {
                $error = "Gagal mengunggah gambar sampul.";
            }
        } else {
            $error = "Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.";
        }
    }
    
    // Generate slug
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    $original_slug = $slug;
    $counter = 1;
    while (true) {
        $check_sql = "SELECT id FROM posts WHERE slug = '$slug' AND id != $id";
        $res = $conn->query($check_sql);
        if ($res->num_rows == 0) break;
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }

    if (empty($error)) {
        $sql = "UPDATE posts SET 
                title = '$title', 
                slug = '$slug',
                content = '$content', 
                category = '$category', 
                tags = '$tags', 
                publish_date = '$publish_date', 
                is_published = $is_published 
                $image_clause 
                WHERE id = $id";
                
        if ($conn->query($sql) === TRUE) {
            $success = "Berita berhasil diperbarui!";
            echo "<script>setTimeout(function(){ window.location.href='index'; }, 1500);</script>";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch existing data
$sql = "SELECT * FROM posts WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    header("Location: index");
    exit;
}
$post = $result->fetch_assoc();

?>



<style>
    .form-section { background: white; border: 1px solid #e0e0e0; border-radius: 4px; padding: 25px; margin-bottom: 25px; }
    .form-title { font-size: 16px; font-weight: 700; color: #333; margin-top: 0; margin-bottom: 20px; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .hint { font-size: 12px; color: #00897b; margin-top: 5px; margin-bottom: 15px; display: block; }
    .custom-file-upload { display: inline-block; padding: 10px 20px; cursor: pointer; background: #f6a821; color: #333; border-radius: 4px; font-weight: 700; font-size: 14px; border: 1px solid #d9931b; }
    .custom-file-upload:hover { background: #e0981b; }
    input[type="file"] { display: none; }
    .radio-group { display: flex; gap: 20px; margin-top: 10px; }
    .radio-group label { font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 5px; }
    .btn-outline { background: transparent; border: 1px solid #ccc; color: #333; padding: 10px 20px; font-weight: 600; cursor: pointer; border-radius: 4px; }
    .btn-outline:hover { background: #f4f4f4; }
    .btn-primary { background: #1a365d; border: none; color: white; padding: 10px 20px; font-weight: 600; cursor: pointer; border-radius: 4px; }
    .btn-primary:hover { background: #2d4a77; }
    .action-buttons { display: flex; gap: 10px; margin-top: 20px; }
</style>

<div class="card" style="max-width: 900px; margin: 0 auto; background: transparent; box-shadow: none; padding: 0;">
    
    <div style="margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 24px;">UBAH DATA BERITA</h2>
    </div>
    
    <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    
    <form action="edit" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
        
        <div class="form-section">
            <div class="form-group">
                <label>Judul Berita</label>
                <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($post['title']); ?>" style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0;">
                <span class="hint">Untuk penulisan judul maksimal 100 karakter</span>
            </div>
            
            <div class="form-group">
                <label>Sampul / Cover</label>
                <span class="hint" style="margin-bottom: 10px;">Disarankan memasukkan sampul / cover indah berukuran 700px x 350px. Kosongkan jika tidak ingin mengubah cover saat ini.</span>
                <label for="image-upload" class="custom-file-upload">
                    Ubah Sampul / Cover
                </label>
                <input id="image-upload" type="file" name="image" accept="image/*" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                <span id="file-name" style="margin-left: 10px; font-size: 13px; color: #666;">
                    <?php echo $post['image_url'] ? 'Cover saat ini: ' . basename($post['image_url']) : ''; ?>
                </span>
            </div>
            
            <div class="form-group">
                <label>Kategori Berita</label>
                <select name="category" class="form-control" style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0; max-width: 300px;">
                    <option value="">--Pilih Kategori--</option>
                    <option value="Layanan dan Kebijakan" <?php echo ($post['category'] == 'Layanan dan Kebijakan') ? 'selected' : ''; ?>>Layanan dan Kebijakan</option>
                    <option value="Koleksi dan Sumber Daya Digital" <?php echo ($post['category'] == 'Koleksi dan Sumber Daya Digital') ? 'selected' : ''; ?>>Koleksi dan Sumber Daya Digital</option>
                    <option value="Agenda Akademik" <?php echo ($post['category'] == 'Agenda Akademik') ? 'selected' : ''; ?>>Agenda Akademik</option>
                    <option value="Literasi Informasi" <?php echo ($post['category'] == 'Literasi Informasi') ? 'selected' : ''; ?>>Literasi Informasi</option>
                    <option value="Statistik dan Kinerja" <?php echo ($post['category'] == 'Statistik dan Kinerja') ? 'selected' : ''; ?>>Statistik dan Kinerja</option>
                </select>
            </div>
        </div>
        
        <div class="form-section">
            <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block;">Isi berita</label>
            <textarea id="content" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        
        <div class="form-section">
            <div class="form-group">
                <label>Tag Berita</label>
                <span class="hint" style="color: #666;">Masukkan kata-kata kunci yang digunakan di dalam berita. Berilah tanda koma (,) untuk memisahkan kata kunci satu dengan yang lainnya.</span>
                <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($post['tags'] ?? ''); ?>" style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0;">
            </div>
            
            <div class="form-group">
                <label>Tanggal Berita</label>
                <input type="date" name="publish_date" class="form-control" value="<?php echo $post['publish_date'] ?? date('Y-m-d', strtotime($post['created_at'])); ?>" style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0; max-width: 200px;">
            </div>
            
            <div class="form-group">
                <label>Terbitkan</label>
                <div class="radio-group">
                    <label><input type="radio" name="is_published" value="1" <?php echo ($post['is_published'] == 1) ? 'checked' : ''; ?>> Ya</label>
                    <label><input type="radio" name="is_published" value="0" <?php echo ($post['is_published'] == 0) ? 'checked' : ''; ?>> Tidak</label>
                </div>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn-primary">SIMPAN</button>
                <a href="index" class="btn-outline">BATAL</a>
            </div>
        </div>
        
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
