<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-28
 * Time: 9:20
 */

namespace Toolkit\Traits;

/**
 * Class PathAliasTrait
 * @package Toolkit\Traits
 * @property array $aliases path alias array
 */
trait PathAliasTrait
{
    // protected static $aliases = [];

    /**
     * set/get path alias
     * @param array|string $path
     * @param string|null $value
     * @return bool|string
     */
    public static function alias($path, $value = null)
    {
        // get path by alias
        if (\is_string($path) && !$value) {
            // don't use alias
            if ($path[0] !== '@') {
                return $path;
            }

            $sep = '/';
            $path = str_replace(['/', '\\'], $sep, $path);

            // only a alias. e.g. @project
            if (!strpos($path, $sep)) {
                return self::$aliases[$path] ?? $path;
            }

            // have other partial. e.g: @project/temp/logs
            $realPath = $path;
            list($alias, $other) = explode($sep, $path, 2);

            if (isset(self::$aliases[$alias])) {
                $realPath = self::$aliases[$alias] . $sep . $other;
            }

            return $realPath;
        }

        if ($path && $value && \is_string($path) && \is_string($value)) {
            $path = [$path => $value];
        }

        // custom set path's alias. e.g: Slim::alias([ 'alias' => 'path' ]);
        if (\is_array($path)) {
            /**
             * @var string $alias
             * @var string $realPath
             */
            foreach ($path as $alias => $realPath) {
                // 1th char must is '@'
                if ($alias[0] !== '@') {
                    continue;
                }

                self::$aliases[$alias] = self::alias($realPath);
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public static function getAliases()
    {
        return self::$aliases;
    }
}
