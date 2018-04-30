<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-28
 * Time: 9:20
 */

namespace Toolkit\Traits;

/**
 * Class NameAliasStaticTrait
 * @package Toolkit\Traits
 * @property array $aliases path alias array
 */
trait NameAliasStaticTrait
{
    // protected static $aliases = [];

    /**
     * set/get name alias
     * @param array|string $name
     * @param string|null $alias
     * @return bool|string
     */
    public static function alias($name, $alias = null)
    {
        // get real name for $id
        if (null === $alias) {
            return self::resolveAlias($name);
        }

        foreach ((array)$alias as $aliasName) {
            if (!isset(self::$aliases[$aliasName])) {
                self::$aliases[$aliasName] = $name;
            }
        }

        return true;
    }

    /**
     * @param $alias
     * @return mixed
     */
    public static function resolveAlias($alias)
    {
        return self::$aliases[$alias] ?? $alias;
    }

    /**
     * @param $alias
     * @return bool
     */
    public static function hasAlias($alias)
    {
        return isset(self::$aliases[$alias]);
    }

    /**
     * @return array
     */
    public static function getAliases()
    {
        return self::$aliases;
    }
}
