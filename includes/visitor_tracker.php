<?php
// Cegah error jika dipanggil tanpa koneksi database
if (!isset($conn) || !($conn instanceof mysqli)) {
    return;
}

// 1. Pastikan session aktif untuk mendeteksi status login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cegah penghitungan jika pengguna adalah ADMIN (semua akun admin / NIP, baik via session atau cookie)
if (!empty($_SESSION['admin_logged_in']) || !empty($_COOKIE['is_admin_device'])) {
    return;
}

// 3. Cegah penghitungan jika sedang mengakses halaman di dalam folder admin
$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$request_uri = str_replace('\\', '/', $_SERVER['REQUEST_URI'] ?? '');
if (strpos($script_name, '/admin/') !== false || strpos($request_uri, '/admin/') !== false || basename($script_name) === 'login.php') {
    return;
}

// 4. Catat hit kunjungan untuk pengunjung umum (Hari Ini)
$today = date('Y-m-d');
$track_sql = "INSERT INTO visitor_stats (visit_date, hits) VALUES ('$today', 1) ON DUPLICATE KEY UPDATE hits = hits + 1";
$conn->query($track_sql);
