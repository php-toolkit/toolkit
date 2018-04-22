<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/1
 * Time: 下午5:33
 */

namespace Toolkit\SysUtil;

/**
 * Class Cli
 * @package Toolkit\SysUtil
 */
class Cli
{
    const NORMAL = 0;

    // Foreground color
    const FG_BLACK = 30;
    const FG_RED = 31;
    const FG_GREEN = 32;
    const FG_BROWN = 33; // like yellow
    const FG_BLUE = 34;
    const FG_CYAN = 36;
    const FG_WHITE = 37;
    const FG_DEFAULT = 39;

    // extra Foreground color
    const FG_DARK_GRAY = 90;
    const FG_LIGHT_RED = 91;
    const FG_LIGHT_GREEN = 92;
    const FG_LIGHT_YELLOW = 93;
    const FG_LIGHT_BLUE = 94;
    const FG_LIGHT_MAGENTA = 95;
    const FG_LIGHT_CYAN = 96;
    const FG_WHITE_W = 97;

    // Background color
    const BG_BLACK = 40;
    const BG_RED = 41;
    const BG_GREEN = 42;
    const BG_BROWN = 43; // like yellow
    const BG_BLUE = 44;
    const BG_CYAN = 46;
    const BG_WHITE = 47;
    const BG_DEFAULT = 49;

    // extra Background color
    const BG_DARK_GRAY = 100;
    const BG_LIGHT_RED = 101;
    const BG_LIGHT_GREEN = 102;
    const BG_LIGHT_YELLOW = 103;
    const BG_LIGHT_BLUE = 104;
    const BG_LIGHT_MAGENTA = 105;
    const BG_LIGHT_CYAN = 106;
    const BG_WHITE_W = 107;

    // color option
    const BOLD = 1;      // 加粗
    const FUZZY = 2;      // 模糊(不是所有的终端仿真器都支持)
    const ITALIC = 3;      // 斜体(不是所有的终端仿真器都支持)
    const UNDERSCORE = 4;  // 下划线
    const BLINK = 5;      // 闪烁
    const REVERSE = 7;    // 颠倒的 交换背景色与前景色
    const CONCEALED = 8;      // 隐匿的

    /**
     * Regex to match tags
     * @var string
     */
    const COLOR_TAG = '/<([a-z=;]+)>(.*?)<\/\\1>/s';

    /**
     * some styles
     * @var array
     */
    const STYLES = [
        'light_red' => '1;31',
        'light_green' => '1;32',
        'yellow' => '1;33',
        'light_blue' => '1;34',
        'magenta' => '1;35',
        'light_cyan' => '1;36',
        'white' => '1;37',
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'brown' => '0;33',
        'blue' => '0;34',
        'cyan' => '0;36',

        'bold' => '1',
        'underscore' => '4',
        'reverse' => '7',

        //
        'suc' => '1;32',// same 'green' and 'bold'
        'success' => '1;32',
        'info' => '0;32',// same 'green'
        'comment' => '0;33',// same 'brown'
        'warning' => '0;30;43',
        'danger' => '0;31',// same 'red'
        'error' => '30;41',
    ];

    /*******************************************************************************
     * color render
     ******************************************************************************/

    /**
     * @param $text
     * @param string|int|array $style
     * @return string
     */
    public static function color($text, $style = null)
    {
        if (!$text) {
            return $text;
        }

        if (!self::isSupportColor()) {
            return self::clearColor($text);
        }

        if (\is_string($style)) {
            $color = self::STYLES[$style] ?? '0';
        } elseif (\is_int($style)) {
            $color = $style;

            // array: [self::FG_GREEN, self::BG_WHITE, self::UNDERSCORE]
        } elseif (\is_array($style)) {
            $color = implode(';', $style);
        } elseif (strpos($text, '<') !== false) {
            return self::renderColor($text);
        } else {
            return $text;
        }

        // $result = chr(27). "$color{$text}" . chr(27) . chr(27) . "[0m". chr(27);
        return "\033[{$color}m{$text}\033[0m";
    }

