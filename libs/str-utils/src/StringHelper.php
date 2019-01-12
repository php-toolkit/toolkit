<?php

namespace Toolkit\StrUtil;

/**
 * Class StringHelper
 * @package Toolkit\StrUtil
 */
abstract class StringHelper
{
    ////////////////////////////////////////////////////////////////////////
    /// Check value
    ////////////////////////////////////////////////////////////////////////

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
     * @param string       $string
     * @param string|array $needle
     * @return bool
     */
    public static function contains(string $string, $needle): bool
    {
        return self::has($string, $needle);
    }

    /**
     * @param string       $string
     * @param string|array $needle
     * @return bool
     */
    public static function has(string $string, $needle): bool
    {
        if (\is_string($needle)) {
            return \stripos($string, $needle) !== false;
        }

        if (\is_array($needle)) {
            foreach ((array)$needle as $item) {
                if (\stripos($string, $item) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param        $str
     * @param        $find
     * @param int    $offset
     * @param string $encoding
     * @return bool|int
     */
    public static function strpos(string $str, string $find, int $offset = 0, string $encoding = 'UTF-8')
    {
        return \function_exists('mb_strpos') ?
            \mb_strpos($str, $find, $offset, $encoding) :
            \strpos($str, $find, $offset);
    }

    /**
     * @param string $str
     * @param string $find
     * @param int    $offset
     * @param string $encoding
     * @return bool|int
     */
    public static function strrpos(string $str, string $find, int $offset = 0, string $encoding = 'utf-8')
    {
        return \function_exists('mb_strrpos') ?
            \mb_strrpos($str, $find, $offset, $encoding) :
            \strrpos($str, $find, $offset);
    }

    /**
     * 使用正则验证数据
     * @access public
     * @param string $value 要验证的数据
     * @param string $rule 验证规则 require email url currency number integer english
     * @return boolean
     */
    public static function regexMatch(string $value, string $rule): bool
    {
        $validate = [
            'require'  => '/\S+/',
            'email'    => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            // 'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'url'      => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i',
            'currency' => '/^\d+(\.\d+)?$/',
            # 货币
            'number'   => '/^\d+$/',
            'zip'      => '/^\d{6}$/',
            'integer'  => '/^[-\+]?\d+$/',
            'double'   => '/^[-\+]?\d+(\.\d+)?$/',
            'english'  => '/^[A-Za-z]+$/',
        ];

        $value = \trim($value);
        $name  = \strtolower($rule);

        // 检查是否有内置的正则表达式
        if (isset($validate[$name])) {
            $rule = $validate[$name];
        }

        return \preg_match($rule, $value) === 1;
    }

    ////////////////////////////////////////////////////////////////////////
    /// Check Length
    ////////////////////////////////////////////////////////////////////////

    /**
     * from Symfony
     * @param string $string
     * @return int
     */
    public static function len(string $string): int
    {
        if (false === $encoding = \mb_detect_encoding($string, null, true)) {
            return \strlen($string);
        }

        return \mb_strwidth($string, $encoding);
    }

    public static function strlen(string $str, string $encoding = 'UTF-8'): int
    {
        $str = \html_entity_decode($str, \ENT_COMPAT, 'UTF-8');

        return \function_exists('mb_strlen') ? \mb_strlen($str, $encoding) : \strlen($str);
    }

    /**
     * @param string $string
     * @return int
     */
    public static function utf8Len(string $string): int
    {
        // strlen: one chinese is 3 char.
        // mb_strlen: one chinese is 1 char.
        // mb_strwidth: one chinese is 2 char.
        return \mb_strlen($string, 'utf-8');
    }

    /**
     * 计算字符长度
     * @param  string $str
     * @return int
     */
    public static function length(string $str): int
    {
        if ($str === '') {
            return 0;
        }

        if (\function_exists('mb_strlen')) {
            return \mb_strlen($str, 'utf-8');
        }

        \preg_match_all('/./u', $str, $arr);

        return \count($arr[0]);
    }

    /**
     * @from web
     * 可以统计中文字符串长度的函数
     * @param string $str 要计算长度的字符串
     * @internal param bool $type 计算长度类型，0(默认)表示一个中文算一个字符，1表示一个中文算两个字符
     * @return int
     */
    public static function absLen(string $str): int
    {
        if (empty($str)) {
            return 0;
        }

        if (\function_exists('mb_strwidth')) {
            return \mb_strwidth($str, 'utf-8');
        }

        if (\function_exists('mb_strlen')) {
            return \mb_strlen($str, 'utf-8');
        }

        \preg_match_all('/./u', $str, $ar);

        return \count($ar[0]);
    }

    ////////////////////////////////////////////////////////////
    /// Security
    ////////////////////////////////////////////////////////////

    /**
     * ********************** 生成一定长度的随机字符串函数 **********************
     * @param  int         $length - 随机字符串长度
     * @param array|string $param -
     * @internal param string $chars
     * @return string
     * @throws \Exception
     */
    public static function random(int $length, array $param = []): string
    {
        $param = \array_merge([
            'prefix' => '',
            'suffix' => '',
            'chars'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
        ], $param);

        $chars = $param['chars'];
        $max   = \strlen($chars) - 1;   //strlen($chars) 计算字符串的长度
        $str   = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, $max)];
        }

        return $param['prefix'] . $str . $param['suffix'];
    }

    /**
     * @param int $length
     * @return string
     */
    public static function genSalt(int $length = 32): string
    {
        return \substr(
            \str_replace('+', '.', \base64_encode(\hex2bin(\random_token($length)))),
            0,
            44
        );
    }

    /**
     * @param int $length
     * @return bool|string
     */
    public static function genUid(int $length = 7): string
    {
        if (!\is_int($length) || $length > 32 || $length < 1) {
            $length = 7;
        }

        return \substr(\hash('md5', \uniqid('', true)), 0, $length);
    }

    /**
     * @param string $string
     * @param int    $indent
     * @param string $padStr
     * @return string
     */
    public static function pad(string $string, $indent, $padStr): string
    {
        return $indent > 0 ? \str_pad($string, $indent, $padStr) : $string;
    }

    /**
     * gen UUID
     * @param int  $version
     * @param null $node
     * @param null $ns
     * @return UUID
     * @throws \Inhere\Exceptions\InvalidArgumentException
     */
    // public static function genUUID($version = 1, $node = null, $ns = null)
    // {
    //     return UUID::generate($version, $node, $ns);
    // }

    ////////////////////////////////////////////////////////////////////////
    /// Case Convert
    ////////////////////////////////////////////////////////////////////////

    /**
     * Convert \n and \r\n and \r to <br />
     * @param string $str String to transform
     * @return string New string
     */
    public static function nl2br(string $str): string
    {
        return \str_replace(["\r\n", "\r", "\n"], '<br />', $str);
    }

    public static function lower(string $str): string
    {
        return static::strtolower($str);
    }

    /**
     * @param string $str
     * @return bool|string
     */
    public static function strtolower(string $str): string
    {
        return \function_exists('mb_strtolower') ? \mb_strtolower($str, 'utf-8') : \strtolower($str);
    }

    public static function upper(string $str): string
    {
        return static::strtoupper($str);
    }

    /**
     * @param $str
     * @return bool|string
     */
    public static function strtoupper(string $str)
    {
        if (!\is_string($str)) {
            return $str;
        }

        return \function_exists('mb_strtoupper') ? \mb_strtoupper($str, 'utf-8') : \strtoupper($str);
    }

    /**
     * @param $str
     * @return string
     */
    public static function ucfirst(string $str): string
    {
        return self::strtoupper(self::substr($str, 0, 1)) . self::substr($str, 1);
    }

    /**
     * @param $str
     * @return string
     */
    public static function ucwords(string $str): string
    {
        return \function_exists('mb_convert_case') ?
            \mb_convert_case($str, \MB_CASE_TITLE) :
            \ucwords(self::strtolower($str));
    }

    /**
     * @param string $str
     * @param bool   $upperFirstChar
     * @return mixed
     */
    public static function camel(string $str, bool $upperFirstChar = false): string
    {
        return self::toCamelCase($str, $upperFirstChar);
    }

    /**
     * @param string $str
     * @param bool   $upperFirstChar
     * @return mixed
     */
    public static function toCamel(string $str, bool $upperFirstChar = false): string
    {
        return self::toCamelCase($str, $upperFirstChar);
    }

    /**
     * to camel
     * @param string $name
     * @param bool   $upperFirst
     * @return string
     */
    public static function camelCase(string $name, bool $upperFirst = false): string
    {
        $name = \trim($name, '-_');

        // convert 'first-second' to 'firstSecond'
        if (\strpos($name, '-')) {
            $name = \ucwords(\str_replace('-', ' ', $name));
            $name = \str_replace(' ', '', \lcfirst($name));
        }

        return $upperFirst ? \ucfirst($name) : $name;
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -> firstName)
     * @param  string $str
     * @param bool    $upperFirst
     * @return mixed
     */
    public static function toCamelCase(string $str, bool $upperFirst = false): string
    {
        $str = (string)self::strtolower($str);

        if ($upperFirst) {
            $str = self::ucfirst($str);
        }

        return \preg_replace_callback('/_+([a-z])/', function ($c) {
            return \strtoupper($c[1]);
        }, $str);
    }

    public static function snake(string $str, string $sep = '_'): string
    {
        return self::toSnakeCase($str, $sep);
    }

    public static function toSnake(string $str, string $sep = '_'): string
    {
        return self::toSnakeCase($str, $sep);
    }

    /**
     * Transform a CamelCase string to underscore_case string
     * @param string $str
     * @param string $sep
     * @return string
     */
    public static function toSnakeCase(string $str, string $sep = '_'): string
    {
        // 'CMSCategories' => 'cms_categories'
        // 'RangePrice' => 'range_price'
        return self::lower(\trim(\preg_replace('/([A-Z][a-z])/', $sep . '$1', $str), $sep));
    }

    /**
     * 驼峰式 <=> 下划线式
     * @param  string $str [description]
     * @param  bool   $toCamelCase
     * true : 驼峰式 => 下划线式
     * false : 驼峰式 <= 下划线式
     * @return string
     */
    public static function nameChange(string $str, bool $toCamelCase = true): string
    {
        $str = \trim($str);

        // 默认 ：下划线式 =>驼峰式
        if ($toCamelCase) {
            if (\strpos($str, '_') === false) {
                return $str;
            }

            $arr_char  = \explode('_', \strtolower($str));
            $newString = \array_shift($arr_char);

            foreach ($arr_char as $val) {
                $newString .= \ucfirst($val);
            }

            return $newString;
        }

        // 驼峰式 => 下划线式
        return \strtolower(\preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
    }

    ////////////////////////////////////////////////////////////////////////
    /// Convert to array
    ////////////////////////////////////////////////////////////////////////

    /**
     * var_dump(str2array('34,56,678, 678, 89, '));
     * @param string $str
     * @param string $sep
     * @return array
     */
    public static function str2array(string $str, string $sep = ','): array
    {
        $str = \trim($str, "$sep ");

        if (!$str) {
            return [];
        }

        return \preg_split("/\s*$sep\s*/", $str, -1, \PREG_SPLIT_NO_EMPTY);
    }

    public static function toArray(string $string, string $delimiter = ',', int $limit = 0): array
    {
        $string = \trim($string, "$delimiter ");
        if ($string === '') {
            return [];
        }

        $values  = [];
        $rawList = $limit < 1 ? \explode($delimiter, $string) : \explode($delimiter, $string, $limit);

        foreach ($rawList as $val) {
            if (($val = \trim($val)) !== '') {
                $values[] = $val;
            }
        }

        return $values;
    }

    public static function explode(string $str, string $separator = '.', int $limit = 0): array
    {
        return static::split2Array($str, $separator, $limit);
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param int    $limit
     * @return array
     */
    public static function split2Array(string $string, string $delimiter = ',', int $limit = 0): array
    {
        $string = \trim($string, "$delimiter ");

        if (!\strpos($string, $delimiter)) {
            return [$string];
        }

        if ($limit < 1) {
            $list = \explode($delimiter, $string);
        } else {
            $list = \explode($delimiter, $string, $limit);
        }

        return \array_values(array_filter(\array_map('trim', $list), 'strlen'));
    }

    /**
     * @param string $string
     * @param int    $width
     * @return array
     */
    public static function splitByWidth(string $string, int $width): array
    {
        // str_split is not suitable for multi-byte characters, we should use preg_split to get char array properly.
        // additionally, array_slice() is not enough as some character has doubled width.
        // we need a function to split string not by character count but by string width
        if (false === $encoding = \mb_detect_encoding($string, null, true)) {
            return \str_split($string, $width);
        }

        $utf8String = \mb_convert_encoding($string, 'utf8', $encoding);
        $lines      = [];
        $line       = '';

        foreach (\preg_split('//u', $utf8String) as $char) {
            // test if $char could be appended to current line
            if (\mb_strwidth($line . $char, 'utf8') <= $width) {
                $line .= $char;
                continue;
            }

            // if not, push current line to array and make new line
            $lines[] = \str_pad($line, $width);
            $line    = $char;
        }

        if ('' !== $line) {
            $lines[] = \count($lines) ? \str_pad($line, $width) : $line;
        }

        \mb_convert_variables($encoding, 'utf8', $lines);

        return $lines;
    }

    ////////////////////////////////////////////////////////////////////////
    /// Truncate
    ////////////////////////////////////////////////////////////////////////

    /**
     * @param string   $str
     * @param int      $start
     * @param int|null $length
     * @param string   $encoding
     * @return bool|string
     */
    public static function substr(string $str, int $start, int $length = null, string $encoding = 'utf-8')
    {
        if (\function_exists('mb_substr')) {
            return \mb_substr($str, $start, ($length === null ? self::strlen($str) : (int)$length), $encoding);
        }

        return \substr($str, $start, ($length === null ? self::strlen($str) : (int)$length));
    }

    /**
     * @from web
     *  utf-8编码下截取中文字符串,参数可以参照substr函数
     * @param string $str 要进行截取的字符串
     * @param int    $start 要进行截取的开始位置，负数为反向截取
     * @param int    $end 要进行截取的长度
     * @return string
     */
    public static function utf8SubStr(string $str, int $start = 0, int $end = null): string
    {
        if (empty($str)) {
            return false;
        }

        if (\function_exists('mb_substr')) {
            if (\func_num_args() >= 3) {
                $end = \func_get_arg(2);

                return \mb_substr($str, $start, $end, 'utf-8');
            }

            \mb_internal_encoding('UTF-8');

            return \mb_substr($str, $start);

        }

        $null = '';
        \preg_match_all('/./u', $str, $ar);

        if (\func_num_args() >= 3) {
            $end = \func_get_arg(2);
            return \implode($null, \array_slice($ar[0], $start, $end));
        }

        return \implode($null, \array_slice($ar[0], $start));
    }


    /**
     * @from web
     * 中文截取，支持gb2312,gbk,utf-8,big5   *
     * @param string $str 要截取的字串
     * @param int    $start 截取起始位置
     * @param int    $length 截取长度
     * @param string $charset utf-8|gb2312|gbk|big5 编码
     * @param bool   $suffix 是否加尾缀
     * @return string
     */
    public static function zhSubStr($str, $start = 0, $length = 0, $charset = 'utf-8', $suffix = true): string
    {
        if (\function_exists('mb_substr')) {
            if (\mb_strlen($str, $charset) <= $length) {
                return $str;
            }

            $slice = \mb_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

            \preg_match_all($re[$charset], $str, $match);
            if (\count($match[0]) <= $length) {
                return $str;
            }

            $slice = \implode('', \array_slice($match[0], $start, $length));
        }

        return (bool)$suffix ? $slice . '…' : $slice;
    }

    /**
     * Truncate strings
     * @param string $str
     * @param int    $maxLength Max length
     * @param string $suffix Suffix optional
     * @return string $str truncated
     */
    /* CAUTION : Use it only on module hookEvents.
    ** For other purposes use the smarty function instead */
    public static function truncate(string $str, $maxLength, $suffix = '...'): string
    {
        if (self::strlen($str) <= $maxLength) {
            return $str;
        }

        $str = \utf8_decode($str);

        return \utf8_encode(\substr($str, 0, $maxLength - self::strlen($suffix)) . $suffix);
    }

    /**
     * 字符截断输出
     * @param string   $str
     * @param int      $start
     * @param null|int $length
     * @return string
     */
    public static function truncate2(string $str, int $start, int $length = null): string
    {
        if (!$length) {
            $length = $start;
            $start  = 0;
        }

        if (\strlen($str) <= $length) {
            return $str;
        }

        if (\function_exists('mb_substr')) {
            $str = \mb_substr(\strip_tags($str), $start, $length, 'utf-8');
        } else {
            $str = \substr($str, $start, $length) . '...';
        }

        return $str;
    }

    /**
     * Copied from CakePHP String utility file
     * @param string $text
     * @param int    $length
     * @param array  $options
     * @return bool|string
     */
    public static function truncate3(string $text, int $length = 120, array $options = [])
    {
        $default = [
            'ellipsis' => '...',
            'exact'    => true,
            'html'     => true
        ];

        $options  = array_merge($default, $options);
        $ellipsis = $options['ellipsis'];
        $exact    = $options['exact'];
        $html     = $options['html'];

        /**
         * @var string $ellipsis
         * @var bool   $exact
         * @var bool   $html
         */
        if ($html) {
            if (self::strlen(\preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }

            $total_length = self::strlen(\strip_tags($ellipsis));
            $open_tags    = $tags = [];
            $truncate     = '';
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/', $tag[0])) {
                        array_unshift($open_tags, $tag[2]);
                    } elseif (preg_match('/<\/([\w]+)[^>]*>/', $tag[0], $close_tag)) {
                        $pos = array_search($close_tag[1], $open_tags, true);
                        if ($pos !== false) {
                            array_splice($open_tags, $pos, 1);
                        }
                    }
                }
                $truncate       .= $tag[1];
                $content_length = self::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[\d]{1,7};|&#x[0-9a-f]{1,6};/i', ' ',
                    $tag[3]));

                if ($content_length + $total_length > $length) {
                    $left            = $length - $total_length;
                    $entities_length = 0;

                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[\d]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities,
                        PREG_OFFSET_CAPTURE)) {
                        foreach ((array)$entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += self::strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= self::substr($tag[3], 0, $left + $entities_length);
                    break;
                }

                $truncate     .= $tag[3];
                $total_length += $content_length;

                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (self::strlen($text) <= $length) {
                return $text;
            }

            $truncate = self::substr($text, 0, $length - self::strlen($ellipsis));
        }

        $open_tags = null;

        if (!$exact) {
            $spacepos = self::strrpos($truncate, ' ');
            if ($html) {
                $truncate_check = self::substr($truncate, 0, $spacepos);
                $last_open_tag  = self::strrpos($truncate_check, '<');
                $last_close_tag = self::strrpos($truncate_check, '>');

                if ($last_open_tag > $last_close_tag) {
                    preg_match_all('/<[\w]+[^>]*>/', $truncate, $last_tag_matches);
                    $last_tag = array_pop($last_tag_matches[0]);
                    $spacepos = self::strrpos($truncate, $last_tag) + self::strlen($last_tag);
                }

                $bits = self::substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $dropped_tags, PREG_SET_ORDER);

                /** @var array $dropped_tags */
                if (!empty($dropped_tags)) {
                    if (!empty($open_tags)) {
                        foreach ($dropped_tags as $closing_tag) {
                            if (!\in_array($closing_tag[1], $open_tags, true)) {
                                array_unshift($open_tags, $closing_tag[1]);
                            }
                        }
                    } else {
                        foreach ($dropped_tags as $closing_tag) {
                            $open_tags[] = $closing_tag[1];
                        }
                    }
                }
            }

            $truncate = self::substr($truncate, 0, $spacepos);
        }

