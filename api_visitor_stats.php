<?php
header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'db.php';

$today = date('Y-m-d');
$day_of_week = (int)date('N'); // 1 (Senin) - 7 (Minggu)
$this_week_start = date('Y-m-d', strtotime('-' . ($day_of_week - 1) . ' days'));
$this_year_start = date('Y-01-01');

$stat_today = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date = '$today'")->fetch_assoc()['total'] ?? 0;
$stat_week  = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$this_week_start'")->fetch_assoc()['total'] ?? 0;
$stat_year  = $conn->query("SELECT SUM(hits) as total FROM visitor_stats WHERE visit_date >= '$this_year_start'")->fetch_assoc()['total'] ?? 0;
$stat_all   = $conn->query("SELECT SUM(hits) as total FROM visitor_stats")->fetch_assoc()['total'] ?? 0;

echo json_encode([
    'today' => number_format($stat_today, 0, ',', '.'),
    'week'  => number_format($stat_week, 0, ',', '.'),
    'year'  => number_format($stat_year, 0, ',', '.'),
    'all'   => number_format($stat_all, 0, ',', '.')
]);
exit;
