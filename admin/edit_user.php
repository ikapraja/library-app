<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}
if ($_SESSION['admin_role'] !== 'superadmin') {
    header("Location: index");
    exit;
}

require_once '../db.php';
require_once 'includes/header.php';

$error = '';
$success = '';
$user = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<script>alert('Pengguna tidak ditemukan.'); window.location.href='users';</script>";
        exit;
    }
} else {
    header("Location: users");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role'] ?? 'admin');
    
    // Check if username already exists for a different user
    $check_sql = "SELECT id FROM users WHERE username = '$username' AND id != $id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $error = "Username sudah digunakan oleh pengguna lain.";
    } else {
        // Build the update query based on whether a new password is provided
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = '$username', password = '$hashed_password', name = '$name', email = '$email', role = '$role' WHERE id = $id";
        } else {
            $sql = "UPDATE users SET username = '$username', name = '$name', email = '$email', role = '$role' WHERE id = $id";
        }
        
        if ($conn->query($sql) === TRUE) {
            // If the superadmin edited their own profile, update session variables
            if ($_SESSION['admin_id'] == $id) {
                $_SESSION['admin_name'] = $name;
                $_SESSION['admin_user'] = $username;
                $_SESSION['admin_role'] = $role;
            }
            
            $success = "Data pengguna berhasil diperbarui!";
            // refresh user data
            $user['name'] = $name;
            $user['email'] = $email;
            $user['username'] = $username;
            $user['role'] = $role;
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Edit Pengguna</h2>
        <a href="users" class="btn" style="background: #ccc; color: #333;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
    
    <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    
    <?php if($user): ?>
    <form action="edit_user?id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($user['name']); ?>">
        </div>
        
        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        
        <div class="form-group">
            <label>Peran (Role)</label>
            <select name="role" class="form-control" required>
                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin (Hanya Kelola Konten)</option>
                <option value="superadmin" <?php echo ($user['role'] == 'superadmin') ? 'selected' : ''; ?>>Superadmin (Akses Penuh)</option>
            </select>
        </div>
        
        <div class="form-group" style="position: relative;">
            <label>Password Baru <span style="font-weight: normal; font-size: 0.85em; color: #666;">(Kosongkan jika tidak ingin mengubah password)</span></label>
            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Masukkan password baru" style="padding-right: 40px;">
            <i class="fa-regular fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: #888;"></i>
        </div>
        
        <button type="submit" class="btn"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
    </form>
    <?php endif; ?>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#passwordInput');

    if(togglePassword && password) {
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>
