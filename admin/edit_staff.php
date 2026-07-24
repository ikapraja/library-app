<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo "<script>window.location.href='staff';</script>";
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $role = $conn->real_escape_string($_POST['role']);
    $email = $conn->real_escape_string($_POST['email']);
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
                $photo_url = 'uploads/' . $new_filename;
                $photo_clause = ", photo_url = '$photo_url'";
            } else {
                $error = "Gagal mengunggah foto.";
            }
        } else {
            $error = "Format foto tidak didukung.";
        }
    }
    
    if (empty($error)) {
        $sql = "UPDATE staff_members SET name = '$name', role = '$role', email = '$email', order_num = $order_num $photo_clause WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Anggota berhasil diperbarui!";
            echo "<script>window.location.href='staff';</script>";
            exit;
        } else {
            $error = "Gagal memperbarui data: " . $conn->error;
        }
    }
}

$sql = "SELECT * FROM staff_members WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<script>window.location.href='staff';</script>";
    exit;
}
$staff = $result->fetch_assoc();
?>

<div class="card">
    <h2><i class="fa-solid fa-pen-to-square"></i> Edit Anggota Tim</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_staff" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $staff['id']; ?>">
        
        <div class="form-group">
            <label>Nama Lengkap (beserta gelar)</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($staff['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Jabatan</label>
            <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($staff['role']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($staff['email']); ?>">
        </div>
        
        <div class="form-group">
            <label>Nomor Urut Tampil</label>
            <input type="number" name="order_num" class="form-control" value="<?php echo $staff['order_num']; ?>">
        </div>
        
        <div class="form-group">
            <label>Foto Profil Baru (Biarkan kosong jika tidak ingin mengubah)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <?php if ($staff['photo_url']): ?>
                <div style="margin-top: 10px;">
                    <img src="../<?php echo htmlspecialchars($staff['photo_url']); ?>" alt="Current Photo" style="width: 100px; border-radius: 8px;">
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn">SIMPAN PERUBAHAN</button>
            <a href="staff" class="btn btn-outline" style="margin-left: 10px;">BATAL</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
