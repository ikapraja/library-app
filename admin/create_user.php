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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role'] ?? 'admin');
    
    // Check if username already exists
    $check_sql = "SELECT id FROM users WHERE username = '$username'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $error = "Username sudah digunakan, silakan pilih yang lain.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, name, email, role) VALUES ('$username', '$hashed_password', '$name', '$email', '$role')";
        if ($conn->query($sql) === TRUE) {
            $success = "Pengguna berhasil ditambahkan!";
            echo "<script>setTimeout(function(){ window.location.href='users'; }, 1500);</script>";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Tambah Pengguna Baru</h2>
        <a href="users" class="btn" style="background: #ccc; color: #333;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>
    
    <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    
    <form action="create_user" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control" required placeholder="Contoh: Bagus Ikapraja">
        </div>
        
        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" class="form-control" required placeholder="Contoh: bagusikapraja@pktj.ac.id">
        </div>
        
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Untuk login">
        </div>
        
        <div class="form-group">
            <label>Peran (Role)</label>
            <select name="role" class="form-control" required>
                <option value="admin">Admin (Hanya Kelola Konten)</option>
                <option value="superadmin">Superadmin (Akses Penuh)</option>
            </select>
        </div>
        
        <div class="form-group" style="position: relative;">
            <label>Password</label>
            <input type="password" name="password" id="passwordInput" class="form-control" required placeholder="Gunakan password yang kuat" style="padding-right: 40px;">
            <i class="fa-regular fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: #888;"></i>
        </div>
        
        <button type="submit" class="btn"><i class="fa-solid fa-user-plus"></i> Simpan Pengguna</button>
    </form>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#passwordInput');

    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>

<?php require_once 'includes/footer.php'; ?>