        $truncate .= $ellipsis;

        if ($html && $open_tags) {
            foreach ((array)$open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    ////////////////////////////////////////////////////////////////////////
    /// Format
    ////////////////////////////////////////////////////////////////////////

    /**
     * [format description]
     * @param       $str
     * @param array $replaceParams 用于 str_replace('search','replace',$str )
     * @example
     *   $replaceParams = [
     *        'xx',  //'search'
     *        'yy', //'replace'
     *   ]
     *   $replaceParams = [
     *        ['xx','xx2'],  //'search'
     *        ['yy','yy2'],  //'replace'
     *   ]
     * @param array $pregParams 用于 preg_replace('pattern','replace',$str)
     * @example
     * $pregParams = [
     *     'xx',  //'pattern'
     *     'yy',  //'replace'
     * ]
     * * $pregParams = [
     *     ['xx','xx2'],  //'pattern'
     *     ['yy','yy2'],  //'replace'
     * ]
     * @return string [type]                [description]
     */
    public static function format($str, array $replaceParams = [], array $pregParams = []): string
    {
        if (!\is_string($str) || !$str || (!$replaceParams && !$pregParams)) {
            return $str;
        }

        if ($replaceParams && \count($replaceParams) === 2) {
            list($search, $replace) = $replaceParams;
            $str = str_replace($search, $replace, $str);
        }

        if ($pregParams && \count($pregParams) === 2) {
            list($pattern, $replace) = $pregParams;
            $str = preg_replace($pattern, $replace, $str);
        }

        return trim($str);
    }

    /**
     * 格式化，用空格分隔各个词组
     * @param  string $keyword 字符串
     * @return string 格式化后的字符串
     */
    public static function wordFormat($keyword): string
    {
        // 将全角角逗号换为空格
        $keyword = str_replace(['，', ','], ' ', $keyword);

        return preg_replace([
            // 去掉两个空格以上的
            '/\s(?=\s)/',
            // 将非空格替换为一个空格
            '/[\n\r\t]/'
        ], ['', ' '], trim($keyword));
    }

    /**
     * 缩进格式化内容，去空白/注释
     * @param     $fileName
     * @param int $type
     * @return mixed
     */
    public static function deleteStripSpace($fileName, $type = 0)
    {
        $data = trim(file_get_contents($fileName));
        $data = 0 === strpos($data, '<?php') ? substr($data, 5) : $data;
        $data = substr($data, -2) === '?>' ? substr($data, 0, -2) : $data;

        //去掉所有注释 换行空白保留
        if ((int)$type === 1) {
            $preg_arr = [
                '/\/\*.*?\*\/\s*/is'    // 去掉所有多行注释/* .... */
                ,
                '/\/\/.*?[\r\n]/is'    // 去掉所有单行注释//....
                ,
                '/\#.*?[\r\n]/is'      // 去掉所有单行注释 #....
            ];

            return preg_replace($preg_arr, '', $data);
        }

        $preg_arr = [
            '/\/\*.*?\*\/\s*/is'    // 去掉所有多行注释 /* .... */
            ,
            '/\/\/.*?[\r\n]/is'    // 去掉所有单行注释 //....
            ,
            '/\#.*?[\r\n]/is'      // 去掉所有单行注释 #....
            ,
            '/(?!\w)\s*?(?!\w)/is' //去掉空白行
        ];

        return preg_replace($preg_arr, '', $data);
    }
}
