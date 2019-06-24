<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2016/8/10 0010
 * Time: 00:41
 */

namespace Toolkit\StrUtil;

use InvalidArgumentException;
use RuntimeException;
use stdClass;
use function array_merge;
use function basename;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function is_string;
use function json_decode;
use function json_encode;
use function preg_replace;
use function trim;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Class JsonHelper
 *
 * @package Toolkit\StrUtil
 */
class JsonHelper
{
    /**
     * @param mixed $data
     * @param int   $flags
     *
     * @return false|string
     */
    public static function prettyJSON(
        $data,
        int $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ) {
        return json_encode($data, $flags);
    }

    /**
     * encode data to json
     *
     * @param $data
     *
     * @return string
     */
    public static function encode($data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $data
     * @param bool   $toArray
     *
     * @return array|mixed|null|stdClass|string
     * @throws InvalidArgumentException
     */
    public static function parse(string $data, bool $toArray = true)
    {
        if (is_file($data)) {
            return self::parseFile($data, $toArray);
        }

        return self::parseString($data, $toArray);
    }

    /**
     * @param string    $file
     * @param bool|true $toArray
     *
     * @return mixed|null|string
     * @throws InvalidArgumentException
     */
    public static function parseFile(string $file, $toArray = true)
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException("File not found or does not exist resources: {$file}");
        }

        $string = file_get_contents($file);

        return self::parseString($string, $toArray);
    }

    /**
     * @param string $string
     * @param bool   $toArray
     *
     * @return array|mixed|stdClass
     */
    public static function parseString(string $string, bool $toArray = true)
    {
        if (!$string) {
            return $toArray ? [] : new stdClass();
        }

        $string = (string)preg_replace([
            // 去掉所有多行注释/* .... */
            '/\/\*.*?\*\/\s*/is',
            // 去掉所有单行注释//....
            '/\/\/.*?[\r\n]/is',
            // 去掉空白, 多个空格换成一个
            //'/(?!\w)\s*?(?!\w)/is'
        ], ['', '', ' '], trim($string));

        // json_last_error() === JSON_ERROR_NONE
        return json_decode($string, $toArray);
    }

    /**
     * @param string $input   文件 或 数据
     * @param bool   $output  是否输出到文件， 默认返回格式化的数据
     * @param array  $options 当 $output=true,此选项有效
     *                        $options = [
     *                        'type'      => 'min' // 输出数据类型 min 压缩过的 raw 正常的
     *                        'file'      => 'xx.json' // 输出文件路径;仅是文件名，则会取输入路径
     *                        ]
     *
     * @return string | bool
     */
    public static function format($input, $output = false, array $options = [])
    {
        if (!is_string($input)) {
            return false;
        }

        $data = trim($input);

        if (file_exists($input)) {
            $data = file_get_contents($input);
        }

        if (!$data) {
            return false;
        }

        $data = preg_replace([
            // 去掉所有多行注释/* .... */
            '/\/\*.*?\*\/\s*/is',
            // 去掉所有单行注释//....
            '/\/\/.*?[\r\n]/is',
            // 去掉空白行
            "/(\n[\r])+/is"
        ], ['', '', "\n"], $data);

        if (!$output) {
            return $data;
        }

        $default = ['type' => 'min'];
        $options = array_merge($default, $options);

        if (file_exists($input) && (empty($options['file']) || !is_file($options['file']))) {
            $dir  = dirname($input);
            $name = basename($input, '.json');
            $file = $dir . '/' . $name . '.' . $options['type'] . '.json';
            // save to options
            $options['file'] = $file;
        }

        static::saveAs($data, $options['file'], $options['type']);
        return $data;
    }

    /**
     * @param string $data
     * @param string $output
     * @param array  $options
     *
     * @return bool|int
     */
    public static function saveAs(string $data, string $output, array $options = [])
    {
        $default = ['type' => 'min', 'file' => ''];
        $options = array_merge($default, $options);
        $saveDir = dirname($output);

        if (!file_exists($saveDir)) {
            throw new RuntimeException('设置的json文件输出' . $saveDir . '目录不存在！');
        }

        $name = basename($output, '.json');
        $file = $saveDir . '/' . $name . '.' . $options['type'] . '.json';

        // 去掉空白
        if ($options['type '] === 'min') {
            $data = preg_replace('/(?!\w)\s*?(?!\w)/i', '', $data);
        }

        return file_put_contents($file, $data);
    }
}
