<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/3/28 0028
 * Time: 19:25
 */

namespace Toolkit\DataParserTest;

use PHPUnit\Framework\TestCase;
use Toolkit\DataParser\PhpParser;

/**
 * Class PhpParserTest
 *
 * @covers \Toolkit\DataParser\PhpParser
 */
class PhpParserTest extends TestCase
{
    public function testDecode(): void
    {
        $str = 'a:1:{s:4:"name";s:5:"value";}';

        $parser = new PhpParser();
        $ret    = $parser->decode($str);

        $this->assertIsArray($ret);
        $this->assertArrayHasKey('name', $ret);
    }

    public function testEncode(): void
    {
        $data = [
            'name' => 'value',
        ];

        $parser = new PhpParser();
        $ret    = $parser->encode($data);

        $this->assertIsString($ret);
        $this->assertStringStartsWith('a:1:{', $ret);
    }
}