    public static function renderColor($text)
    {
        if (!$text || false === strpos($text, '<')) {
            return $text;
        }

        // if don't support output color text, clear color tag.
        if (!SysEnv::isSupportColor()) {
            return static::clearColor($text);
        }

        if (!preg_match_all(self::COLOR_TAG, $text, $matches)) {
            return $text;
        }

        foreach ((array)$matches[0] as $i => $m) {
            if ($style = self::STYLES[$matches[1][$i]] ?? null) {
                $tag = $matches[1][$i];
                $match = $matches[2][$i];

                $replace = sprintf("\033[%sm%s\033[0m", $style, $match);
                $text = str_replace("<$tag>$match</$tag>", $replace, $text);
            }
        }

        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function clearColor($text)
    {
        // return preg_replace('/\033\[(?:\d;?)+m/', '' , "\033[0;36mtext\033[0m");
        return preg_replace('/\033\[(?:\d;?)+m/', '', strip_tags($text));
    }

    /*******************************************************************************
     * read/write message
     ******************************************************************************/

    /**
     * @param mixed $message
     * @param bool $nl
     * @return string
     */
    public static function read($message = null, $nl = false): string
    {
        if ($message) {
            self::write($message, $nl);
        }

        return trim(fgets(STDIN));
    }

    /**
     * write message to console
     * @param $message
     * @param bool $nl
     * @param bool $quit
     */
    public static function write($message, $nl = true, $quit = false)
    {
        if (\is_array($message)) {
            $message = implode($nl ? PHP_EOL : '', $message);
        }

        self::stdout(self::renderColor($message), $nl, $quit);
    }

    /**
     * Logs data to stdout
     * @param string $message
     * @param bool $nl
     * @param bool|int $quit
     */
    public static function stdout($message, $nl = true, $quit = false)
    {
        fwrite(\STDOUT, $message . ($nl ? PHP_EOL : ''));
        fflush(\STDOUT);

        if (($isTrue = true === $quit) || \is_int($quit)) {
            $code = $isTrue ? 0 : $quit;
            exit($code);
        }
    }

    /**
     * Logs data to stderr
     * @param string $message
     * @param bool $nl
     * @param bool|int $quit
     */
    public static function stderr($message, $nl = true, $quit = -200)
    {
        fwrite(\STDERR, self::color('[ERROR] ', 'red') . $message . ($nl ? PHP_EOL : ''));
        fflush(\STDOUT);

        if (($isTrue = true === $quit) || \is_int($quit)) {
            $code = $isTrue ? 0 : $quit;
            exit($code);
        }
    }

    /**
     * Returns true if STDOUT supports colorization.
     * This code has been copied and adapted from
     * \Symfony\Component\Console\Output\OutputStream.
     * @return boolean
     */
    public static function isSupportColor()
    {
        return SysEnv::isSupportColor();
    }

    /**
     * Parses $GLOBALS['argv'] for parameters and assigns them to an array.
     * Supports:
     * -e
     * -e <value>
     * --long-param
     * --long-param=<value>
     * --long-param <value>
     * <value>
     * @link https://github.com/inhere/php-console/blob/master/src/io/Input.php
     * @param array $noValues List of parameters without values
     * @param bool $mergeOpts
     * @return array
     */
    public static function parseArgv(array $noValues = [], $mergeOpts = false)
    {
        $params = $GLOBALS['argv'];
        reset($params);

        $args = $sOpts = $lOpts = [];
        $fullScript = implode(' ', $params);
        $script = array_shift($params);

        // each() will deprecated at 7.2 so,there use current and next instead it.
        // while (list(,$p) = each($params)) {
        while (false !== ($p = current($params))) {
            next($params);

            // is options
            if ($p{0} === '-') {
                $isLong = false;
                $opt = substr($p, 1);
                $value = true;

                // long-opt: (--<opt>)
                if ($opt{0} === '-') {
                    $isLong = true;
                    $opt = substr($opt, 1);

                    // long-opt: value specified inline (--<opt>=<value>)
                    if (strpos($opt, '=') !== false) {
                        list($opt, $value) = explode('=', $opt, 2);
                    }

                    // short-opt: value specified inline (-<opt>=<value>)
                } elseif (\strlen($opt) > 2 && $opt{1} === '=') {
                    list($opt, $value) = explode('=', $opt, 2);
                }

                // check if next parameter is a descriptor or a value
                $nxp = current($params);

                if ($value === true && $nxp !== false && $nxp{0} !== '-' && !\in_array($opt, $noValues, true)) {
                    // list(,$value) = each($params);
                    $value = current($params);
                    next($params);

                    // short-opt: bool opts. like -e -abc
                } elseif (!$isLong && $value === true) {
                    foreach (str_split($opt) as $char) {
                        $sOpts[$char] = true;
                    }

                    continue;
                }

                if ($isLong) {
                    $lOpts[$opt] = $value;
                } else {
                    $sOpts[$opt] = $value;
                }

                // arguments: param doesn't belong to any option, define it is args
            } else {
                // value specified inline (<arg>=<value>)
                if (strpos($p, '=') !== false) {
                    list($name, $value) = explode('=', $p, 2);
                    $args[$name] = $value;
                } else {
                    $args[] = $p;
                }
            }
        }

        unset($params);

        if ($mergeOpts) {
            return [$fullScript, $script, $args, array_merge($sOpts, $lOpts)];
        }

        return [$fullScript, $script, $args, $sOpts, $lOpts];
    }

}
