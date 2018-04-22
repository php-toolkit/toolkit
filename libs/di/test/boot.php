<?php
/**
 * phpunit
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    $file = null;

    if (0 === strpos($class,'MyLib\DI\Example\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('MyLib\DI\Example\\')));
        $file = dirname(__DIR__) . "/example/{$path}.php";
    } elseif (0 === strpos($class,'MyLib\DI\Test\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('MyLib\DI\Test\\')));
        $file = __DIR__ . "/{$path}.php";
    } elseif (0 === strpos($class,'MyLib\DI\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('MyLib\DI\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});

if (file_exists(dirname(__DIR__) . '/../../autoload.php')) {
    require dirname(__DIR__) . '/../../autoload.php';
}