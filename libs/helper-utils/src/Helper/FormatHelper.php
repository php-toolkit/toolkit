<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2015/1/29
 * Use : ...
 * File: FormatHelper.php
 */

namespace MyLib\Helpers\Helper;

/**
 * Class FormatHelper
 * @package MyLib\Helpers\Helper
 */
class FormatHelper
{
    /**
     * formatTime
     * @param  int $secs
     * @return string
     */
    public static function beforeTime($secs)
    {
        static $timeFormats = [
            [0, '< 1 sec'],
            [1, '1 sec'],
            [2, 'secs', 1],
            [60, '1 min'],
            [120, 'mins', 60],
            [3600, '1 hr'],
            [7200, 'hrs', 3600],
            [86400, '1 day'],
            [172800, 'days', 86400],
        ];

        foreach ($timeFormats as $index => $format) {
            if ($secs >= $format[0]) {
                if ((isset($timeFormats[$index + 1]) && $secs < $timeFormats[$index + 1][0])
                    || $index === \count($timeFormats) - 1
                ) {
                    if (2 === \count($format)) {
                        return $format[1];
                    }

                    return floor($secs / $format[2]) . ' ' . $format[1];
                }
            }
        }

        return date('Y-m-d H:i:s', $secs);
    }

    /**
     * @param string $mTime value is microtime(1)
     * @return string
     */
    public static function microTime($mTime = null)
    {
        if (!$mTime) {
            $mTime = microtime(true);
        }

        list($ts, $ms) = explode('.', sprintf('%.4f', $mTime));

        return date('Y/m/d H:i:s', $ts) . '.' . $ms;
    }

    /**
     * @param $memory
     * @return string
     * ```
     * Helper::memory(memory_get_usage(true));
     * ```
     */
    public static function memory($memory)
    {
        if ($memory >= 1024 * 1024 * 1024) {
            return sprintf('%.1f GiB', $memory / 1024 / 1024 / 1024);
        }

        if ($memory >= 1024 * 1024) {
            return sprintf('%.1f MiB', $memory / 1024 / 1024);
        }

        if ($memory >= 1024) {
            return sprintf('%d KiB', $memory / 1024);
        }

        return sprintf('%d B', $memory);
    }

    /**
     * @param int $size
     * @return string
     * ```
     * Helper::size(memory_get_usage(true));
     * ```
     */
    public static function size($size)
    {
        if ($size >= 1024 * 1024 * 1024) {
            return sprintf('%.1f Gb', $size / 1024 / 1024 / 1024);
        }

        if ($size >= 1024 * 1024) {
            return sprintf('%.1f Mb', $size / 1024 / 1024);
        }

        if ($size >= 1024) {
            return sprintf('%d Kb', $size / 1024);
        }

        return sprintf('%d b', $size);
    }

