<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 15-1-14
 * Name: File.php
 * Time: 10:35
 */

namespace Toolkit\File;

use Exception;
use SplFileObject;
use Throwable;
use Toolkit\File\Exception\FileNotFoundException;
use Toolkit\File\Exception\FileReadException;
use Toolkit\File\Exception\FileSystemException;
use Toolkit\File\Parser\IniParser;
use Toolkit\File\Parser\JsonParser;
use Toolkit\File\Parser\YmlParser;
use function array_slice;
use function assert;
use function class_exists;
use function count;
use function is_array;

/**
 * Class Read
 *
 * @package Toolkit\File
 */
trait ReadTrait
{
    /**
     * @param string $src 要解析的 文件 或 字符串内容。
     * @param string $format
     *
     * @return array|bool
     * @throws FileNotFoundException
     */
    public static function load($src, $format = self::FORMAT_PHP)
    {
        $src = trim($src);

        switch ($format) {
            case self::FORMAT_YML:
                $array = self::loadYml($src);
                break;

            case self::FORMAT_JSON:
                $array = self::loadJson($src);
                break;

            case self::FORMAT_INI:
                $array = self::loadIni($src);
                break;

            case self::FORMAT_PHP:
            default:
                $array = self::loadPhp($src);
                break;
        }

        return $array;
    }

    /**
     * load array data form file.
     *
     * @param string $file
     * @param bool   $throwError
     *
     * @return array
     * @throws FileNotFoundException
     */
    public static function loadPhp($file, $throwError = true): array
    {
        $ary = [];

        if (is_file($file)) {
            $ary = require $file;

            if (!is_array($ary)) {
                $ary = [];
            }
        } elseif ($throwError) {
            throw new FileNotFoundException("php file [$file] not exists.");
        }

        return $ary;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public static function loadJson($file): array
    {
        return JsonParser::parse($file);
    }

    /**
     * @param string $ini 要解析的 ini 文件名 或 字符串内容。
     *
     * @return array|bool
     */
    public static function loadIni($ini)
    {
        return IniParser::parse($ini);
    }

    /**
     * @param string $yml 要解析的 yml 文件名 或 字符串内容。
     *
     * @return array|bool
     */
    public static function loadYml($yml)
    {
        return YmlParser::parse($yml);
    }

    /**
     * @param           $file
     * @param bool|true $filter
     *
     * @return array|string
     * @throws FileNotFoundException
     * @throws FileReadException
     */
    public static function readAllLine($file, $filter = true)
    {
        $contents = self::getContents($file);

        if (!$contents) {
            return [];
        }

        $array = explode(PHP_EOL, $contents);

        return (bool)$filter ? array_filter($array) : $array;
    }

    /**
     * getLines 获取文件一定范围内的内容
     *
     * @param string  $fileName  含完整路径的文件
     * @param integer $startLine 开始行数 默认第1行
     * @param integer $endLine   结束行数 默认第50行
     * @param string  $mode      打开文件方式
     *
     * @return array  返回内容
     * @throws FileSystemException
     */
    public static function readLines(string $fileName, int $startLine = 1, int $endLine = 10, $mode = 'rb'): array
    {
        $content   = [];
        $startLine = $startLine <= 0 ? 1 : $startLine;

        if ($endLine <= $startLine) {
            return $content;
        }

        // 判断php版本（因为要用到SplFileObject，PHP>=5.1.0）
        if (class_exists('SplFileObject', false)) {
            $count = $endLine - $startLine;

            try {
                $objFile = new SplFileObject($fileName, $mode);
                $objFile->seek($startLine - 1); // 转到第N行, seek方法参数从0开始计数

                for ($i = 0; $i <= $count; ++$i) {
                    $content[] = $objFile->current(); // current()获取当前行内容
                    $objFile->next(); // 下一行
                }
            } catch (Throwable $e) {
                throw new FileSystemException("Error on read the file '{$fileName}'. ERR: " . $e->getMessage());
            }

        } else { // PHP<5.1
            if (!$fp = fopen($fileName, $mode)) {
                throw new FileSystemException('can not open the file:' . $fileName);
            }

            // 移动指针 跳过前$startLine行
            for ($i = 1; $i < $startLine; ++$i) {
                fgets($fp);
            }

            // 读取文件行内容
            for (; $i <= $endLine; ++$i) {
                $content[] = fgets($fp);
            }

            fclose($fp);
        }

        return $content;
    }

    /**
     * symmetry  得到当前行对称上下几($lineNum)行的内容
     *
     * @param string  $fileName 含完整路径的文件
     * @param integer $current  [当前行数]
     * @param integer $lineNum  [获取行数] = $lineNum*2+1
     *
     * @return array
     * @throws FileSystemException
     */
    public static function readSymmetry($fileName, $current = 1, $lineNum = 3): array
    {
        $startLine = $current - $lineNum;
        $endLine   = $current + $lineNum;

        if ((int)$current < ($lineNum + 1)) {
            $startLine = 1;
            $endLine   = 9;
        }

        return self::readLines($fileName, $startLine, $endLine);
    }

    /**
     * @param string $file
     * @param int    $baseLine
     * @param int    $prevLines
     * @param int    $nextLines
     *
     * @return array
     * @throws FileSystemException
     */
    public static function readRangeLines(string $file, int $baseLine, int $prevLines = 3, int $nextLines = 3): array
    {
        $startLine = $baseLine - $prevLines;
        $endLine   = $baseLine + $nextLines;

        return self::readLines($file, $startLine, $endLine);
    }

    /**
     * 得到基准行数上5行下3行的内容， lines up and down
     *
     * @param string $file
     * @param int    $baseLine 基准行数
     *
     * @return array
     * @throws FileSystemException
     */
    public static function getLines5u3d(string $file, int $baseLine = 1): array
    {
        return self::readRangeLines($file, $baseLine, 5);
    }

    /**
     * 读取文件的最后几行（支持大文件读取）
     *
     * @link http://www.jb51.net/article/81909.htm
     *
     * @param resource $fp e.g fopen("access.log", "r+")
     * @param int      $n
     * @param int      $base
     *
     * @return array
     */
    public static function tail($fp, int $n, int $base = 5): array
    {
        assert($n > 0);

        $pos   = $n + 1;
        $lines = [];

        while (count($lines) <= $n) {
            try {
                fseek($fp, -$pos, SEEK_END);
            } catch (Exception $e) {
                fclose($fp);
                break;
            }

            $pos *= $base;

            while (!feof($fp)) {
                array_unshift($lines, fgets($fp));
            }
        }

        return array_slice($lines, 0, $n);
    }
}
