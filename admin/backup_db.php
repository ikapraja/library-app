<?php
session_start();
require_once '../db.php';

// Cek autentikasi dan otorisasi (Hanya Superadmin)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'superadmin') {
    die("Akses ditolak. Anda tidak memiliki izin untuk mengunduh backup.");
}

// Set header untuk memicu download file .sql
$filename = "backup_perpustakaan_" . date("Y-m-d_H-i-s") . ".sql";
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$sql_dump = "-- Backup Database Perpustakaan PKTJ\n";
$sql_dump .= "-- Waktu: " . date("Y-m-d H:i:s") . "\n\n";

foreach ($tables as $table) {
    $result = $conn->query("SELECT * FROM $table");
    $num_fields = $result->field_count;
    
    $sql_dump .= "DROP TABLE IF EXISTS $table;\n";
    $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
    $sql_dump .= $row2[1] . ";\n\n";
    
    for ($i = 0; $i < $num_fields; $i++) {
        while ($row = $result->fetch_row()) {
            $sql_dump .= "INSERT INTO $table VALUES(";
            for ($j = 0; $j < $num_fields; $j++) {
                $row[$j] = addslashes($row[$j]);
                $row[$j] = str_replace("\n", "\\n", $row[$j]);
                if (isset($row[$j])) {
                    $sql_dump .= '"' . $row[$j] . '"';
                } else {
                    $sql_dump .= '""';
                }
                if ($j < ($num_fields - 1)) {
                    $sql_dump .= ',';
                }
            }
            $sql_dump .= ");\n";
        }
    }
    $sql_dump .= "\n\n";
}

echo $sql_dump;
exit;
?>
