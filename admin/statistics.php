<?php
require_once '../db.php';
require_once 'includes/header.php';

// --- SUMMARY CARDS DATA (Hari Ini, Minggu Ini, Tahun Ini, Sepanjang Waktu) ---
$today = date('Y-m-d');
$day_of_week = (int)date('N'); // 1 (Senin) - 7 (Minggu)
$this_week_start = date('Y-m-d', strtotime('-' . ($day_of_week - 1) . ' days'));
$this_year_start = date('Y-01-01');

$stat_today = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date = '$today'")->fetch_assoc()['total'] ?? 0;
$stat_week  = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$this_week_start'")->fetch_assoc()['total'] ?? 0;
$stat_year  = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$this_year_start'")->fetch_assoc()['total'] ?? 0;
$stat_all   = $conn->query("SELECT SUM(hits) as total FROM visitor_stats")->fetch_assoc()['total'] ?? 0;


// --- CHART DATA BASED ON RANGE ---
$range = $_GET['range'] ?? '30d';
$start_input = $_GET['start_date'] ?? '';
$end_input = $_GET['end_date'] ?? '';

$labels = [];
$data = [];

// Minimum year for dropdown
$curr_year = (int)date('Y');
$dropdown_min_year = $curr_year - 3; // Hanya 4 tahun terakhir (sekarang dan 3 tahun ke belakang)

if (!empty($start_input) && !empty($end_input)) {
    $range = 'custom';
    $start_date = $start_input;
    $end_date = $end_input;
    
    $current = strtotime($start_date);
    $last = strtotime($end_date);
    
    // Prevent too many days (limit to 365 days max for daily view)
    $diff_days = round(($last - $current) / (60 * 60 * 24));
    if ($diff_days > 365) {
        // Group by month if range > 1 year
        while($current <= $last) {
            $m_key = date('Y-m', $current);
            if(!isset($labels[$m_key])) {
                $labels[$m_key] = date('M Y', $current);
                $data[$m_key] = 0;
            }
            $current = strtotime("+1 month", $current);
        }
        $sql = "SELECT DATE_FORMAT(visit_date, '%Y-%m') as key_val, SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$start_date' AND visit_date <= '$end_date' GROUP BY key_val ORDER BY key_val ASC";
    } else {
        // Group by day
        while($current <= $last) {
            $d = date('Y-m-d', $current);
            $labels[$d] = date('d M Y', $current);
            $data[$d] = 0;
            $current = strtotime("+1 day", $current);
        }
        $sql = "SELECT visit_date as key_val, SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$start_date' AND visit_date <= '$end_date' GROUP BY key_val ORDER BY key_val ASC";
    }
    
    $result = $conn->query($sql);
    if($result) {
        while($row = $result->fetch_assoc()){
            $k = $row['key_val'];
            if(isset($data[$k])) $data[$k] = (int)$row['total'];
        }
    }
} else if ($range == '7d' || $range == '30d') {
    $days = ($range == '7d') ? 7 : 30;
    $start_date = date('Y-m-d', strtotime("-$days days"));
    for($i = $days-1; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $labels[$d] = date('d M', strtotime($d));
        $data[$d] = 0;
    }
    $sql = "SELECT visit_date, SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$start_date' GROUP BY visit_date ORDER BY visit_date ASC";
    $result = $conn->query($sql);
    if($result) {
        while($row = $result->fetch_assoc()){
            $d = $row['visit_date'];
            if(isset($data[$d])) $data[$d] = (int)$row['total'];
        }
    }
} else if ($range == '6m' || $range == '1y') {
    $months = ($range == '6m') ? 6 : 12;
    $start_date = date('Y-m-01', strtotime("-$months months"));
    for($i = $months-1; $i >= 0; $i--) {
        $m_key = date('Y-m', strtotime("-$i months"));
        $labels[$m_key] = date('M Y', strtotime("-$i months"));
        $data[$m_key] = 0;
    }
    $sql = "SELECT DATE_FORMAT(visit_date, '%Y-%m') as m_key, SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$start_date' GROUP BY m_key ORDER BY m_key ASC";
    $result = $conn->query($sql);
    if($result) {
        while($row = $result->fetch_assoc()){
            $m = $row['m_key'];
            if(isset($data[$m])) $data[$m] = (int)$row['total'];
        }
    }
} else if ($range == 'all') {
    $real_min_year = $conn->query("SELECT MIN(YEAR(visit_date)) as min_year FROM visitor_stats")->fetch_assoc()['min_year'] ?? $curr_year;
    if(!$real_min_year) $real_min_year = $curr_year;
    for($y = $real_min_year; $y <= $curr_year; $y++) {
        $labels[(string)$y] = (string)$y;
        $data[(string)$y] = 0;
    }
    $sql = "SELECT YEAR(visit_date) as y_key, SUM(hits) as total FROM visitor_stats GROUP BY y_key ORDER BY y_key ASC";
    $result = $conn->query($sql);
    if($result) {
        while($row = $result->fetch_assoc()){
            $y = $row['y_key'];
            if(isset($data[$y])) $data[$y] = (int)$row['total'];
        }
    }
} else if (is_numeric($range)) {
    $y = (int)$range;
    for($m = 1; $m <= 12; $m++) {
        $m_key = sprintf("%04d-%02d", $y, $m);
        $labels[$m_key] = date('M Y', strtotime("$m_key-01"));
        $data[$m_key] = 0;
    }
    $sql = "SELECT DATE_FORMAT(visit_date, '%Y-%m') as m_key, SUM(hits) as total FROM visitor_stats WHERE YEAR(visit_date) = $y GROUP BY m_key ORDER BY m_key ASC";
    $result = $conn->query($sql);
    if($result) {
        while($row = $result->fetch_assoc()){
            $m = $row['m_key'];
            if(isset($data[$m])) $data[$m] = (int)$row['total'];
        }
    }
}

