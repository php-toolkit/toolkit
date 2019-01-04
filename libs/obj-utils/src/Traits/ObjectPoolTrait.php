<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-19
 * Time: 17:02
 */

namespace Toolkit\ObjUtil\Traits;

/**
 * Class ObjectPoolTrait
 * @package Toolkit\ObjUtil\Traits
 */
trait ObjectPoolTrait
{
    /**
     * @var \SplStack[] [class => \SplStack]
     */
    private static $pool = [];

    /**
     * @param string $class
     * @return mixed
     */
    public static function get(string $class)
    {
        $stack = self::getStack($class);

        if (!$stack->isEmpty()) {
            return $stack->shift();
        }

        return new $class;
    }

    /**
     * @param \stdClass|string $object
     */
    public static function put($object)
    {
        if (\is_string($object)) {
            $object = new $object;
        }

        self::getStack($object)->push($object);
    }

    /**
     * @param string   $class
     * @param \Closure $handler
     * @return mixed
     */
    public static function use($class, \Closure $handler)
    {
        $obj = self::get($class);

        $ret = $handler($obj);

        self::put($obj);

        return $ret;
    }

    /**
     * @param string|\stdClass $class
     * @return \SplStack
     */
    public static function getStack($class): \SplStack
    {
        $class = \is_string($class) ? $class : \get_class($class);

        if (!isset(self::$pool[$class])) {
            self::$pool[$class] = new \SplStack();
        }

        return self::$pool[$class];
    }

    /**
     * @param null $class
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function count($class = null): int
    {
        if ($class) {
            if (!isset(self::$pool[$class])) {
                throw new \InvalidArgumentException("The object is never created of the class: $class");
            }

            return self::$pool[$class]->count();
        }

        return \count(self::$pool);
    }

    /**
     * @param null $class
     * @throws \InvalidArgumentException
     */
    public static function destroy($class = null)
    {
        if ($class) {
            if (!isset(self::$pool[$class])) {
                throw new \InvalidArgumentException("The object is never created of the class: $class");
            }

            unset(self::$pool[$class]);
        } else {
            self::$pool = [];
        }
    }
}
