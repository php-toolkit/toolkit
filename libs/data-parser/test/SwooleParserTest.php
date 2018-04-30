<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/4/30 0030
 * Time: 12:55
 */

namespace Toolkit\DataParserTest;

use Toolkit\DataParser\SwooleParser;
use PHPUnit\Framework\TestCase;
use Swoole\Serialize;

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

        $this->assertInternalType('string', $ret);
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

        $this->assertInternalType('array', $ret);
        $this->assertArrayHasKey('name', $ret);
    }

}
