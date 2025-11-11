<?php

// Direct debug - try to load public/index.php and see what fails
echo "=== DEBUG INFO ===\n";
echo "Current working directory: " . getcwd() . "\n";
echo "__FILE__: " . __FILE__ . "\n";
echo "__DIR__: " . __DIR__ . "\n";

$index_file = __DIR__ . '/public/index.php';
echo "Looking for: $index_file\n";
echo "File exists: " . (file_exists($index_file) ? 'YES' : 'NO') . "\n";

// Try to include public/index.php directly
try {
    require_once $index_file;
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
