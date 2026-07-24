<?php
$index = file_get_contents('index.php');
preg_match('/<!-- Footer -->.*?<\/html>/s', $index, $matches);
$footer = $matches[0];

$bebas = file_get_contents('bebas-pustaka.php');
$bebas .= "\n" . $footer;
file_put_contents('bebas-pustaka.php', $bebas);
echo "Footer appended to bebas-pustaka.php\n";
?>
