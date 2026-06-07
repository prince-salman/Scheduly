<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("Error: vendor/autoload.php not found. Vercel failed to run composer install.");
}

require __DIR__ . '/../public/index.php';
