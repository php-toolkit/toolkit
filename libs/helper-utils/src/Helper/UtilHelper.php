<?php

namespace Toolkit\Helper;

/**
 * Class UtilHelper
 * @package Toolkit\Helper
 */
abstract class UtilHelper
{
    /**
     * Display a var dump in firebug console
     * @param mixed  $object Object to display
     * @param string $type
     */
    public static function fd($object, $type = 'log'): void
    {
        $types = ['log', 'debug', 'info', 'warn', 'error', 'assert'];

        if (!\in_array($type, $types, true)) {
            $type = 'log';
        }

        $data = json_encode($object);

        echo '<script type="text/javascript">console.' . $type . '(' . $data . ');</script>';
    }

    /**
     * @param string     $pathname
     * @param int|string $projectId This must be a one character
     * @return int|string
     * @throws \LogicException
     */
    public static function ftok($pathname, $projectId)
    {
        if (\strlen($projectId) > 1) {
            throw new \LogicException("the project id must be a one character(int/str). Input: $projectId");
        }

        if (\function_exists('ftok')) {
            return ftok($pathname, $projectId);
        }

        if (!$st = @stat($pathname)) {
            return -1;
        }

        $key = sprintf('%u', ($st['ino'] & 0xffff) | (($st['dev'] & 0xff) << 16) | (($projectId & 0xff) << 24));

        return $key;
    }
}
