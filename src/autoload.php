<?php

$lib = __DIR__;
$map = [
    // arr-utils
    'Toolkit\ArrUtil\\'        => $lib . '/arr-utils/src',
    'Toolkit\ArrUtilTest\\'    => $lib . '/arr-utils/test',
    // cli-utils
    'Toolkit\Cli\\'            => $lib . '/cli-utils/src',
    'Toolkit\CliTest\\'        => $lib . '/cli-utils/test',
    // di
    'Toolkit\DI\\'             => $lib . '/di/src',
    'Toolkit\DITest\\'         => $lib . '/di/test',
    // collection
    'Toolkit\CollectionTest\\' => $lib . '/collection/test',
    'Toolkit\Collection\\'     => $lib . '/collection/src',
    // file parse
    'Toolkit\File\Parse\\'     => $lib . '/file-parse/src',
    'Toolkit\File\ParseTest\\' => $lib . '/file-parse/test',
    // file utils
    'Toolkit\File\\'           => $lib . '/file-utils/src',
    'Toolkit\FileTest\\'       => $lib . '/file-utils/test',
    // obj-utils
    'Toolkit\ObjUtil\\'        => $lib . '/obj-utils/src',
    'Toolkit\ObjUtilTest\\'    => $lib . '/obj-utils/test',
    // php utils
    'Toolkit\PhpUtil\\'        => $lib . '/php-utils/src',
    'Toolkit\PhpUtilTest\\'    => $lib . '/php-utils/test',
    // str utils
    'Toolkit\StrUtil\\'        => $lib . '/str-utils/src',
    'Toolkit\StrUtilTest\\'    => $lib . '/str-utils/test',
    // sys utils
    'Toolkit\Sys\\'            => $lib . '/sys-utils/src',
    'Toolkit\SysTest\\'        => $lib . '/sys-utils/test',
];

spl_autoload_register(function ($class) use ($map) {
    foreach ($map as $np => $dir) {
        if (0 === strpos($class, $np)) {
            $path = str_replace('\\', '/', substr($class, strlen($np)));
            $file = $dir . "/example/{$path}.php";

            if ($file && is_file($file)) {
                include $file;
                return;
            }
        }
    }
});
