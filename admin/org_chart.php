<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['chart']) && $_FILES['chart']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['chart']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'org_chart_' . time() . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['chart']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
                $sql = "UPDATE pages SET content = '$image_url' WHERE slug = 'org-chart-img'";
                if ($conn->query($sql) === TRUE) {
                    $success = "Bagan Struktur Organisasi berhasil diperbarui!";
                } else {
                    $error = "Gagal menyimpan ke database: " . $conn->error;
                }
            } else {
                $error = "Gagal mengunggah foto.";
            }
        } else {
            $error = "Format foto tidak didukung.";
        }
    } else {
        $error = "Harap pilih file gambar terlebih dahulu.";
    }
}

$sql = "SELECT content FROM pages WHERE slug = 'org-chart-img'";
$result = $conn->query($sql);
$chart = $result->fetch_assoc();
$current_chart = $chart ? $chart['content'] : 'Struktur Organisasi.png';
?>

<div class="card">
    <h2><i class="fa-solid fa-sitemap"></i> Bagan Struktur Organisasi</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <form method="POST" action="org_chart" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Upload Bagan Struktur Baru</label>
                    <p style="font-size: 0.9em; color: #666; margin-bottom: 15px;">Pilih gambar resolusi tinggi (disarankan format PNG dengan latar transparan atau putih) untuk menampilkan struktur utama organisasi.</p>
                    <input type="file" name="chart" class="form-control" accept="image/*" required>
                </div>
                
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn"><i class="fa-solid fa-upload"></i> UPLOAD & SIMPAN</button>
                </div>
            </form>
        </div>
        
        <div style="flex: 1; min-width: 300px;">
            <label style="font-weight: 600; color: #444; display: block; margin-bottom: 10px;">Gambar Saat Ini</label>
            <div style="border: 1px dashed #ccc; padding: 10px; border-radius: 8px; text-align: center; background: #f9f9f9;">
                <img src="<?php echo htmlspecialchars(strpos($current_chart, 'http') === 0 ? $current_chart : '../' . $current_chart); ?>" alt="Org Chart" style="max-width: 100%; max-height: 400px; border-radius: 4px;">
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
