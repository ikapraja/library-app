<?php
// Redirect cerdas ke halaman admin login
// Bekerja otomatis baik di root domain (library.pktj.ac.id) maupun subfolder localhost (localhost/library-app)
$base_dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
header("Location: " . $base_dir . "/admin/login", true, 302);
exit;
