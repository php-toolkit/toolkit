<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/21 0021
 * Time: 21:40
 */

require dirname(__DIR__) . '/test/boot.php';

$mw = new \Toolkit\FileUtil\ModifyWatcher();
$ret = $mw
    // ->setIdFile(__DIR__ . '/tmp/dir.id')
    ->watch(dirname(__DIR__))
    ->isChanged();

// d41d8cd98f00b204e9800998ecf8427e
// current file:  ae4464472e898ba0bba8dc7302b157c0
var_dump($ret, $mw->getDirMd5(), $mw->getFileCounter());
