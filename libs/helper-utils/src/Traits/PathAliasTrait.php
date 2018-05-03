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
 */
trait PathAliasTrait
{
    /**
     * @var array
     */
    protected static $aliases = [];

    /**
     * get real value by alias
     * @param string $alias
     * @return string|mixed
     * @throws \InvalidArgumentException
     */
    public static function alias(string $alias)
    {
        // Not an alias
        if (!$alias || $alias[0] !== '@') {
            return $alias;
        }

        $sep = '/';
        $other = '';
        $alias = \str_replace('\\', $sep, $alias);

        if (\strpos($alias, $sep)) {
            // have other partial. e.g: @project/temp/logs
            list($alias, $other) = \explode($sep, $alias, 2);
        }

        if (!isset(self::$aliases[$alias])) {
            throw new \InvalidArgumentException("The alias name '$alias' is not registered!");
        }

        return self::$aliases[$alias] . ($other ? $sep . $other : '');
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasAlias(string $name): bool
    {
        return isset(self::$aliases[$name]);
    }

    /**
     * @param string $alias
     * @param $value
     * @throws \InvalidArgumentException
     */
    public static function setAlias(string $alias, $value)
    {
        self::$aliases[$alias] = self::alias($value);
    }

    /**
     * @param array $aliases
     * @throws \InvalidArgumentException
     */
    public static function setAliases(array $aliases)
    {
        foreach ($aliases as $alias => $realPath) {
            // 1th char must is '@'
            if (!$alias || $alias[0] !== '@') {
                continue;
            }

            self::$aliases[$alias] = self::alias($realPath);
        }
    }

    /**
     * @return array
     */
    public static function getAliases(): array
    {
        return self::$aliases;
    }
}
