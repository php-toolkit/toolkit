<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->notName('routes.php')
    ->exclude(['test', 'example'])
    ->in([
        __DIR__ . '/libs/'
    ]);

$versions = GitVersionCollection::create(__DIR__);

return new Sami($iterator, [
    // 'theme'                => 'enhanced',
    // 'versions' => $versions,
    'title'                => 'Php Toolkit Classes Documentation',
    'build_dir'            => __DIR__ . '/classes-docs/%version%',
    'cache_dir'            => __DIR__ . '/caches/%version%',
    'default_opened_level' => 1,
    // 'store'                => new MyArrayStore,
]);

/**
 * usage: php sami.phar update --force sami.doc.inc
 */
