<?php
/**
 * phpunit
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    $file = null;

    if (0 === strpos($class, 'Toolkit\DI\Example\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\DI\Example\\')));
        $file = dirname(__DIR__) . "/example/{$path}.php";
        <<<<
        <<< HEAD
    } elseif (0 === strpos($class,'Toolkit\DITest\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\DITest\\')));
=======
    } elseif (0 === strpos($class,'Toolkit\DI\Test\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\DI\Test\\')));
>>>>>>> 42776df603e867a87654e7edfa6649337cb975f6
        $file = __DIR__ . "/{$path}.php";
    } elseif (0 === strpos($class,'Toolkit\DI\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\DI\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});

if (file_exists(dirname(__DIR__) . '/../../autoload.php')) {
    require dirname(__DIR__) . '/../../autoload.php';
}
