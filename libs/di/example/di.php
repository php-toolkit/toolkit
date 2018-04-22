<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-06-26
 * Time: 11:57
 */

use Toolkit\DI\Container;
use Inhere\Library\Utils\LiteLogger;

require dirname(__DIR__) . '/test/boot.php';

$di = new Container([
    'logger' => LiteLogger::make(['name' => 'test']),
    'logger2' => [
        'target' => LiteLogger::class . '::make',
        ['name' => 'test2']// first arg
    ]
]);

var_dump($di);

var_dump($di->get('logger2'));
