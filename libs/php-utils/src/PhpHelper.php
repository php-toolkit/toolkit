<?php
/**
 *
 */

namespace Toolkit\PhpUtil;


/**
 * Class PhpHelper
 * @package Toolkit\PhpUtil
 */
class PhpHelper
{
    /**
     * get $_SERVER value
     * @param  string $name
     * @param  string $default
     * @return mixed
     */
    public static function serverParam(string $name, $default = '')
    {
        $name = \strtoupper($name);

        return $_SERVER[$name] ?? $default;
    }

    /**
     * @param callable|mixed $cb
     * @param array ...$args
     * @return mixed
     */
    public static function call($cb, ...$args)
    {
        if (\is_string($cb)) {
            // function
            if (\strpos($cb, '::') === false) {
                return $cb(...$args);
            }

            // ClassName::method
            $cb = \explode('::', $cb, 2);
        } elseif (\is_object($cb) && \method_exists($cb, '__invoke')) {
            return $cb(...$args);
        }

        if (\is_array($cb)) {
            list($obj, $mhd) = $cb;

            return \is_object($obj) ? $obj->$mhd(...$args) : $obj::$mhd(...$args);
        }

        return $cb(...$args);
    }

    /**
     * @param callable $cb
     * @param array $args
     * @return mixed
     */
    public static function callByArray(callable $cb, array $args)
    {
        return self::call($cb, ...$args);
    }

    /**
     * 获取资源消耗
     * @param int $startTime
     * @param int|float $startMem
     * @param array $info
     * @param bool $realUsage
     * @return array
     */
    public static function runtime($startTime, $startMem, array $info = [], $realUsage = false): array
    {
        $info['startTime'] = $startTime;
        $info['endTime'] = microtime(true);
        $info['endMemory'] = memory_get_usage($realUsage);

        // 计算运行时间
        $info['runtime'] = number_format(($info['endTime'] - $startTime) * 1000, 3) . 'ms';

        if ($startMem) {
            $startMem = array_sum(explode(' ', $startMem));
            $endMem = array_sum(explode(' ', $info['endMemory']));

            $info['memory'] = number_format(($endMem - $startMem) / 1024, 3) . 'kb';
        }

        $peakMem = memory_get_peak_usage(true) / 1024 / 1024;
        $info['peakMemory'] = number_format($peakMem, 3) . 'Mb';

        return $info;
    }

    /**
     * @return array
     */
    public static function getUserConstants(): array
    {
        $const = \get_defined_constants(true);

        return $const['user'] ?? [];
    }

    /**
     * dump vars
     * @param array ...$args
     * @return string
     */
    public static function dumpVars(...$args): string
    {
        ob_start();
        var_dump(...$args);
        $string = ob_get_clean();

        return \preg_replace("/=>\n\s+/", '=> ', $string);
    }

    /**
     * print vars
     * @param array ...$args
     * @return string
     */
    public static function printVars(...$args): string
    {
        $string = '';

        foreach ($args as $arg) {
            $string .= print_r($arg, 1) . PHP_EOL;
        }

        return \preg_replace("/Array\n\s+\(/", 'Array (', $string);
    }

    /**
     * @param $var
     * @return mixed
     */
    public static function exportVar($var)
    {
        return \var_export($var, true);
    }

}
