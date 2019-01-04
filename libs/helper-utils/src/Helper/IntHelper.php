<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 16-07-25
 * Time: 10:35
 */

namespace Toolkit\Helper;

/**
 * Class IntHelper
 * @link http://cn2.php.net/manual/zh/function.pack.php#119402
 * @package Toolkit\Helper
 */
class IntHelper
{
    public static function int8($i)
    {
        return \is_int($i) ? pack('c', $i) : unpack('c', $i)[1];
    }

    public static function uInt8($i)
    {
        return \is_int($i) ? pack('C', $i) : unpack('C', $i)[1];
    }

    public static function int16($i)
    {
        return \is_int($i) ? pack('s', $i) : unpack('s', $i)[1];
    }

    public static function uInt16($i, $endianness = false)
    {
        $f = \is_int($i) ? 'pack' : 'unpack';

        if ($endianness === true) {
            // big-endian
            $i = $f('n', $i);
        } elseif ($endianness === false) {
            // little-endian
            $i = $f('v', $i);
        } elseif ($endianness === null) {
            // machine byte order
            $i = $f('S', $i);
        }

        return \is_array($i) ? $i[1] : $i;
    }

    public static function int32($i)
    {
        return \is_int($i) ? pack('l', $i) : unpack('l', $i)[1];
    }

    public static function uInt32($i, $endianness = false)
    {
        $f = \is_int($i) ? 'pack' : 'unpack';

        if ($endianness === true) {
            // big-endian
            $i = $f('N', $i);
        } elseif ($endianness === false) {
            // little-endian
            $i = $f('V', $i);
        } elseif ($endianness === null) {
            // machine byte order
            $i = $f('L', $i);
        }

        return \is_array($i) ? $i[1] : $i;
    }

    public static function int64($i)
    {
        return \is_int($i) ? pack('q', $i) : unpack('q', $i)[1];
    }

    public static function uInt64($i, $endianness = false)
    {
        $f = \is_int($i) ? 'pack' : 'unpack';

        if ($endianness === true) {
            // big-endian
            $i = $f('J', $i);
        } elseif ($endianness === false) {
            // little-endian
            $i = $f('P', $i);
        } elseif ($endianness === null) {
            // machine byte order
            $i = $f('Q', $i);
        }

        return \is_array($i) ? $i[1] : $i;
    }
}
