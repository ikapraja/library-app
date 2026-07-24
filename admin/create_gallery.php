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

// Function to get YT ID
function getYoutubeId($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $type = $conn->real_escape_string($_POST['type']);
    $is_published = (isset($_POST['is_published']) && $_POST['is_published'] == '0') ? 0 : 1;
    $video_url = $conn->real_escape_string($_POST['video_url']);
    $image_url = '';
    
    // Handle image upload if provided
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
            } else {
                $error = "Gagal mengunggah gambar.";
            }
        } else {
            $error = "Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.";
        }
    } else {
        // If no image uploaded but it's a video, try to get YT thumbnail
        if ($type == 'video' && !empty($video_url)) {
            $yt_id = getYoutubeId($video_url);
            if ($yt_id) {
                $image_url = "https://img.youtube.com/vi/{$yt_id}/maxresdefault.jpg";
            }
        } else {
            $error = "Gambar sampul wajib diisi untuk foto.";
        }
    }
    
    if (empty($error)) {
        if ($type == 'photo') {
            $video_url = ''; // clear if photo
        }
        
        $sql = "INSERT INTO gallery (title, type, is_published, video_url, image_url) VALUES ('$title', '$type', $is_published, '$video_url', '$image_url')";
        if ($conn->query($sql) === TRUE) {
            $success = "Galeri berhasil ditambahkan!";
            echo "<script>setTimeout(function(){ window.location.href='gallery'; }, 1500);</script>";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Tambah Galeri</h2>
        <a href="gallery" class="btn" style="background: #ccc; color: #333;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
    
    <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    
    <form action="create_gallery" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tipe Galeri</label>
            <select name="type" id="typeSelect" class="form-control" required>
                <option value="photo">Foto (Gambar Biasa)</option>
                <option value="video">Video (Tautan Youtube)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Judul Kegiatan / Keterangan</label>
            <input type="text" name="title" class="form-control" required placeholder="Contoh: Kunjungan Studi Banding 2026">
        </div>
        
        <div class="form-group" id="videoUrlGroup" style="display: none;">
            <label>Tautan (Link) Youtube</label>
            <input type="url" name="video_url" class="form-control" placeholder="Contoh: https://www.youtube.com/watch?v=XXXXXX">
            <p style="font-size: 12px; color: #777; margin-top: 5px;">Thumbnail otomatis diambil dari Youtube jika Anda tidak mengunggah foto sampul di bawah.</p>
        </div>
        
        <div class="form-group">
            <label>Status Publikasi</label>
            <select name="is_published" class="form-control" required>
                <option value="1">Terbit (Tampilkan di Beranda)</option>
                <option value="0">Draft (Sembunyikan Sementara)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label id="imageLabel">Unggah Foto</label>
            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput" style="padding: 10px;">
            <p style="font-size: 12px; color: #777; margin-top: 5px;" id="imageHelp">Format disarankan: JPG, PNG, atau WebP. Maksimal 2MB.</p>
        </div>
        
        <div id="imagePreview" style="margin-bottom: 20px; display: none;">
            <label>Preview Gambar:</label>
            <div style="margin-top: 10px;">
                <img id="preview" src="#" alt="Preview" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
        </div>
        
        <button type="submit" class="btn"><i class="fa-solid fa-cloud-arrow-up"></i> Simpan Galeri</button>
    </form>
</div>

<script>
    const typeSelect = document.getElementById('typeSelect');
    const videoUrlGroup = document.getElementById('videoUrlGroup');
    const imageInput = document.getElementById('imageInput');
    const imageLabel = document.getElementById('imageLabel');
    const imageHelp = document.getElementById('imageHelp');
    
    function toggleFields() {
        if (typeSelect.value === 'video') {
            videoUrlGroup.style.display = 'block';
            imageLabel.innerHTML = 'Foto Sampul Kustom (Opsional)';
            imageHelp.innerHTML = 'Kosongkan jika ingin menggunakan thumbnail otomatis dari Youtube.';
            imageInput.required = false;
        } else {
            videoUrlGroup.style.display = 'none';
            imageLabel.innerHTML = 'Unggah Foto';
            imageHelp.innerHTML = 'Format disarankan: JPG, PNG, atau WebP. Maksimal 2MB.';
            imageInput.required = true;
        }
    }
    
    typeSelect.addEventListener('change', toggleFields);
    toggleFields(); // run on load

    document.getElementById('imageInput').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        var previewContainer = document.getElementById('imagePreview');
        
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
