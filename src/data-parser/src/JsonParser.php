<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:07
 */

namespace Toolkit\DataParser;

use function json_decode;
use function json_encode;

/**
 * Class JsonParser
 *
 * @package Toolkit\DataParser
 * @author  inhere <in.798@qq.com>
 */
class JsonParser extends AbstractDataParser
{
    /**
     * class constructor.
     *
     * @param array $encodeOpts
     * @param array $decodeOpts
     */
    public function __construct(array $encodeOpts = [], array $decodeOpts = [])
    {
        parent::__construct($encodeOpts, $decodeOpts ?: [true]);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function encode($data): string
    {
        return json_encode($data, ...$this->encodeOpts);
    }

    /**
     * @param string $data
     *
     * @return mixed
     */
    public function decode(string $data)
    {
        return json_decode($data, ...$this->decodeOpts);
    }
}