    /**
     * Format a number into a human readable format
     * e.g. 24962496 => 23.81M
     * @param     $size
     * @param int $precision
     * @return string
     */
    public static function bytes($size, $precision = 2)
    {
        if (!$size) {
            return '0';
        }

        $base = log($size) / log(1024);
        $suffixes = array('b', 'k', 'M', 'G', 'T');
        $floorBase = floor($base);

        return round(1024 ** ($base - $floorBase), $precision) . $suffixes[(int)$floorBase];
    }

    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value
     * @param string $value value to convert
     * @return int
     */
    public static function convertBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        $value_length = \strlen($value);
        $qty = (int)substr($value, 0, $value_length - 1);
        $unit = Str::strtolower(substr($value, $value_length - 1));
        switch ($unit) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
        }

        return $qty;
    }

    /**
     * Replaces &amp; with & for XHTML compliance
     * @param   string $text Text to process
     * @return  string  Processed string.
     */
    public static function ampReplace($text)
    {
        $text = str_replace([
            '&&',
            '&#',
            '&#',
            '&amp;',
            '*-*',
            '*--*',
        ], [
            '*--*',
            '*-*',
            '*-*',
            '&',
            '&#',
            '&&',
        ], $text);

        $text = (string)preg_replace('/|&(?![\w]+;)|/', '&amp;', $text);

        return $text;
    }

    /**
     * Cleans text of all formatting and scripting code
     * @param   string|null|array $text Text to clean
     * @return  string  Cleaned text.
     */
    public static function cleanText(string $text)
    {
        $text = preg_replace('/<script[^>]*>.*?</script>/si', '', $text);
        $text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
        $text = preg_replace('/<!--.+?-->/', '', $text);
        $text = preg_replace('/{.+?}/', '', $text);
        $text = preg_replace('/&nbsp;/', ' ', $text);
        $text = preg_replace('/&amp;/', ' ', $text);
        $text = preg_replace('/&quot;/', ' ', $text);
        $text = strip_tags($text);
        $text = htmlspecialchars($text);

        return $text;
    }

    /**
     * 返回删除注释和空格后的PHP源码(php_strip_whitespace)
     * @link http://cn2.php.net/manual/zh/function.php-strip-whitespace.php
     * @param  string|bool $src
     * @return string
     */
    public static function phpCode($src)
    {
        // Whitespaces left and right from this signs can be ignored
        static $IW = array(
            T_CONCAT_EQUAL, // .=
            T_DOUBLE_ARROW, // =>
            T_BOOLEAN_AND, // &&
            T_BOOLEAN_OR, // ||
            T_IS_EQUAL, // ==
            T_IS_NOT_EQUAL, // != or <>
            T_IS_SMALLER_OR_EQUAL, // <=
            T_IS_GREATER_OR_EQUAL, // >=
            T_INC, // ++
            T_DEC, // --
            T_PLUS_EQUAL, // +=
            T_MINUS_EQUAL, // -=
            T_MUL_EQUAL, // *=
            T_DIV_EQUAL, // /=
            T_IS_IDENTICAL, // ===
            T_IS_NOT_IDENTICAL, // !==
            T_DOUBLE_COLON, // ::
            T_PAAMAYIM_NEKUDOTAYIM, // ::
            T_OBJECT_OPERATOR, // ->
            T_DOLLAR_OPEN_CURLY_BRACES, // ${
            T_AND_EQUAL, // &=
            T_MOD_EQUAL, // %=
            T_XOR_EQUAL, // ^=
            T_OR_EQUAL, // |=
            T_SL, // <<
            T_SR, // >>
            T_SL_EQUAL, // <<=
            T_SR_EQUAL, // >>=
        );

        if (!$src) {
            return false;
        }

        if (is_file($src) && (!$src = file_get_contents($src))) {
            return false;
        }

        $tokens = token_get_all($src);

        $new = '';
        $c = \count($tokens);
        $iw = false; // ignore whitespace
        $ih = false; // in HEREDOC
        $ls = ''; // last sign
        $ot = null; // open tag
        for ($i = 0; $i < $c; $i++) {
            $token = $tokens[$i];
            if (\is_array($token)) {
                list($tn, $ts) = $token; // tokens: number, string, line
                $tname = token_name($tn);
                if ($tn === T_INLINE_HTML) {
                    $new .= $ts;
                    $iw = false;
                } else {
                    if ($tn === T_OPEN_TAG) {
                        if (strpos($ts, ' ') || strpos($ts, "\n") || strpos($ts, "\t") || strpos($ts, "\r")) {
                            $ts = rtrim($ts);
                        }
                        $ts .= ' ';
                        $new .= $ts;
                        $ot = T_OPEN_TAG;
                        $iw = true;
                    } elseif ($tn === T_OPEN_TAG_WITH_ECHO) {
                        $new .= $ts;
                        $ot = T_OPEN_TAG_WITH_ECHO;
                        $iw = true;
                    } elseif ($tn === T_CLOSE_TAG) {
                        if ($ot === T_OPEN_TAG_WITH_ECHO) {
                            $new = rtrim($new, '; ');
                        } else {
                            $ts = ' ' . $ts;
                        }
                        $new .= $ts;
                        $ot = null;
                        $iw = false;
                    } elseif (\in_array($tn, $IW, true)) {
                        $new .= $ts;
                        $iw = true;
                    } elseif ($tn === T_CONSTANT_ENCAPSED_STRING || $tn === T_ENCAPSED_AND_WHITESPACE) {
                        if ($ts[0] === '"') {
                            $ts = addcslashes($ts, "\n\t\r");
                        }
                        $new .= $ts;
                        $iw = true;
                    } elseif ($tn === T_WHITESPACE) {
                        $nt = @$tokens[$i + 1];
                        if (!$iw && (!\is_string($nt) || $nt === '$') && !\in_array($nt[0], $IW)) {
                            $new .= " ";
                        }
                        $iw = false;
                    } elseif ($tn === T_START_HEREDOC) {
                        $new .= "<<<S\n";
                        $iw = false;
                        $ih = true; // in HEREDOC
                    } elseif ($tn === T_END_HEREDOC) {
                        $new .= "S;\n";
                        $iw = true;
                        $ih = false; // in HEREDOC
                        for ($j = $i + 1; $j < $c; $j++) {
                            if (\is_string($tokens[$j]) && $tokens[$j] === ';') {
                                $i = $j;
                                break;
                            }
                            if ($tokens[$j][0] === T_CLOSE_TAG) {
                                break;
                            }
                        }
                    } elseif ($tn === T_COMMENT || $tn === T_DOC_COMMENT) {
                        $iw = true;
                    } else {
                        if (!$ih) {
                            $ts = strtolower($ts);
                        }
                        $new .= $ts;
                        $iw = false;
                    }
                }
                $ls = '';
            } else {
                if (($token !== ';' && $token !== ':') || $ls !== $token) {
                    $new .= $token;
                    $ls = $token;
                }
                $iw = true;
            }
        }

        return $new;
    }
}
