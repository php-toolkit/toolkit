<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/5/2
 * Time: 上午10:12
 */

namespace Toolkit\ObjUtil;

use Toolkit\ObjUtil\Traits\PropertyAccessByGetterSetterTrait;

/**
 * Class Configurable
 * @package Toolkit\ObjUtil
 */
class Configurable
{
    use PropertyAccessByGetterSetterTrait;

    /**
     * Configurable constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            Obj::init($this, $config);
        }

        $this->init();
    }

    /**
     * init
     */
    protected function init()
    {
        // init something ...
    }
}
