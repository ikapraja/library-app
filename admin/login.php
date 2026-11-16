<?php
session_start();
require_once '../db.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $sql = "SELECT id, username, password, name, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role'];
            header("Location: index");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Perpustakaan PKTJ</title>
    <link rel="icon" href="../LOGO PERPUS PKTJ.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background: linear-gradient(135deg, #1a365d 0%, #0d213b 100%); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; position: relative; }
        .login-wrapper { position: relative; z-index: 2; width: 100%; max-width: 400px; padding: 20px; }
        .login-box { background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); text-align: center; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px); }
        .login-logo { max-width: 80px; margin-bottom: 15px; }
        .login-box h2 { color: #1a365d; margin-top: 0; margin-bottom: 25px; font-weight: 700; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; color: #444; font-weight: 600; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: 'Outfit', sans-serif; transition: all 0.3s; background: #f9f9f9; }
        .form-group input:focus { border-color: #f6a821; outline: none; box-shadow: 0 0 0 3px rgba(246, 168, 33, 0.2); background: #fff; }
        .btn-login { background-color: #1a365d; color: #fff; padding: 14px 20px; border: none; border-radius: 6px; width: 100%; cursor: pointer; font-size: 16px; font-weight: 600; font-family: 'Outfit', sans-serif; transition: all 0.3s; margin-top: 10px; }
        .btn-login:hover { background-color: #f6a821; color: #1a365d; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(246, 168, 33, 0.3); }
        .error { color: #e74c3c; background: #fdf0ed; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fadbd8; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <img src="../LOGO PERPUS PKTJ.png" alt="Logo Perpustakaan" class="login-logo">
            <h2>Admin Dashboard</h2>
            <?php if($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
            <form action="login" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username">
                </div>
                <div class="form-group" style="position: relative;">
                    <label>Password</label>
                    <input type="password" name="password" id="passwordInput" required placeholder="Masukkan password" style="padding-right: 40px;">
                    <i class="fa-regular fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 42px; cursor: pointer; color: #888; transition: color 0.3s;" onmouseover="this.style.color='#1a365d'" onmouseout="this.style.color='#888'"></i>
                </div>
                <button type="submit" class="btn-login">Masuk Sistem</button>
            </form>
        </div>
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
</body>
</html>
