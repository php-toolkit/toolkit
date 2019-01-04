<?php

$lib = __DIR__;
$map = [
    // arr-utils
    'Toolkit\ArrUtil\\'         => $lib . '/arr-utils/src',
    'Toolkit\ArrUtil\Test\\'    => $lib . '/arr-utils/test',
    // cli-utils
    'Toolkit\Cli\\'             => $lib . '/cli-utils/src',
    'Toolkit\Cli\Test\\'        => $lib . '/cli-utils/test',
    // collection
    'Toolkit\Collection\Test\\' => $lib . '/collection/test',
    'Toolkit\Collection\\'      => $lib . '/collection/src',
    // file parse
    'Toolkit\File\Parse\\'      => $lib . '/file-parse/src',
    'Toolkit\File\Parse\Test\\' => $lib . '/file-parse/test',
    // file utils
    'Toolkit\File\\'            => $lib . '/file-utils/src',
    'Toolkit\File\Test\\'       => $lib . '/file-utils/test',
    // obj-utils
    'Toolkit\ObjUtil\\'         => $lib . '/obj-utils/src',
    'Toolkit\ObjUtil\Test\\'    => $lib . '/obj-utils/test',
    // php utils
    'Toolkit\PhpUtil\\'         => $lib . '/php-utils/src',
    'Toolkit\PhpUtil\Test\\'    => $lib . '/php-utils/test',
    // str utils
    'Toolkit\StrUtil\\'         => $lib . '/str-utils/src',
    'Toolkit\StrUtil\Test\\'    => $lib . '/str-utils/test',
    // sys utils
    'Toolkit\Sys\\'             => $lib . '/sys-utils/src',
    'Toolkit\Sys\Test\\'        => $lib . '/sys-utils/test',
];

spl_autoload_register(function ($class) use ($map) {
    $file = null;
    foreach ($map as $np => $dir) {
        if (0 === strpos($class, $np)) {
            $path = str_replace('\\', '/', substr($class, strlen($np)));
            $file = $dir . "/example/{$path}.php";

            if ($file && is_file($file)) {
                include_file_toolkit($file);
                return;
            }
        }
    }
});

function include_file_toolkit($file)
{
    include $file;
}
