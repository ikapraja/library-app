<?php
// Cegah browser dan proxy menyimpan cache redirect lama
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect cerdas ke halaman admin login
// Bekerja otomatis baik di root domain (library.pktj.ac.id) maupun subfolder localhost (localhost/library-app)
$base_dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
header("Location: " . $base_dir . "/admin/login", true, 302);
exit;
