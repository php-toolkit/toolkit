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
    const IGN = 1;
    const DFL = 0;
    const ERR = -1;

    const HUP = 1;
    const INT = 2; // Ctrl+C
    const QUIT = 3;
    const ILL = 4;
    const TRAP = 5;
    const ABRT = 6;
    const IOT = 6;
    const BUS = 7;
    const FPE = 8;
    const KILL = 9;
    const USR1 = 10;
    const SEGV = 11;
    const USR2 = 12;
    const PIPE = 13;
    const ALRM = 14;
    const TERM = 15;
    const STKFLT = 16;
    const CLD = 17;
    const CHLD = 17;
    const CONT = 18;
    const STOP = 19;
    const TSTP = 20;
    const TTIN = 21;
    const TTOU = 22;
    const URG = 23;
    const XCPU = 24;
    const XFSZ = 25;
    const VTALRM = 26;
    const PROF = 27;
    const WINCH = 28;
    const POLL = 29;
    const IO = 29;
    const PWR = 30;
    const SYS = 31;
    const BABY = 31;
}
