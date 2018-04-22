<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/3/29 0029
 * Time: 19:22
 */

namespace Toolkit\DataParser;

use Swoole\Serialize;

/**
 * Class SwooleParser
 * @author inhere <in.798@qq.com>
 * @link https://wiki.swoole.com/wiki/page/p-serialize.html
 */
class SwooleParser implements ParserInterface
{
    /**
     * class constructor.
     * @throws \RuntimeException
     */
    public function __construct()
    {
        if (!\class_exists(Serialize::class, false)) {
            throw new \RuntimeException("The php extension 'swoole_serialize' is required!");
        }
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        return (string)Serialize::pack($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        return Serialize::unpack($data);
    }
}
