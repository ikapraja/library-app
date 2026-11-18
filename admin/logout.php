<?php
session_start();
session_destroy();
if (isset($_COOKIE['is_admin_device'])) {
    setcookie('is_admin_device', '', time() - 3600, '/');
}
header("Location: login");
exit;
?>
