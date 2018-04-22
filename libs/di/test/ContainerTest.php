<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-01-30
 * Time: 11:46
 */

namespace MyLib\DI\Test;

use MyLib\DI\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class ContainerTest
 * @package MyLib\DI\Test
 * @covers \MyLib\DI\Container
 */
class ContainerTest extends TestCase
{
    public function testCreate()
    {
        $di = new Container([
            's1' => SomeClass::class,
            's2' => [
                'class' => MakeByStatic::class . '::factory',
                [
                    [
                        'name' => 'test2'
                    ]
                ]
            ],
            's3' => [
                'class' => MakeByMethod::class . '->factory',
                [
                    [
                        'name' => 'test2'
                    ]
                ]
            ]
        ]);

        $this->assertCount(3, $di->count());
        $this->assertTrue($di->has('s1'));
    }
}