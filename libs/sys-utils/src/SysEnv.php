<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/9
 * Time: 下午10:06
 */

namespace Toolkit\SysUtil;

/**
 * Class EnvHelper
 * @package Toolkit\SysUtil
 */
class SysEnv
{
    /**************************************************************************
     * system env
     *************************************************************************/

    /**
     * @return bool
     */
    public static function isUnix(): bool
    {
        $uNames = ['CYG', 'DAR', 'FRE', 'HP-', 'IRI', 'LIN', 'NET', 'OPE', 'SUN', 'UNI'];

        return \in_array(strtoupper(substr(PHP_OS, 0, 3)), $uNames, true);
    }

    /**
     * @return bool
     */
    public static function isLinux(): bool
    {
        return stripos(PHP_OS, 'LIN') !== false;
    }

    /**
     * @return bool
     */
    public static function isWin(): bool
    {
        return self::isWindows();
    }

    /**
     * @return bool
     */
    public static function isWindows(): bool
    {
        return stripos(PHP_OS, 'WIN') !== false;
    }

    /**
     * @return bool
     */
    public static function isMac(): bool
    {
        return stripos(PHP_OS, 'Darwin') !== false;
    }

    /**
     * @return bool
     */
    public static function isRoot(): bool
    {
        if (\function_exists('posix_getuid')) {
            return posix_getuid() === 0;
        }

        return getmyuid() === 0;
    }

    /**
     * @return string
     */
    public static function getHostname(): string
    {
        return php_uname('n');
    }

    /**
     * @return string
     */
    public static function getNullDevice(): string
    {
        if (self::isUnix()) {
            return '/dev/null';
        }

        return 'NUL';
    }

    /**
     * Returns true if STDOUT supports colorization.
     * This code has been copied and adapted from
     * \Symfony\Component\Console\Output\OutputStream.
     * @return boolean
     */
    public static function isSupportColor(): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return
                '10.0.10586' === PHP_WINDOWS_VERSION_MAJOR . '.' . PHP_WINDOWS_VERSION_MINOR . '.' . PHP_WINDOWS_VERSION_BUILD
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM')// || 'cygwin' === getenv('TERM')
                ;
        }

        if (!\defined('STDOUT')) {
            return false;
        }

        return self::isInteractive(STDOUT);
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     * @param  int|resource $fileDescriptor
     * @return boolean
     */
    public static function isInteractive($fileDescriptor): bool
    {
        return \function_exists('posix_isatty') && @posix_isatty($fileDescriptor);
    }

}
