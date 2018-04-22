<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-01-30
 * Time: 11:53
 */

namespace MyLib\DI\Test;

/**
 * Class SomeClass
 * @package MyLib\DI\Test
 */
class MakeByStatic
{
    public static function factory(array $options = [])
    {
        return new SomeClass($options);
    }
}