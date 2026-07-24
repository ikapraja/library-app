<?php
$files = glob('*.php');
foreach ($files as $file) {
    if (in_array($file, ['db.php', 'setup_db.php'])) continue;
    $content = file_get_contents($file);
    $new_content = str_replace('.php', '.php', $content);
    if ($content !== $new_content) {
        file_put_contents($file, $new_content);
        echo "Updated links in $file\n";
    }
}
echo "Done replacing links.\n";
?>
