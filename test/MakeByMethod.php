<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-01-30
 * Time: 11:53
 */

namespace Toolkit\DI\Test;

/**
 * Class SomeClass
 * @package Toolkit\DI\Test
 */
class MakeByMethod
{
    public function factory(array $options = []): SomeClass
    {
        return new SomeClass($options);
    }
}
