<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/29 0029
 * Time: 22:03
 */

namespace Toolkit\ObjUtil\Traits;

use Toolkit\ObjUtil\Exception\GetPropertyException;
use Toolkit\ObjUtil\Exception\PropertyException;
use Toolkit\ObjUtil\Exception\SetPropertyException;

/**
 * trait PropertyAccessByGetterSetterTrait
 * @package Toolkit\ObjUtil\Traits
 * ```
 * class A
 * {
 *     use PropertyAccessByGetterSetterTrait;
 * }
 * ```
 */
trait PropertyAccessByGetterSetterTrait
{
    /**
     * @reference yii2 yii\base\Object::__set()
     * @param $name
     * @param $value
     * @throws SetPropertyException
     */
    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);

        if (\method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (\method_exists($this, 'get' . ucfirst($name))) {
            throw new SetPropertyException('Setting a Read-only property! ' . \get_class($this) . "::{$name}");
        } else {
            throw new SetPropertyException('Setting a Unknown property! ' . \get_class($this) . "::{$name}");
        }
    }

    /**
     * @reference yii2 yii\base\Object::__set()
     * @param $name
     * @throws GetPropertyException
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);

        if (\method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (\method_exists($this, 'set' . ucfirst($name))) {
            throw new GetPropertyException('Getting a Write-only property! ' . \get_class($this) . "::{$name}");
        }

        throw new GetPropertyException('Getting a Unknown property! ' . \get_class($this) . "::{$name}");
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . ucfirst($name);

        if (\method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * @param $name
     * @throws \Toolkit\ObjUtil\Exception\PropertyException
     */
    public function __unset($name)
    {
        $setter = 'set' . ucfirst($name);

        if (\method_exists($this, $setter)) {
            $this->$setter(null);

            return;
        }

        throw new PropertyException('Unset an unknown or read-only property: ' . \get_class($this) . '::' . $name);
    }

}
