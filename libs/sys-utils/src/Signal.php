<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/5/4
 * Time: 上午10:55
 */

namespace Toolkit\Sys;

/**
 * Class Signal
 * @package Toolkit\Sys
 * @link http://php.net/manual/en/pcntl.constants.php
 */
class Signal
{
    public const IGN = 1;
    public const DFL = 0;
    public const ERR = -1;

    public const HUP    = 1;
    public const INT    = 2; // Ctrl+C
    public const QUIT   = 3;
    public const ILL    = 4;
    public const TRAP   = 5;
    public const ABRT   = 6;
    public const IOT    = 6;
    public const BUS    = 7;
    public const FPE    = 8;
    public const KILL   = 9;
    public const USR1   = 10;
    public const SEGV   = 11;
    public const USR2   = 12;
    public const PIPE   = 13;
    public const ALRM   = 14;
    public const TERM   = 15;
    public const STKFLT = 16;
    public const CLD    = 17;
    public const CHLD   = 17;
    public const CONT   = 18;
    public const STOP   = 19;
    public const TSTP   = 20;
    public const TTIN   = 21;
    public const TTOU   = 22;
    public const URG    = 23;
    public const XCPU   = 24;
    public const XFSZ   = 25;
    public const VTALRM = 26;
    public const PROF   = 27;
    public const WINCH  = 28;
    public const POLL   = 29;
    public const IO     = 29;
    public const PWR    = 30;
    public const SYS    = 31;
    public const BABY   = 31;
}
