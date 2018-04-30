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
class SomeClass
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
