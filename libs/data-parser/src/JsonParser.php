<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:07
 */

namespace Toolkit\DataParser;

/**
 * Class JsonParser
 * @package Toolkit\DataParser
 */
class JsonParser implements ParserInterface
{
    /**
     * @var bool
     */
    protected $assoc = true;

    /**
     * JsonParser constructor.
     * @param null $assoc
     */
    public function __construct($assoc = null)
    {
        if ($assoc !== null) {
            $this->setAssoc($assoc);
        }
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        return \json_decode($data, $this->assoc);
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        return \json_encode($data);
    }

    /**
     * @return bool
     */
    public function isAssoc(): bool
    {
        return $this->assoc;
    }

    /**
     * @param bool $assoc
     */
    public function setAssoc($assoc)
    {
        $this->assoc = (bool)$assoc;
    }
}
