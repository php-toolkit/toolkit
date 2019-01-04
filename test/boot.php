<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/4/30 0030
 * Time: 14:48
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(dirname(__DIR__, 3) . '/autoload.php')) {
    require dirname(__DIR__, 3) . '/autoload.php';
}

$baseDir = dirname(__DIR__);
$map = [
    'Toolkit\DITest\\' => $baseDir . '/libs/di/test',
];

spl_autoload_register(function ($class) use ($map) {
    foreach ($map as $np => $dir) {
        if (0 === strpos($class, $np)) {
            $path = str_replace('\\', '/', substr($class, strlen($np)));
            $file = $dir . "/{$path}.php";

            if (is_file($file)) {
                __include_file($file);
            }
        }
    }
});

function __include_file($file)
{
    include $file;
}
