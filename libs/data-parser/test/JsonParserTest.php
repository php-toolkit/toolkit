<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/3/28 0028
 * Time: 19:25
 */

namespace Toolkit\DataParserTest;

use PHPUnit\Framework\TestCase;
use Toolkit\DataParser\JsonParser;

/**
 * Class JsonParserTest
 * @covers \Toolkit\DataParser\JsonParser
 */
class JsonParserTest extends TestCase
{
    public function testDecode()
    {
        $str = '{"name": "value"}';

        $parser = new JsonParser();
        $ret = $parser->decode($str);

        $this->assertInternalType('array', $ret);
        $this->assertArrayHasKey('name', $ret);
    }

    public function testEncode()
    {
        $data = [
            'name' => 'value',
        ];

        $parser = new JsonParser();
        $ret = $parser->encode($data);

        $this->assertInternalType('string', $ret);
        $this->assertJson($ret);
    }
}
