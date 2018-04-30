<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/18
 * Time: 下午7:31
 */

namespace Toolkit\ObjUtil\Traits;

use Toolkit\ObjUtil\Obj;

/**
 * Trait SingletonTrait
 * @package Toolkit\ObjUtil\Traits
 */
trait SingletonTrait
{
    /**
     * @return mixed
     */
    public static function own()
    {
        return Obj::singleton(static::class);
    }

    private function __clone()
    {
    }
}
