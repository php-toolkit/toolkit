<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-01-30
 * Time: 11:53
 */

namespace Toolkit\DITest;

/**
 * Class SomeClass
 * @package Toolkit\DITest
 */
class MakeByMethod
{
    public function factory(array $options = []): SomeClass
    {
        return new SomeClass($options);
    }
}
