<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo "<script>window.location.href='facilities';</script>";
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $order_num = (int)$_POST['order_num'];
    
    $photo_clause = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/' . $new_filename;
                $photo_clause = ", image_url = '$image_url'";
            } else {
                $error = "Gagal mengunggah foto.";
            }
        } else {
            $error = "Format foto tidak didukung.";
        }
    }
    
    if (empty($error)) {
        $sql = "UPDATE facilities SET name = '$name', location = '$location', description = '$description', order_num = $order_num $photo_clause WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Fasilitas berhasil diperbarui!";
            echo "<script>window.location.href='facilities';</script>";
            exit;
        } else {
            $error = "Gagal memperbarui data: " . $conn->error;
        }
    }
}

$sql = "SELECT * FROM facilities WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<script>window.location.href='facilities';</script>";
    exit;
}
$fac = $result->fetch_assoc();
?>

<div class="card">
    <h2><i class="fa-solid fa-pen-to-square"></i> Edit Fasilitas</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_facility" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $fac['id']; ?>">
        
        <div class="form-group">
            <label>Nama Fasilitas</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($fac['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($fac['location']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Deskripsi Singkat</label>
            <textarea name="description" class="form-control" style="height: 100px;" required><?php echo htmlspecialchars($fac['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Nomor Urut Tampil</label>
            <input type="number" name="order_num" class="form-control" value="<?php echo $fac['order_num']; ?>">
        </div>
        
        <div class="form-group">
            <label>Foto Ruangan Baru (Biarkan kosong jika tidak ingin mengubah)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <?php if ($fac['image_url']): ?>
                <div style="margin-top: 10px;">
                    <img src="<?php echo htmlspecialchars(strpos($fac['image_url'], 'http') === 0 ? $fac['image_url'] : '../' . $fac['image_url']); ?>" alt="Current Photo" style="width: 200px; border-radius: 8px;">
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn">SIMPAN PERUBAHAN</button>
            <a href="facilities" class="btn btn-outline" style="margin-left: 10px;">BATAL</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
