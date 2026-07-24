<?php
require_once '../db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $category = $conn->real_escape_string($_POST['category'] ?? 'Tidak Berkategori');
    $tags = $conn->real_escape_string($_POST['tags'] ?? '');
    $publish_date = $conn->real_escape_string($_POST['publish_date'] ?? date('Y-m-d'));
    $is_published = (int)($_POST['is_published'] ?? 1);
    
    $image_url = '';
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
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
        $check_sql = "SELECT id FROM posts WHERE slug = '$slug'";
        $res = $conn->query($check_sql);
        if ($res->num_rows == 0) break;
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }

    if (empty($error)) {
        $sql = "INSERT INTO posts (title, slug, content, image_url, category, tags, publish_date, is_published) 
                VALUES ('$title', '$slug', '$content', '$image_url', '$category', '$tags', '$publish_date', $is_published)";
                
        if ($conn->query($sql) === TRUE) {
            $success = "Berita berhasil disimpan!";
            echo "<script>setTimeout(function(){ window.location.href='index'; }, 1500);</script>";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
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
    
    <form action="create" method="POST" enctype="multipart/form-data">
        
        <div class="form-section">
            <div class="form-group">
                <label>Judul Berita</label>
                <input type="text" name="title" class="form-control" required placeholder="Masukkan Judul berita" style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0;">
                <span class="hint">Untuk penulisan judul maksimal 100 karakter</span>
            </div>
            
            <div class="form-group">
                <label>Sampul / Cover</label>
                <span class="hint" style="margin-bottom: 10px;">Disarankan memasukkan sampul / cover indah berukuran 700px x 350px</span>
                <label for="image-upload" class="custom-file-upload">
                    Pilih Sampul / Cover
                </label>
                <input id="image-upload" type="file" name="image" accept="image/*" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                <span id="file-name" style="margin-left: 10px; font-size: 13px; color: #666;"></span>
            </div>
            
            <div class="form-group">
                <label>Kategori Berita</label>
                <select name="category" class="form-control" style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0; max-width: 300px;">
                    <option value="">--Pilih Kategori--</option>
                    <option value="Layanan dan Kebijakan">Layanan dan Kebijakan</option>
                    <option value="Koleksi dan Sumber Daya Digital">Koleksi dan Sumber Daya Digital</option>
                    <option value="Agenda Akademik">Agenda Akademik</option>
                    <option value="Literasi Informasi">Literasi Informasi</option>
                    <option value="Statistik dan Kinerja">Statistik dan Kinerja</option>
                </select>
            </div>
        </div>
        
        <div class="form-section">
            <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block;">Isi berita</label>
            <textarea id="content" name="content"></textarea>
        </div>
        
        <div class="form-section">
            <div class="form-group">
                <label>Tag Berita</label>
                <span class="hint" style="color: #666;">Masukkan kata-kata kunci yang digunakan di dalam berita. Berilah tanda koma (,) untuk memisahkan kata kunci satu dengan yang lainnya.</span>
                <input type="text" name="tags" class="form-control" placeholder="Tulis Tag Disini..." style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0;">
            </div>
            
            <div class="form-group">
                <label>Tanggal Berita</label>
                <input type="date" name="publish_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="border: none; border-bottom: 1px solid #ccc; border-radius: 0; padding-left: 0; max-width: 200px;">
            </div>
            
            <div class="form-group">
                <label>Terbitkan</label>
                <div class="radio-group">
                    <label><input type="radio" name="is_published" value="1" checked> Ya</label>
                    <label><input type="radio" name="is_published" value="0"> Tidak</label>
                </div>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn-primary">SIMPAN</button>
                <a href="index" class="btn-outline">TUTUP</a>
            </div>
        </div>
        
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
