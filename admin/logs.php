<?php
require_once 'includes/header.php';

// Path file error_log di cPanel/server (cek di root public_html dan di admin)
$log_files = [
    'Root (public_html/error_log)' => __DIR__ . '/../error_log',
    'Admin (admin/error_log)' => __DIR__ . '/error_log',
];

$active_log_path = null;
$active_log_name = '';

foreach ($log_files as $name => $path) {
    if (file_exists($path)) {
        $active_log_path = $path;
        $active_log_name = $name;
        break;
    }
}

// Fallback jika belum ada file error_log
if (!$active_log_path) {
    $active_log_path = __DIR__ . '/../error_log';
    $active_log_name = 'Root (public_html/error_log)';
}

$alert = '';

// Handle Clear Log Action
if (isset($_POST['clear_log'])) {
    if (file_exists($active_log_path)) {
        file_put_contents($active_log_path, '');
        $alert = '<div class="alert alert-success"><i class="fa-solid fa-check"></i> Catatan log error berhasil dikosongkan!</div>';
    } else {
        $alert = '<div class="alert alert-success"><i class="fa-solid fa-check"></i> Tidak ada file log yang perlu dibersihkan.</div>';
    }
}

// Read and parse log lines
$parsed_logs = [];
if (file_exists($active_log_path) && filesize($active_log_path) > 0) {
    $lines = file($active_log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines) {
        $lines = array_reverse($lines); // Tampilkan yang terbaru di atas
        // Batasi 200 log terakhir agar tetap ringan
        $lines = array_slice($lines, 0, 200);

        foreach ($lines as $line) {
            $type = 'INFO';
            $badge_bg = '#64748b';
            
            if (stripos($line, 'fatal') !== false || stripos($line, 'error') !== false) {
                $type = 'FATAL ERROR';
                $badge_bg = '#ef4444';
            } elseif (stripos($line, 'warning') !== false) {
                $type = 'WARNING';
                $badge_bg = '#f59e0b';
            } elseif (stripos($line, 'notice') !== false) {
                $type = 'NOTICE';
                $badge_bg = '#3b82f6';
            }

            // Extract timestamp if available [dd-Mmm-yyyy hh:mm:ss timezone]
            $time = '-';
            $message = $line;
            if (preg_match('/^\[(.*?)\] (.*)$/', $line, $matches)) {
                $time = $matches[1];
                $message = $matches[2];
            }

            $parsed_logs[] = [
                'type' => $type,
                'badge_bg' => $badge_bg,
                'time' => $time,
                'message' => $message,
                'raw' => $line
            ];
        }
    }
}
?>

<div class="content-area">
    <?php echo $alert; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="margin: 0; color: #1a365d; font-size: 20px;">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent); margin-right: 8px;"></i> Catatan Log Sistem (Error Log)
            </h2>
            <p style="margin: 5px 0 0; color: #64748b; font-size: 13px;">
                Lokasi file: <code><?php echo htmlspecialchars($active_log_name); ?></code> 
                (Ukuran: <?php echo file_exists($active_log_path) ? round(filesize($active_log_path)/1024, 2) . ' KB' : '0 KB'; ?>)
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="logs" class="btn" style="background: #3b82f6; color: white; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-rotate"></i> Muat Ulang
            </a>
            <?php if (!empty($parsed_logs)): ?>
            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh riwayat catatan error ini?');" style="margin: 0;">
                <button type="submit" name="clear_log" style="background: #ef4444; color: white; border: none; padding: 9px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-trash-can"></i> Bersihkan Log
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Error Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 4px solid #3b82f6;">
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Catatan</div>
            <div style="font-size: 24px; font-weight: 800; color: #1a365d; margin-top: 5px;"><?php echo count($parsed_logs); ?></div>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 4px solid #ef4444;">
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Fatal Error</div>
            <div style="font-size: 24px; font-weight: 800; color: #ef4444; margin-top: 5px;">
                <?php echo count(array_filter($parsed_logs, fn($l) => $l['type'] === 'FATAL ERROR')); ?>
            </div>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 4px solid #f59e0b;">
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Peringatan (Warning)</div>
            <div style="font-size: 24px; font-weight: 800; color: #f59e0b; margin-top: 5px;">
                <?php echo count(array_filter($parsed_logs, fn($l) => $l['type'] === 'WARNING')); ?>
            </div>
        </div>
    </div>

    <!-- Log List -->
    <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); overflow: hidden;">
        <?php if (empty($parsed_logs)): ?>
            <div style="padding: 60px 20px; text-align: center;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #dcfce7; color: #16a34a; font-size: 32px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 style="margin: 0; color: #1e293b; font-size: 18px;">Sistem Sangat Sehat!</h3>
                <p style="margin: 8px 0 0; color: #64748b; font-size: 14px;">Tidak ada error atau kendala sistem yang tercatat pada file log saat ini.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569;">
                            <th style="padding: 14px 20px; width: 130px;">Tipe</th>
                            <th style="padding: 14px 20px; width: 180px;">Waktu</th>
                            <th style="padding: 14px 20px;">Pesan & Lokasi Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parsed_logs as $log): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 20px; vertical-align: top;">
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; color: white; font-weight: 700; font-size: 11px; letter-spacing: 0.5px; background: <?php echo $log['badge_bg']; ?>;">
                                    <?php echo $log['type']; ?>
                                </span>
                            </td>
                            <td style="padding: 14px 20px; vertical-align: top; color: #64748b; font-family: monospace; font-size: 12px; white-space: nowrap;">
                                <?php echo htmlspecialchars($log['time']); ?>
                            </td>
                            <td style="padding: 14px 20px; vertical-align: top; color: #334155; font-family: monospace; font-size: 12px; line-height: 1.6; word-break: break-word;">
                                <?php echo htmlspecialchars($log['message']); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
