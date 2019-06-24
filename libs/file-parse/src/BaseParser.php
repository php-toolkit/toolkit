<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/17
 * Time: 上午11:43
 */

namespace Toolkit\File\Parse;

use InvalidArgumentException;
use function dirname;
use function file_get_contents;
use function is_file;

/**
 * Class BaseParser
 *
 * @package Toolkit\File\Parse
 */
abstract class BaseParser
{
    public const EXTEND_KEY    = 'extend';
    public const IMPORT_KEY    = 'import';
    public const REFERENCE_KEY = 'reference';

    /**
     * parse data
     *
     * @param string   $string      Waiting for the parse data
     * @param bool     $enhancement 启用增强功能，支持通过关键字 继承、导入、参考
     * @param callable $pathHandler When the second param is true, this param is valid.
     * @param string   $fileDir     When the second param is true, this param is valid.
     *
     * @return array
     */
    abstract protected static function doParse(
        $string,
        $enhancement = false,
        callable $pathHandler = null,
        string $fileDir = ''
    ): array;

    /**
     * @param               $string
     * @param bool          $enhancement
     * @param callable|null $pathHandler
     * @param string        $fileDir
     *
     * @return array
     */
    public static function parse($string, $enhancement = false, callable $pathHandler = null, $fileDir = ''): array
    {
        if (is_file($string)) {
            return self::parseFile($string, $enhancement, $pathHandler, $fileDir);
        }

        return static::doParse($string, $enhancement, $pathHandler, $fileDir);
    }

    /**
     * @param               $file
     * @param bool          $enhancement
     * @param callable|null $pathHandler
     * @param string        $fileDir
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public static function parseFile($file, $enhancement = false, callable $pathHandler = null, $fileDir = ''): array
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException("Target file [$file] not exists");
        }

        $fileDir = $fileDir ?: dirname($file);
        $data    = file_get_contents($file);

        return static::doParse($data, $enhancement, $pathHandler, $fileDir);
    }

    /**
     * @param               $string
     * @param bool          $enhancement
     * @param callable|null $pathHandler
     * @param string        $fileDir
     *
     * @return array
     */
    public static function parseString(
        $string,
        $enhancement = false,
        callable $pathHandler = null,
        $fileDir = ''
    ): array {
        return static::doParse($string, $enhancement, $pathHandler, $fileDir);
    }
}
