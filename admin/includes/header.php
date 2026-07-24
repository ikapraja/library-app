<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Perpustakaan PKTJ</title>
    <link rel="icon" href="/library-app/LOGO PERPUS PKTJ.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --primary-bg: #1a365d;
            --secondary-bg: #2d4a77;
            --text-light: #f4f7f6;
            --text-dark: #333;
            --accent: #f6a821;
        }
        body { font-family: 'Outfit', sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar Styles */
        .sidebar { width: 250px; background-color: var(--primary-bg); color: var(--text-light); display: flex; flex-direction: column; height: 100%; transition: all 0.3s; }
        .sidebar-header { padding: 20px; background-color: var(--secondary-bg); text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header img { max-width: 60px; margin-bottom: 10px; }
        .sidebar-header h3 { margin: 0; font-size: 16px; color: var(--accent); }
        .sidebar-header p { margin: 5px 0 0; font-size: 12px; opacity: 0.8; }
        
        .sidebar-menu { flex: 1; overflow-y: auto; padding: 15px 0; }
        .menu-label { padding: 10px 20px; font-size: 11px; text-transform: uppercase; color: #8a9bb5; font-weight: 700; letter-spacing: 1px; }
        .sidebar-menu ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 20px; color: #d1d9e6; text-decoration: none; transition: 0.2s; font-size: 14px; }
        .sidebar-menu li a i { width: 25px; font-size: 16px; margin-right: 10px; text-align: center; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background-color: var(--secondary-bg); color: #fff; border-left: 4px solid var(--accent); }
        
        /* Main Content Styles */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .top-navbar { background: #fff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); z-index: 10; }
        .top-navbar .user-info { display: flex; align-items: center; gap: 10px; font-weight: 600; color: var(--primary-bg); }
        .top-navbar .btn-logout { background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; }
        
        .content-area { padding: 30px; overflow-y: auto; flex: 1; }
        .card { background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        
        /* General Elements */
        h2 { color: var(--primary-bg); margin-top: 0; margin-bottom: 20px; font-weight: 700; }
        .btn { display: inline-block; padding: 10px 15px; background: var(--primary-bg); color: white; text-decoration: none; border-radius: 4px; font-weight: 600; border: none; cursor: pointer; font-family: 'Outfit', sans-serif; }
        .btn:hover { background: var(--accent); color: var(--primary-bg); }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; color: white; }
        .btn-warning { background: #f1c40f; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background-color: #f8f9fa; color: #555; font-weight: 600; }
        table tbody tr:hover { background-color: #f9f9f9; }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #444; font-weight: 600; }
        .form-control { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-family: 'Outfit', sans-serif; font-size: 14px; }
        .form-control:focus { outline: none; border-color: var(--primary-bg); box-shadow: 0 0 0 2px rgba(26,54,93,0.1); }
        textarea.form-control { height: 150px; resize: vertical; }
        
        .alert { padding: 12px 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Hide TinyMCE API Key Notification */
        .tox-notifications-container { display: none !important; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../LOGO PERPUS PKTJ.png" alt="Logo Perpustakaan PKTJ">
            <h3>ADMIN PERPUSTAKAAN</h3>
            <p>Politeknik Keselamatan Transportasi Jalan</p>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-label">Main Navigation</div>
            <ul>
                <li><a href="index" class="<?php echo $current_page == 'index.php' || $current_page == 'create.php' ? 'active' : ''; ?>"><i class="fa-solid fa-newspaper"></i> Berita</a></li>
                <li><a href="pages" class="<?php echo in_array($current_page, ['pages.php', 'edit_page.php', 'create_page.php']) ? 'active' : ''; ?>"><i class="fa-regular fa-file-lines"></i> Halaman</a></li>
                <li><a href="chatbot" class="<?php echo $current_page == 'chatbot.php' ? 'active' : ''; ?>"><i class="fa-solid fa-robot"></i> FAQ & Chatbot (Pustabots)</a></li>
                <li><a href="gallery" class="<?php echo $current_page == 'gallery.php' ? 'active' : ''; ?>"><i class="fa-solid fa-images"></i> Galeri Album</a></li>
                <li><a href="facilities" class="<?php echo in_array($current_page, ['facilities.php', 'edit_facility.php', 'create_facility.php']) ? 'active' : ''; ?>"><i class="fa-solid fa-building"></i> Fasilitas</a></li>
                <li><a href="staff" class="<?php echo in_array($current_page, ['staff.php', 'edit_staff.php', 'create_staff.php']) ? 'active' : ''; ?>"><i class="fa-solid fa-user-tie"></i> Tim Pustakawan</a></li>
                <li><a href="org_chart" class="<?php echo $current_page == 'org_chart.php' ? 'active' : ''; ?>"><i class="fa-solid fa-sitemap"></i> Bagan Struktur</a></li>
                <li><a href="statistics" class="<?php echo $current_page == 'statistics.php' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-line"></i> Statistik Pengunjung</a></li>
            </ul>
            
            <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin'): ?>
            <div class="menu-label" style="margin-top: 15px;">Konfigurasi Aplikasi</div>
            <ul>
                <li><a href="users" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>"><i class="fa-solid fa-users-gear fa-fw"></i> Pengguna</a></li>
                <li><a href="menus" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menus.php' ? 'active' : ''; ?>"><i class="fa-solid fa-sitemap fa-fw"></i> Menu Navigasi</a></li>
                <li><a href="settings" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-gear fa-fw"></i> Pengaturan Sistem</a></li>
            </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div style="color: #888; font-size: 14px;">
                <i class="fa-regular fa-clock"></i> <span id="live-clock"><?php echo date('d M Y, H:i'); ?></span>
                <script>
                    function updateClock() {
                        const now = new Date();
                        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        const day = String(now.getDate()).padStart(2, '0');
                        const month = months[now.getMonth()];
                        const year = now.getFullYear();
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        document.getElementById('live-clock').textContent = `${day} ${month} ${year}, ${hours}:${minutes}`;
                    }
                    setInterval(updateClock, 1000);
                </script>
            </div>
            <div class="user-info">
                <i class="fa-solid fa-circle-user" style="font-size: 24px; color: var(--primary-bg);"></i>
                <span>Halo, <?php echo htmlspecialchars($admin_name); ?></span>
                <a href="change_password" title="Ubah Password" style="color: var(--primary-bg); margin-left: 10px; font-size: 18px; transition: color 0.3s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--primary-bg)'"><i class="fa-solid fa-key"></i></a>
                <a href="logout" class="btn-logout" style="margin-left: 15px;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
