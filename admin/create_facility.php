<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $order_num = (int)$_POST['order_num'];
    
    $image_url = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
            } else {
                $error = "Gagal mengunggah foto.";
            }
        } else {
            $error = "Format foto tidak didukung.";
        }
    } else {
        // If no photo uploaded, use a placeholder
        $image_url = 'https://via.placeholder.com/400x250/e0e7ff/3f51b5?text=Foto+Menyusul';
    }
    
    if (empty($error)) {
        $sql = "INSERT INTO facilities (name, location, description, image_url, order_num) VALUES ('$name', '$location', '$description', '$image_url', $order_num)";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Fasilitas berhasil ditambahkan!";
            echo "<script>window.location.href='facilities';</script>";
            exit;
        } else {
            $error = "Gagal menambah data: " . $conn->error;
        }
    }
}
?>

<div class="card">
    <h2><i class="fa-solid fa-plus"></i> Tambah Fasilitas</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="create_facility" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Fasilitas</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Lokasi (Misal: Kampus Perintis / Kampus Margadana - Lantai 1)</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Deskripsi Singkat</label>
            <textarea name="description" class="form-control" style="height: 100px;" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Nomor Urut Tampil</label>
            <input type="number" name="order_num" class="form-control" value="0">
        </div>
        
        <div class="form-group">
            <label>Foto Ruangan (Opsional, disarankan rasio lanskap / mendatar)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn">SIMPAN</button>
            <a href="facilities" class="btn btn-outline" style="margin-left: 10px;">BATAL</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
