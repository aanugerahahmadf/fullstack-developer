<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Disable all output buffering for instant response delivery
if (ob_get_level()) {
    ob_end_clean();
}
ini_set('output_buffering', '0');
ini_set('zlib.output_compression', '0');
ini_set('implicit_flush', '1');

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
