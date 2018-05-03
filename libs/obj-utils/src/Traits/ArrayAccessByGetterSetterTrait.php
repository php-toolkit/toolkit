<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/29 0029
 * Time: 22:03
 */

namespace Toolkit\ObjUtil\Traits;

/**
 * Class TraitArrayAccess
 * @package Toolkit\ObjUtil\Traits
 * ```
 * class A implements \ArrayAccess
 * {
 *     use ArrayAccessByGetterSetterTrait;
 * }
 * ```
 */
trait ArrayAccessByGetterSetterTrait
{
    /**
     * Checks whether an offset exists in the iterator.
     * @param   mixed $offset The array offset.
     * @return  boolean  True if the offset exists, false otherwise.
     */
    public function offsetExists($offset): bool
    {
        return \property_exists($this, $offset);
    }

    /**
     * Gets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  mixed  The array value if it exists, null otherwise.
     */
    public function offsetGet($offset)
    {
        $getter = 'get' . ucfirst($offset);

        if (\method_exists($this, $getter)) {
            $this->$getter();
        }

        return null;
    }

    /**
     * Sets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @param   mixed $value The array value.
     */
    public function offsetSet($offset, $value)
    {
        $setter = 'set' . ucfirst($offset);

        if (\method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }

    /**
     * Unset an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        // unset($this->$offset);
    }
}
