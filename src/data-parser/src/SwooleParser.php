<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/3/29 0029
 * Time: 19:22
 */

namespace Toolkit\DataParser;

use RuntimeException;
use Swoole\Serialize;
use function class_exists;

/**
 * Class SwooleParser
 *
 * @package Toolkit\DataParser
 * @author  inhere <in.798@qq.com>
 * @link    https://wiki.swoole.com/wiki/page/p-serialize.html
 */
class SwooleParser extends AbstractDataParser
{
    /**
     * class constructor.
     *
     * @param array $encodeOpts
     *
     * @throws RuntimeException
     */
    public function __construct(array $encodeOpts = [])
    {
        if (!class_exists(Serialize::class, false)) {
            throw new RuntimeException("The php extension 'swoole_serialize' is required!");
        }

        parent::__construct($encodeOpts);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function encode($data): string
    {
        return (string)Serialize::pack($data, ...$this->encodeOpts);
    }

    /**
     * @param string $data
     *
     * @return mixed
     */
    public function decode(string $data)
    {
        return Serialize::unpack($data, ...$this->decodeOpts);
    }
}
