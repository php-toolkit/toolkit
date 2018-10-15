<?php
/**
 * phpunit
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    $file = null;

    if (0 === strpos($class, 'Toolkit\PhpUtil\Example\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\PhpUtil\Example\\')));
        $file = dirname(__DIR__) . "/example/{$path}.php";
    } elseif (0 === strpos($class, 'Toolkit\PhpUtil\Test\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\PhpUtil\Test\\')));
        $file = __DIR__ . "/{$path}.php";
    } elseif (0 === strpos($class, 'Toolkit\PhpUtil\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\PhpUtil\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});
