<?php
$dir = __DIR__;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $contents = file_get_contents($file);
        $updated = str_replace(
            'require_once "db_connection.php";',
            'require_once "includes/db_connection.php";',
            $contents
        );

        if ($contents !== $updated) {
            file_put_contents($file, $updated);
            echo "✅ Updated: " . $file->getFilename() . "\n";
        }
    }
}
?>
