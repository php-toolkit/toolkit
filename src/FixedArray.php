<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-26
 * Time: 9:18
 */

namespace Toolkit\Collection;

/**
 * Class FixedArray
 *  fixed size array implements, and support string key.
 *  `SplFixedArray` only allow int key.
 * @package Toolkit\Collection
 */
class FixedArray implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array
     * [
     *  'string:key' => 'int:value index'
     * ]
     */
    protected $keys;

    /**
     * @var \SplFixedArray
     */
    protected $values;

    /**
     * FixedArray constructor.
     * @param int $size
     */
    public function __construct(int $size = 0)
    {
        $this->keys = [];
        $this->values = new \SplFixedArray($size);
    }

    /**
     * reset
     * @param int $size
     */
    public function reset(int $size = 0)
    {
        $this->keys = [];
        $this->values = new \SplFixedArray($size);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset(string $key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->values->getSize();
    }

    /**
     * @param $key
     * @return int
     */
    public function getKeyIndex($key): int
    {
        return $this->keys[$key] ?? -1;
    }

    /**
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param array $keys
     */
    public function setKeys(array $keys)
    {
        $this->keys = $keys;
    }

    /**
     * @return \SplFixedArray
     */
    public function getValues(): \SplFixedArray
    {
        return $this->values;
    }

    /**
     * @param \SplFixedArray $values
     */
    public function setValues(\SplFixedArray $values)
    {
        $this->values = $values;
    }

    /**
     * Defined by IteratorAggregate interface
     * Returns an iterator for this object, for use with foreach
     * @return \SplFixedArray
     */
    public function getIterator()
    {
        return $this->values;
    }

    /**
     * Checks whether an offset exists in the iterator.
     * @param   mixed $offset The array offset.
     * @return  boolean  True if the offset exists, false otherwise.
     */
    public function offsetExists($offset)
    {
        return isset($this->keys[$offset]);
    }

    /**
     * Gets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  mixed  The array value if it exists, null otherwise.
     */
    public function offsetGet($offset)
    {
        $index = $this->getKeyIndex($offset);

        if ($index >= 0) {
            return $this->values[$index];
        }

        return null;
    }

    /**
     * Sets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @param   mixed $value The array value.
     * @return  void
     */
    public function offsetSet($offset, $value)
    {
        $index = $this->getSize();

        // change size.
        $this->values->setSize($index + 1);

        $this->values[$index] = $value;
        $this->keys[$offset] = $index;
    }

    /**
     * Unset an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        $index = $this->getKeyIndex($offset);

        if ($index >= 0) {
            // change size.
            $this->values->setSize($index - 1);

            unset($this->keys[$offset], $this->values[$index]);
        }
    }
}
