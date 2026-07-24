<?php
require_once '../db.php';
include 'includes/header.php';

$error = '';
$success = '';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit;
}

$admin_id = intval($_SESSION['admin_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi password tidak cocok.";
    } else {
        // Cek password lama
        $sql = "SELECT password FROM users WHERE id = $admin_id";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($old_password, $user['password'])) {
                // Update ke password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $admin_id";
                if ($conn->query($update_sql)) {
                    $success = "Password berhasil diubah!";
                } else {
                    $error = "Gagal mengubah password: " . $conn->error;
                }
            } else {
                $error = "Password lama yang Anda masukkan salah.";
            }
        } else {
            $error = "Pengguna tidak ditemukan.";
        }
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fa-solid fa-key"></i> Ubah Password</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="change_password">
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block;">Password Lama</label>
            <input type="password" name="old_password" class="form-control" placeholder="Masukkan password lama Anda" required style="padding: 12px;">
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block;">Password Baru</label>
            <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" required style="padding: 12px;">
        </div>

        <div class="form-group" style="margin-bottom: 25px;">
            <label style="font-weight: 600; color: #444; margin-bottom: 10px; display: block;">Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Ketik ulang password baru" minlength="6" required style="padding: 12px;">
        </div>
        
        <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 1.1rem; background: var(--primary-bg);"><i class="fa-solid fa-save"></i> SIMPAN PASSWORD BARU</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
