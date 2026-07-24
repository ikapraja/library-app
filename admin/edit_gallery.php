<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}

require_once '../db.php';
require_once 'includes/header.php';

$error = '';
$success = '';
$gallery = null;

function getYoutubeId($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : null;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM gallery WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $gallery = $result->fetch_assoc();
    } else {
        echo "<script>alert('Galeri tidak ditemukan.'); window.location.href='gallery';</script>";
        exit;
    }
} else {
    header("Location: gallery");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $type = $conn->real_escape_string($_POST['type']);
    $is_published = (isset($_POST['is_published']) && $_POST['is_published'] == '0') ? 0 : 1;
    $video_url = $conn->real_escape_string($_POST['video_url']);
    
    $image_url_update = "";
    
    if ($type == 'photo') {
        $video_url = ''; // clear if changed to photo
    }

    // Check if new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
                
                // Remove old image if exists and it's a local file
                if ($gallery['image_url'] && file_exists('../' . $gallery['image_url']) && strpos($gallery['image_url'], 'http') !== 0) {
                    unlink('../' . $gallery['image_url']);
                }
                
                $image_url_update = ", image_url = '$image_url'";
            } else {
                $error = "Gagal mengunggah gambar baru.";
            }
        } else {
            $error = "Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.";
        }
    } else if ($type == 'video' && !empty($video_url) && empty($image_url_update)) {
        // If type is video, no new image uploaded, maybe video_url changed, let's update thumbnail if they don't have local image
        if (strpos($gallery['image_url'], 'http') === 0 || empty($gallery['image_url'])) {
            $yt_id = getYoutubeId($video_url);
            if ($yt_id) {
                $image_url = "https://img.youtube.com/vi/{$yt_id}/maxresdefault.jpg";
                $image_url_update = ", image_url = '$image_url'";
            }
        }
    }
    
    if (empty($error)) {
        $sql = "UPDATE gallery SET title = '$title', type = '$type', is_published = $is_published, video_url = '$video_url' $image_url_update WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            $success = "Galeri berhasil diperbarui!";
            // refresh data
            $result = $conn->query("SELECT * FROM gallery WHERE id = $id");
            $gallery = $result->fetch_assoc();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Edit Galeri</h2>
        <a href="gallery" class="btn" style="background: #ccc; color: #333;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
    
    <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    
    <?php if($gallery): ?>
    <form action="edit_gallery?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tipe Galeri</label>
            <select name="type" id="typeSelect" class="form-control" required>
                <option value="photo" <?php echo ($gallery['type'] == 'photo') ? 'selected' : ''; ?>>Foto (Gambar Biasa)</option>
                <option value="video" <?php echo ($gallery['type'] == 'video') ? 'selected' : ''; ?>>Video (Tautan Youtube)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Judul Kegiatan / Keterangan</label>
            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($gallery['title']); ?>">
        </div>
        
        <div class="form-group" id="videoUrlGroup">
            <label>Tautan (Link) Youtube</label>
            <input type="url" name="video_url" class="form-control" value="<?php echo htmlspecialchars($gallery['video_url'] ?? ''); ?>">
            <p style="font-size: 12px; color: #777; margin-top: 5px;">Thumbnail otomatis diperbarui dari Youtube jika Anda tidak mengunggah foto sampul kustom.</p>
        </div>
        
        <div class="form-group">
            <label>Status Publikasi</label>
            <select name="is_published" class="form-control" required>
                <option value="1" <?php echo (isset($gallery['is_published']) && $gallery['is_published'] == 1) ? 'selected' : ''; ?>>Terbit (Tampilkan di Beranda)</option>
                <option value="0" <?php echo (isset($gallery['is_published']) && $gallery['is_published'] == 0) ? 'selected' : ''; ?>>Draft (Sembunyikan Sementara)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Ganti Foto Sampul <span style="font-weight: normal; font-size: 0.85em; color: #666;">(Kosongkan jika tidak ingin mengubah)</span></label>
            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput" style="padding: 10px;">
            <p style="font-size: 12px; color: #777; margin-top: 5px;">Format disarankan: JPG, PNG, atau WebP. Maksimal 2MB.</p>
        </div>
        
        <div id="imagePreview" style="margin-bottom: 20px;">
            <label>Preview Gambar Saat Ini:</label>
            <div style="margin-top: 10px;">
                <?php
                $img_src = $gallery['image_url'];
                if (strpos($img_src, 'http') !== 0 && !empty($img_src)) {
                    $img_src = '../' . $img_src;
                }
                ?>
                <img id="preview" src="<?php echo htmlspecialchars($img_src); ?>" alt="Preview" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>
        
        <button type="submit" class="btn"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
    </form>
    <?php endif; ?>
</div>

<script>
    const typeSelect = document.getElementById('typeSelect');
    const videoUrlGroup = document.getElementById('videoUrlGroup');
    
    function toggleFields() {
        if (typeSelect.value === 'video') {
            videoUrlGroup.style.display = 'block';
        } else {
            videoUrlGroup.style.display = 'none';
        }
    }
    
    typeSelect.addEventListener('change', toggleFields);
    toggleFields(); // run on load

    document.getElementById('imageInput').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
