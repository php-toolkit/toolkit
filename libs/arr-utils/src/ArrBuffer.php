<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-24
 * Time: 9:17
 */

namespace Toolkit\ArrUtil;

/**
 * Class ArrBuffer
 *
 * @package Toolkit\ArrUtil
 */
final class ArrBuffer
{
    /** @var string[] */
    private $body = [];

    /** @var string */
    private $delimiter = ''; // '/' ':'

    /**
     * constructor.
     *
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        if ($content) {
            $this->body[] = $content;
        }
    }

    /**
     * @param string $content
     */
    public function write(string $content): void
    {
        $this->body[] = $content;
    }

    /**
     * @param string $content
     */
    public function append(string $content): void
    {
        $this->write($content);
    }

    /**
     * @param string $content
     */
    public function prepend(string $content): void
    {
        array_unshift($this->body, $content);
    }

    /**
     * clear
     */
    public function clear(): void
    {
        $this->body = [];
    }

    /**
     * @return string[]
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @param string[] $body
     */
    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return implode($this->delimiter, $this->body);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }
}
