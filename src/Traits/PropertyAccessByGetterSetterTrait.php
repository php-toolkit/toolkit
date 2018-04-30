<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/29 0029
 * Time: 22:03
 */

namespace Toolkit\ObjUtil\Traits;

use Inhere\Exceptions\GetPropertyException;
use Inhere\Exceptions\NotFoundException;
use Inhere\Exceptions\SetPropertyException;

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
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . ucfirst($name);

        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * @param $name
     * @throws NotFoundException
     */
    public function __unset($name)
    {
        $setter = 'set' . ucfirst($name);

        if (method_exists($this, $setter)) {
            $this->$setter(null);

            return;
        }

        throw new NotFoundException('Unset an unknown or read-only property: ' . \get_class($this) . '::' . $name);
    }

    /**
     * @reference yii2 yii\base\Object::__set()
     * @param $name
     * @param $value
     * @throws SetPropertyException
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            $this->$method($value);
        } elseif (method_exists($this, 'get' . ucfirst($name))) {
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
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (method_exists($this, 'set' . ucfirst($name))) {
            throw new GetPropertyException('Getting a Write-only property! ' . \get_class($this) . "::{$name}");
        }

        throw new GetPropertyException('Getting a Unknown property! ' . \get_class($this) . "::{$name}");
    }
}
