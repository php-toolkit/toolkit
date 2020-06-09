<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/4/15 0015
 * Time: 12:51
 */

namespace Toolkit\Helper;

/**
 * Class AssertHelper
 * @package Toolkit\Helper
 */
class AssertHelper
{
    /**
     * 检查字符串是否是正确的变量名
     * @param $string
     * @return bool
     */
    public static function isVarName($string): bool
    {
        return preg_match('@^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*@i', $string) === 1;
    }

}
