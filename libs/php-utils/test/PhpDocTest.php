<?php

namespace Toolkit\PhpUtilTest;

use PHPUnit\Framework\TestCase;
use Toolkit\PhpUtil\PhpDoc;

/**
 * Class PhpDocTest
 *
 * @package Toolkit\PhpUtilTest
 */
class PhpDocTest extends TestCase
{
    public function testGetTags(): void
    {
        $comment = <<<DOC
/**
 * Provide some commands to manage the HTTP Server
 *
 * @since 2.0
 *
 * @example
 *  {fullCmd}:start     Start the http server
 *  {fullCmd}:stop      Stop the http server
 */
DOC;
        $ret     = PhpDoc::getTags($comment);
        $this->assertCount(3, $ret);
        $this->assertArrayHasKey('since', $ret);
        $this->assertArrayHasKey('example', $ret);
        $this->assertArrayHasKey('description', $ret);

        $ret = PhpDoc::getTags($comment, ['allow' => ['example']]);
        $this->assertCount(2, $ret);
        $this->assertArrayNotHasKey('since', $ret);
        $this->assertArrayHasKey('example', $ret);
        $this->assertArrayHasKey('description', $ret);

        $ret = PhpDoc::getTags($comment, [
            'allow'   => ['example'],
            'default' => 'desc'
        ]);
        $this->assertCount(2, $ret);
        $this->assertArrayNotHasKey('since', $ret);
        $this->assertArrayHasKey('example', $ret);
        $this->assertArrayHasKey('desc', $ret);
        $this->assertArrayNotHasKey('description', $ret);
    }
}
