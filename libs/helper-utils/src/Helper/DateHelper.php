<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 14-9-25
 * Time: 10:35
 * Uesd: 主要功能是 hi
 */

namespace Toolkit\Helper;

/**
 * Class DateHelper
 * @package Toolkit\Helper
 */
class DateHelper
{
    /**
     * 判断给定的 字符串 是否是个 时间戳
     * @param int $timestamp 时间戳
     * @return bool|string datetime
     */
    public static function isTimestamp($timestamp)
    {
        if (!$timestamp || !is_numeric($timestamp) || 10 !== \strlen($timestamp)) {
            return false;
        }

        return date('Ymd', $timestamp) ? true : false;
    }

    /**
     * 校验值是否是日期格式
     * @param string $date 日期
     * @return boolean
     */
    public static function isDate($date): bool
    {
        // strtotime转换不对，日期格式显然不对。
        return strtotime($date) ? true : false;
    }

    /**
     * 校验值是否是日期并且是否满足设定格式
     * @param string $date 日期
     * @param string $format 需要检验的格式数组
     * @return boolean
     */
    public static function isDateFormat($date, $format = 'Y-m-d'): bool
    {
        if (!$unixTime = strtotime($date)) {
            return false;
        }

        // 校验日期的格式有效性
        if (date($format, $unixTime) === $date) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public static function todayStart(): int
    {
        //        return strtotime(date('Y-m-d 00:00:00'));
        return strtotime('today 00:00:00');
    }

    /**
     * @return int
     */
    public static function todayEnd(): int
    {
        //        return strtotime(date('Y-m-d 23:59:59'));
        return strtotime('today 23:59:59');
    }

    /**
     * @return false|int
     */
    public static function tomorrowBegin()
    {
        return mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
    }

    /**
     * @return int
     */
    public static function tomorrowStart(): int
    {
        return strtotime('+1 day 00:00:00');
    }

    /**
     * @return int
     */
    public static function tomorrowEnd(): int
    {
        return strtotime('+1 day 23:59:59');
    }

    /**
     * @return false|int
     */
    public static function tomorrow()
    {
        return strtotime('+1 day');
    }

    //获取指定日期所在月的第一天和最后一天
    public static function getTheMonth($date): array
    {
        $firstDay = date('Y-m-01', strtotime($date));
        $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));

        return [$firstDay, $lastDay];
    }

    //获取指定日期上个月的第一天和最后一天
    public static function getPurMonth($date): array
    {
        $time = strtotime($date);
        $firstDay = date('Y-m-01', strtotime(date('Y', $time) . '-' . (date('m', $time) - 1) . '-01'));
        $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));

        return [$firstDay, $lastDay];
    }

    //获取指定日期下个月的第一天和最后一天
    public static function getNextMonth($date): array
    {
        $arr = getdate();

        if ($arr['mon'] === 12) {
            $year = $arr['year'] + 1;
            $month = $arr['mon'] - 11;
            $day = $arr['mday'];

            $mday = $day < 10 ? '0' . $day : $day;

            $firstDay = $year . '-0' . $month . '-01';
            $lastDay = $year . '-0' . $month . '-' . $mday;
        } else {
            $time = strtotime($date);
            $firstDay = date('Y-m-01', strtotime(date('Y', $time) . '-' . (date('m', $time) + 1) . '-01'));
            $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
        }

        return [$firstDay, $lastDay];
    }

    /**
     * 获得几天前，几小时前，几月前
     * @param            $time
     * @param null|array $unit
     * @return string
     */
    public static function before($time, $unit = null)
    {
        if (!\is_int($time)) {
            return false;
        }

        $unit = $unit ?: ['年', '月', '星期', '日', '小时', '分钟', '秒'];
        $nowTime = time();
        $diffTime = $nowTime - $time;

        switch (true) {
            case $time < ($nowTime - 31536000):
                return floor($diffTime / 31536000) . $unit[0];
            case $time < ($nowTime - 2592000):
                return floor($diffTime / 2592000) . $unit[1];
            case $time < ($nowTime - 604800):
                return floor($diffTime / 604800) . $unit[2];
            case $time < ($nowTime - 86400):
                return floor($diffTime / 86400) . $unit[3];
            case $time < ($nowTime - 3600):
                return floor($diffTime / 3600) . $unit[4];
            case $time < ($nowTime - 60):
                return floor($diffTime / 60) . $unit[5];
            default:
                return floor($diffTime) . $unit[6];
        }
    }

}
/*
$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
$lastmonth = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
$nextyear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")+1);

echo strtotime("now"), "\n";
echo strtotime("10 September 2000"), "\n";
echo strtotime("+1 day"), "\n";
echo strtotime("+1 week"), "\n";
echo strtotime("+1 week 2 days 4 hours 2 seconds"), "\n";
echo strtotime("next Thursday"), "\n";
echo strtotime("last Monday"), "\n";

*/
