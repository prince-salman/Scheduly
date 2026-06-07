<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("Error: vendor/autoload.php not found. Vercel failed to run composer install.");
}

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>FATAL ERROR</h1>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}
