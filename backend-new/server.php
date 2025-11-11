<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files directly from public folder
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri) && !is_dir(__DIR__ . '/public' . $uri)) {
    return false;
}

// Debug - log request
$logfile = __DIR__ . '/server_debug.log';
file_put_contents($logfile,
    date('Y-m-d H:i:s') . " - Request: {$uri}\n" .
    "  __DIR__: " . __DIR__ . "\n" .
    "  public/index.php exists: " . (file_exists(__DIR__ . '/public/index.php') ? 'YES' : 'NO') . "\n",
    FILE_APPEND
);

// Bootstrap Laravel from public/index.php
require __DIR__ . '/public/index.php';
