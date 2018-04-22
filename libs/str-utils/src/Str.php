<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/7 0007
 * Time: 21:12
 */

namespace MyLib\StrUtil;

/**
 * Class Str
 *  alias of the StringHelper
 * @package MyLib\StrUtil
 */
class Str extends StringHelper
{
    /**
     * @param string $string
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public static function optional(string $string, string $prefix = ' ', string $suffix = ''): string
    {
        if (empty($string)) {
            return '';
        }

        return $prefix . $string . $suffix;
    }

    /**
     * @param string $string
     * @param string|array $needle
     * @return bool
     */
    public static function contains(string $string, $needle)
    {
        return self::has($string, $needle);
    }

    /**
     * @param string $string
     * @param string|array $needle
     * @return bool
     */
    public static function has(string $string, $needle)
    {
        if (\is_string($needle)) {
            return stripos($string, $needle) !== false;
        }

        if (\is_array($needle)) {
            foreach ((array)$needle as $item) {
                if (stripos($string, $item) !== false) {
                    return true;
                }
            }
        }

        return false;
    }
}
