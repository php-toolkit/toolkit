<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/4/30 0030
 * Time: 12:55
 */

namespace Toolkit\DataParserTest;

use PHPUnit\Framework\TestCase;
use Swoole\Serialize;
use Toolkit\DataParser\SwooleParser;

/**
 * Class SwooleParserTest
 * @covers \Toolkit\DataParser\SwooleParser
 */
class SwooleParserTest extends TestCase
{
    public function testEncode()
    {
        if (!\class_exists(Serialize::class, false)) {
            return;
        }

        $data = [
            'name' => 'value',
        ];

        $parser = new SwooleParser();
        $ret = $parser->encode($data);

        $this->assertIsString($ret);
    }

    public function testDecode()
    {
        if (!\class_exists(Serialize::class, false)) {
            return;
        }

        $data = [
            'name' => 'value',
        ];

        $parser = new SwooleParser();
        $str = $parser->encode($data);
        $ret = $parser->decode($str);

        $this->assertIsArray($ret);
        $this->assertArrayHasKey('name', $ret);
    }

}

