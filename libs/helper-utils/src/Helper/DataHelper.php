<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-6-5
 * Time: 下午11:39
 *  数据操作 转码 序列化
 */

namespace Toolkit\Helper;

/**
 * Class DataHelper
 * @package Toolkit\Helper
 */
class DataHelper
{
    /**
     * 定义一个用来序列化数据的函数
     * @param mixed $obj
     * @return string
     */
    public static function encode($obj): string
    {
        return base64_encode(gzcompress(serialize($obj)));
    }

    /**
     * 反序列化
     * @param $txt
     * @param bool|array $allowedClasses
     * @return mixed
     */
    public static function decode($txt, $allowedClasses = false)
    {
        return unserialize(gzuncompress(base64_decode($txt)), ['allowed_classes' => $allowedClasses]);
    }

    /**
     * php对象转换成为数组
     * @param iterable|array|\Traversable $data
     * @param bool $recursive
     * @return array|bool
     */
    public static function toArray($data, $recursive = false)
    {
        // Ensure the input data is an array.
        if (\is_object($data)) {
            if ($data instanceof \Traversable) {
                $data = iterator_to_array($data);
            } elseif (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            }
        } else {
            $data = (array)$data;
        }

        if ($recursive) {
            foreach ($data as &$value) {
                if (\is_array($value) || \is_object($value)) {
                    $value = static::toArray($value, $recursive);
                }
            }
        }

        return $data;
    }

    /**
     * data to array
     * @param array|\Traversable $array
     * @param string $class
     * @return mixed
     */
    public static function toObject($array, $class = \stdClass::class)
    {
        $object = new $class;

        foreach ($array as $name => $value) {
            $name = trim($name);

            if (!$name || is_numeric($name)) {
                continue;
            }

            $object->$name = \is_array($value) ? self::toObject($value) : $value;
        }

        return $object;
    }

    /**
     * Sanitize a string
     * @param string $string String to sanitize
     * @param bool $clearTag clear html tag
     * @return string Sanitized string
     */
    public static function safeOutput($string, $clearTag = false): string
    {
        if (!$clearTag) {
            $string = strip_tags($string);
        }

        return @self::htmlentitiesUTF8($string);
    }

    /**
     * @param $string
     * @param int $type
     * @return array|string
     */
    public static function htmlentitiesUTF8($string, $type = ENT_QUOTES)
    {
        if (\is_array($string)) {
            return array_map([__CLASS__, 'htmlentitiesUTF8'], $string);
        }

        return htmlentities((string)$string, $type, 'utf-8');
    }

    /**
     * @param $string
     * @return string
     */
    public static function htmlentitiesDecodeUTF8($string): string
    {
        if (\is_array($string)) {
            $string = array_map([__CLASS__, 'htmlentitiesDecodeUTF8'], $string);

            return (string)array_shift($string);
        }

        return html_entity_decode((string)$string, ENT_QUOTES, 'utf-8');
    }

    /**
     * @param $argc
     * @param $argv
     * @return null
     */
    public static function argvToGET($argc, $argv)
    {
        if ($argc <= 1) {
            return true;
        }

        // get the first argument and parse it like a query string
        parse_str($argv[1], $args);
        if (!\is_array($args) || !\count($args)) {
            return true;
        }

        $_GET = array_merge($args, $_GET);
        $_SERVER['QUERY_STRING'] = $argv[1];

        return true;
    }

    /**
     * 清理数据的空白
     * @param $data array|string
     * @return array|string
     */
    public static function trim($data)
    {
        if (is_scalar($data)) {
            return trim($data);
        }

        array_walk_recursive($data, function (&$value) {
            $value = trim($value);
        });

        return $data;
    }

    /*
     * strip_tags — 从字符串中去除 HTML 和 PHP 标记
     * 由于 strip_tags() 无法实际验证 HTML，不完整或者破损标签将导致更多的数据被删除。
     * $allow_tags 允许的标记,多个以空格隔开
     **/
    public static function stripTags($data, $allow_tags = null)
    {
        if (\is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::stripTags($v, $allow_tags);
            }

            return $data;
        }

        if (\is_string($data) || is_numeric($data)) {
            return strip_tags($data, $allow_tags);
        }

        return false;
    }

    /**
     * 对数组或字符串进行加斜杠\转义处理 去除转义
     * 去除转义返回一个去除反斜线后的字符串（\' 转换为 ' 等等）。双反斜线（\\）被转换为单个反斜线（\）。
     * @param array|string $data 数据可以是字符串或数组
     * @param int $escape 进行转义 true 转义处理 false 去除转义
     * @param int $level 增强
     * @return array|string
     */
    public static function slashes($data, $escape = 1, $level = 0)
    {
        if (\is_array($data)) {
            foreach ((array)$data as $key => $value) {
                $data[$key] = self::slashes($value, $escape, $level);
            }

            return $data;
        }

        $data = trim($data);

        if (!$escape) {
            return stripslashes($data);
        }

        $data = addslashes($data);

        if ($level) {
            // 两个str_replace替换转义目的是防止黑客转换SQL编码进行攻击。
            $data = str_replace(['_', '%'], ["\_", "\%"], $data);    // 转义掉_ %
        }

        return $data;
    }

    public static function escape_query($str): string
    {
        return strtr($str, array(
            "\0" => '',
            "'" => '&#39;',
            '"' => '&#34;',
            "\\" => '&#92;',
            // more secure
            '<' => '&lt;',
            '>' => '&gt;',
        ));
    }

    /**
     * 对数据进行字符集转换处理，数据可以是字符串或数组及对象
     * @param array|string $data
     * @param $in_charset
     * @param $out_charset
     * @return array|string
     */
    public static function changeEncode($data, $in_charset = 'GBK', $out_charset = 'UTF-8')
    {
        if (\is_array($data)) {

            foreach ($data as $key => $value) {
                $data[$key] = self::changeEncode($value, $in_charset, $out_charset);
            }

            return $data;
        }

        if (\function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($data, $out_charset, $in_charset);
        }

        return iconv($in_charset, $out_charset . '/' . '/IGNORE', $data);
    }

}
