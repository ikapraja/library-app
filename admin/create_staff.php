<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $role = $conn->real_escape_string($_POST['role']);
    $email = $conn->real_escape_string($_POST['email']);
    $order_num = (int)$_POST['order_num'];
    
    $photo_url = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $photo_url = 'uploads/' . $new_filename;
            } else {
                $error = "Gagal mengunggah foto.";
            }
        } else {
            $error = "Format foto tidak didukung.";
        }
    }
    
    if (empty($error)) {
        $sql = "INSERT INTO staff_members (name, role, email, photo_url, order_num) VALUES ('$name', '$role', '$email', '$photo_url', $order_num)";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Anggota berhasil ditambahkan!";
            echo "<script>window.location.href='staff';</script>";
            exit;
        } else {
            $error = "Gagal menambah data: " . $conn->error;
        }
    }
}
?>

<div class="card">
    <h2><i class="fa-solid fa-plus"></i> Tambah Anggota Tim</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="create_staff" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Lengkap (beserta gelar)</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Jabatan</label>
            <input type="text" name="role" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control">
        </div>
        
        <div class="form-group">
            <label>Nomor Urut Tampil</label>
            <input type="number" name="order_num" class="form-control" value="0">
        </div>
        
        <div class="form-group">
            <label>Foto Profil (Opsional, disarankan rasio 1:1)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn">SIMPAN</button>
            <a href="staff" class="btn btn-outline" style="margin-left: 10px;">BATAL</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
