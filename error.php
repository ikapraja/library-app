<?php
$code = isset($_GET['code']) ? (int)$_GET['code'] : 404;

switch ($code) {
    case 403:
        http_response_code(403);
        $title = "403 - Akses Ditolak";
        $headline = "Akses Dibatasi (Forbidden)";
        $message = "Mohon maaf, Anda tidak memiliki izin untuk mengakses direktori atau berkas ini secara langsung demi alasan keamanan.";
        $icon = "fa-shield-halved";
        $color = "#e74c3c";
        break;
    case 400:
        http_response_code(400);
        $title = "400 - Permintaan Tidak Valid";
        $headline = "Permintaan Tidak Dapat Diproses";
        $message = "Server tidak dapat memproses permintaan dari peramban Anda. Silakan muat ulang halaman atau kembali ke beranda.";
        $icon = "fa-circle-exclamation";
        $color = "#e67e22";
        break;
    case 500:
        http_response_code(500);
        $title = "500 - Kendala Server";
        $headline = "Terjadi Kendala pada Server";
        $message = "Sistem kami sedang mengalami kendala teknis sementara. Silakan coba beberapa saat lagi atau hubungi pengelola perpustakaan.";
        $icon = "fa-triangle-exclamation";
        $color = "#ef4444";
        break;
    case 404:
    default:
        http_response_code(404);
        $code = 404;
        $title = "404 - Halaman Tidak Ditemukan";
        $headline = "Halaman Tidak Ditemukan";
        $message = "Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau alamat URL yang Anda masukkan salah.";
        $icon = "fa-book-open-reader";
        $color = "#1E3A8A";
        break;
}

$page_title = $title;
require_once 'db.php';
include 'includes/frontend_header.php';
?>

<div style="background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%); min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
    <div style="max-width: 650px; width: 100%; text-align: center; background: #ffffff; padding: 50px 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.05);">
        
        <div style="width: 90px; height: 90px; margin: 0 auto 25px; border-radius: 50%; background: rgba(30, 58, 138, 0.08); display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid <?php echo $icon; ?>" style="font-size: 42px; color: <?php echo $color; ?>;"></i>
        </div>
        
        <div style="display: inline-block; padding: 4px 14px; background: <?php echo $color; ?>; color: #fff; font-weight: 700; border-radius: 20px; font-size: 0.85rem; letter-spacing: 1px; margin-bottom: 15px;">
            ERROR <?php echo $code; ?>
        </div>

        <h1 style="font-size: 2.2rem; font-weight: 800; color: #1E3A8A; margin: 0 0 15px 0;">
            <?php echo $headline; ?>
        </h1>

        <p style="font-size: 1.05rem; color: #64748b; line-height: 1.7; margin: 0 auto 30px; max-width: 500px;">
            <?php echo $message; ?>
        </p>

        <!-- Search Box -->
        <div style="margin-bottom: 30px;">
            <form action="berita" method="GET" style="display: flex; gap: 8px; max-width: 420px; margin: 0 auto;">
                <input type="text" name="q" placeholder="Cari artikel atau informasi..." style="flex: 1; padding: 12px 18px; border: 1px solid #cbd5e1; border-radius: 30px; font-family: inherit; font-size: 0.95rem; outline: none;">
                <button type="submit" style="padding: 12px 22px; background: #f6a821; color: #1a365d; font-weight: 700; border: none; border-radius: 30px; cursor: pointer; transition: 0.2s;">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </form>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="./" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #1E3A8A; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; transition: 0.2s; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);">
                <i class="fa-solid fa-house"></i> Kembali ke Beranda
            </a>
            <a href="javascript:history.back()" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #f1f5f9; color: #334155; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; transition: 0.2s;">
                <i class="fa-solid fa-arrow-left"></i> Halaman Sebelumnya
            </a>
        </div>

    </div>
</div>

<?php include 'includes/frontend_footer.php'; ?>