// Convert arrays for Chart.js
$chart_labels_json = json_encode(array_values($labels));
$chart_data_json = json_encode(array_values($data));
?>

<div class="content-area">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2><i class="fa-solid fa-chart-line" style="color: var(--accent); margin-right: 10px;"></i> Statistik Pengunjung</h2>
    </div>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div class="card" style="margin-bottom: 0; border-bottom: 4px solid #3498db; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0; color: #777; font-size: 14px; font-weight: 600;">Hari Ini</p>
                <h3 style="margin: 5px 0 0; font-size: 28px; color: var(--primary-bg);"><?= number_format($stat_today, 0, ',', '.') ?></h3>
            </div>
            <i class="fa-solid fa-user-clock" style="font-size: 2.5rem; color: #3498db; opacity: 0.3;"></i>
        </div>
        
        <div class="card" style="margin-bottom: 0; border-bottom: 4px solid #e67e22; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0; color: #777; font-size: 14px; font-weight: 600;">Minggu Ini</p>
                <h3 style="margin: 5px 0 0; font-size: 28px; color: var(--primary-bg);"><?= number_format($stat_week, 0, ',', '.') ?></h3>
            </div>
            <i class="fa-solid fa-calendar-week" style="font-size: 2.5rem; color: #e67e22; opacity: 0.3;"></i>
        </div>
        
        <div class="card" style="margin-bottom: 0; border-bottom: 4px solid #2ecc71; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0; color: #777; font-size: 14px; font-weight: 600;">Tahun Ini</p>
                <h3 style="margin: 5px 0 0; font-size: 28px; color: var(--primary-bg);"><?= number_format($stat_year, 0, ',', '.') ?></h3>
            </div>
            <i class="fa-solid fa-calendar-check" style="font-size: 2.5rem; color: #2ecc71; opacity: 0.3;"></i>
        </div>

        <div class="card" style="margin-bottom: 0; border-bottom: 4px solid #9b59b6; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="margin: 0; color: #777; font-size: 14px; font-weight: 600;">Sepanjang Waktu</p>
                <h3 style="margin: 5px 0 0; font-size: 28px; color: var(--primary-bg);"><?= number_format($stat_all, 0, ',', '.') ?></h3>
            </div>
            <i class="fa-solid fa-earth-americas" style="font-size: 2.5rem; color: #9b59b6; opacity: 0.3;"></i>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <h3 style="margin: 0; color: var(--primary-bg);">Grafik Kunjungan</h3>
            <div style="display: flex; gap: 20px; align-items: center;">
                <form method="GET" action="statistics" style="display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 10px 15px; border-radius: 6px; border: 1px solid #ddd;">
                    <label style="font-size: 13px; color: #555; font-weight: 600;">Rentang Manual:</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($start_input) ?>" class="form-control" style="width: auto; padding: 4px 8px; font-size: 13px;" required>
                    <span style="color: #777;">-</span>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($end_input) ?>" class="form-control" style="width: auto; padding: 4px 8px; font-size: 13px;" required>
                    <button type="submit" class="btn" style="padding: 4px 12px; font-size: 13px;">Tampilkan</button>
                </form>
                
                <span style="color: #ccc; font-weight: bold;">ATAU</span>

                <form method="GET" action="statistics" style="display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 10px 15px; border-radius: 6px; border: 1px solid #ddd;">
                    <label for="range" style="font-size: 13px; color: #555; font-weight: 600;">Pilih Rentang:</label>
                    <select name="range" id="range" class="form-control" style="width: auto; padding: 4px 8px; font-size: 13px;" onchange="this.form.submit()">
                        <option value="7d" <?= $range == '7d' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="30d" <?= $range == '30d' ? 'selected' : '' ?>>1 Bulan Terakhir</option>
                        <option value="6m" <?= $range == '6m' ? 'selected' : '' ?>>6 Bulan Terakhir</option>
                        <option value="1y" <?= $range == '1y' ? 'selected' : '' ?>>1 Tahun Terakhir</option>
                        <option value="all" <?= $range == 'all' ? 'selected' : '' ?>>Sepanjang Waktu</option>
                        <?php 
                        for($y = $curr_year; $y >= $dropdown_min_year; $y--) {
                            $sel = ($range == (string)$y) ? 'selected' : '';
                            echo "<option value=\"$y\" $sel>Tahun $y</option>";
                        }
                        ?>
                    </select>
                </form>
            </div>
        </div>

        <div style="position: relative; height: 400px; width: 100%;">
            <canvas id="visitorChart"></canvas>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('visitorChart').getContext('2d');
    
    // Gradient fill for the chart
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(26, 54, 93, 0.5)'); // var(--primary-bg) with opacity
    gradient.addColorStop(1, 'rgba(26, 54, 93, 0.0)');

    const visitorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= $chart_labels_json ?>,
            datasets: [{
                label: 'Jumlah Pengunjung',
                data: <?= $chart_data_json ?>,
                borderColor: '#1a365d',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#f6a821', // var(--accent)
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Makes the line smooth/curvy
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Hide legend since there's only one dataset
                },
                tooltip: {
                    backgroundColor: 'rgba(26, 54, 93, 0.9)',
                    titleFont: { family: 'Outfit', size: 14 },
                    bodyFont: { family: 'Outfit', size: 14, weight: 'bold' },
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' Kunjungan';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0',
                        drawBorder: false
                    },
                    ticks: {
                        font: { family: 'Outfit' },
                        color: '#888'
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: { family: 'Outfit' },
                        color: '#888'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
