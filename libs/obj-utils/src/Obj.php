<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/7
 * Time: 下午9:39
 */

namespace Toolkit\ObjUtil;

use ArrayAccess;
use Toolkit\ObjUtil\Traits\ObjectPoolTrait;

/**
 * Class Obj
 *  alias of the ObjectHelper
 *
 * @package Toolkit\ObjUtil
 */
class Obj extends ObjectHelper
{
    use ObjectPoolTrait;

    /**
     * @var array
     */
    private static $singletons = [];

    /**
     * @param string $class
     *
     * @return mixed
     */
    public static function singleton(string $class)
    {
        if (!isset(self::$singletons[$class])) {
            self::$singletons[$class] = new $class;
        }

        return self::$singletons[$class];
    }

    /**
     * @param string $class
     *
     * @return mixed
     */
    public static function factory(string $class)
    {
        if (!isset(self::$singletons[$class])) {
            self::$singletons[$class] = new $class;
        }

        return clone self::$singletons[$class];
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public static function isArrayable($object): bool
    {
        return $object instanceof ArrayAccess || method_exists($object, 'toArray');
    }
}
