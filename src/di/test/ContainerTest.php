<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-01-30
 * Time: 11:46
 */

namespace Toolkit\DITest;

use PHPUnit\Framework\TestCase;
use Toolkit\DI\Container;

/**
 * Class ContainerTest
 *
 * @package Toolkit\DITest
 * @covers  \Toolkit\DI\Container
 */
class ContainerTest extends TestCase
{
    public function testCreate(): void
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

        $this->assertCount(3, $di);
        $this->assertTrue($di->has('s1'));
    }
}
