<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/15
 * Time: 上午10:04
 */

namespace Toolkit\PhpUtil;

/**
 * Class PhpError
 * @package Toolkit\PhpUtil
 */
class PhpError
{
    /** @var array */
    public static $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    /**
     * $lastError = error_get_last();
     * @param array $lastError
     * @param null|string $catcher
     * @return array
     */
    public static function toArray(array $lastError, $catcher = null): array
    {
        $digest = 'Fatal Error (' . self::codeToString($lastError['type']) . '): ' . $lastError['message'];
        $data = [
            'code' => $lastError['type'],
            'message' => $lastError['message'],
            'file' => $lastError['file'],
            'line' => $lastError['line'],
            'catcher' => __METHOD__,
        ];

        if ($catcher) {
            $data['catcher'] = $catcher;
        }

        return [$digest, $data];
    }

    /**
     * @param int $code
     * @return string
     */
    public static function codeToString(int $code): string
    {
        switch ($code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return 'Unknown PHP error';
    }
}
