<?php
/**
 * phpunit
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');

$files = [
    dirname(__DIR__) . '/vendor/autoload.php',
    dirname(__DIR__, 2) . '/autoload.php',
    dirname(__DIR__, 6) . '/vendor/autoload.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        require $file;
    }
}
