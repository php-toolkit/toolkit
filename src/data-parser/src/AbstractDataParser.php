<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/4/30 0030
 * Time: 12:47
 */

namespace Toolkit\DataParser;

/**
 * Class AbstractDataParser
 *
 * @package Toolkit\DataParser
 * @author  inhere <in.798@qq.com>
 */
abstract class AbstractDataParser implements DataParserInterface
{
    /**
     * @var array
     */
    protected $encodeOpts;

    /**
     * @var array
     */
    protected $decodeOpts;

    /**
     * JsonParser constructor.
     *
     * @param array $encodeOpts
     * @param array $decodeOpts
     */
    public function __construct(array $encodeOpts = [], array $decodeOpts = [])
    {
        $this->encodeOpts = $encodeOpts;
        $this->decodeOpts = $decodeOpts;
    }

    /**
     * @return array
     */
    public function getEncodeOpts(): array
    {
        return $this->encodeOpts;
    }

    /**
     * @return array
     */
    public function getDecodeOpts(): array
    {
        return $this->decodeOpts;
    }
}
