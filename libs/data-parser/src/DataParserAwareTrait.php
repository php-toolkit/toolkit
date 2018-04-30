<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:37
 */

namespace Toolkit\DataParser;

/**
 * Class DataParserAwareTrait
 * @package Toolkit\DataParser
 * @author inhere <in.798@qq.com>
 */
trait DataParserAwareTrait
{
    /**
     * @var DataParserInterface
     */
    private $parser;

    /**
     * @return DataParserInterface
     */
    public function getParser(): DataParserInterface
    {
        if (!$this->parser) {
            $this->parser = new PhpParser();
        }

        return $this->parser;
    }

    /**
     * @param DataParserInterface $parser
     * @return DataParserAwareTrait
     */
    public function setParser(DataParserInterface $parser): self
    {
        $this->parser = $parser;

        return $this;
    }
}
