<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/8/31
 * Time: 下午2:25
 */

namespace Toolkit\ObjUtil\Traits;

use Toolkit\ObjUtil\Obj;

/**
 * Class StdObjectTrait
 * @package Toolkit\ObjUtil\Traits
 */
trait StdObjectTrait
{
    /**
     * get called class full name
     * @return string
     */
    final public static function fullName(): string
    {
        return static::class;
    }

    /**
     * get called class namespace
     * @param null|string $fullName
     * @return string
     */
    final public static function spaceName(string $fullName = null): string
    {
        $fullName = $fullName ?: self::fullName();
        $fullName = \str_replace('\\', '/', $fullName);

        return \strpos($fullName, '/') ? \dirname($fullName) : null;
    }

    /**
     * get called class name
     * @param null|string $fullName
     * @return string
     */
    final public static function className(string $fullName = null): string
    {
        $fullName = $fullName ?: self::fullName();
        $fullName = \str_replace('\\', '/', $fullName);

        return \basename($fullName);
    }

    /**
     * StdObject constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::init($this, $config);

        $this->init();
    }

    /**
     * init
     */
    protected function init()
    {
        // init something ...
    }

    /**
     * @param string $method
     * @param $args
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function __call($method, array $args)
    {
        // if (method_exists($this, $method) && $this->isAllowCall($method) ) {
        //     return call_user_func_array( array($this, $method), (array) $args);
        // }

        throw new \InvalidArgumentException('Called a Unknown method! ' . \get_class($this) . "->{$method}()");
    }

    /**
     * @param string $method
     * @param $args
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function __callStatic(string $method, $args)
    {
        if (method_exists(self::class, $method)) {
            return \call_user_func_array([self::class, $method], (array)$args);
        }

        throw new \InvalidArgumentException('Called a Unknown static method! [ ' . self::class . "::{$method}()]");
    }
}
