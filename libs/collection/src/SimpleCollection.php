<?php
/**
 * Simple data Collection
 */

namespace MyLib\Collection;

/**
 * Collection
 * This class provides a common interface used by many other
 * classes in a Inhere\Library application that manage "collections"
 * of data that must be inspected and/or manipulated
 */
class SimpleCollection implements CollectionInterface
{
    /**
     * The source data
     * @var array
     */
    protected $data = [];

    /**
     * @param array|null $items
     * @return static
     */
    public static function make($items = null)
    {
        return new static((array)$items);
    }

    /**
     * Create new collection
     * @param array $items Pre-populate collection with this key-value array
     */
    public function __construct(array $items = [])
    {
        $this->replace($items);
    }

    public function __destruct()
    {
        $this->clear();
    }

    /********************************************************************************
     * Collection interface
     *******************************************************************************/

    /**
     * Set collection item
     * @param string $key The data key
     * @param mixed $value The data value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function add($name, $value)
    {
        if (!$this->has($name)) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Get collection item for key
     * @param string $key The data key
     * @param mixed $default The default value to return if data key does not exist
     * @return mixed The key's value, or the default value
     */
    public function get(string $key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * Add item to collection
     * @param array $items Key-value array of data to append to this collection
     */
    public function replace(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param array $names
     * @return array
     */
    public function gets(array $names): array
    {
        $values = [];

        foreach ($names as $name) {
            $values[] = $this->get($name);
        }

        return $values;
    }

    /**
     * @param array $data
     * @return static
     */
    public function sets(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param callable $filter
     * @return static
     */
    public function reject(callable $filter)
    {
        $data = [];

        foreach ($this as $key => $value) {
            if (!$filter($value, $key)) {
                $data[$key] = $value;
            }

            unset($this[$key]);
        }

        return new static($data);
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback)
    {
        $data = [];

        foreach ($this->getIterator() as $key => $value) {
            $data[$key] = $callback($value, $key);
            unset($this[$key]);
        }

        return new static($data);
    }

    /**
     * @param string $char
     * @return string
     */
    public function implode(string $char = ','): string
    {
        return implode($char, $this->all());
    }

    /**
     * Get all items in collection
     * @return array The collection's source data
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * Get collection keys
     * @return array The collection's source data keys
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Does this collection have a given key?
     * @param string $key The data key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove item from collection
     * @param string $key The data key
     * @return mixed|null
     */
    public function remove($key)
    {
        $value = null;

        if ($this->has($key)) {
            $value = $this->data[$key];
            unset($this->data[$key]);
        }

        return $value;
    }

    /**
     * Remove all items from collection
     */
    public function clear()
    {
        $this->data = [];
    }

    /********************************************************************************
     * ArrayAccess interface
     *******************************************************************************/

    /**
     * Does this collection have a given key?
     * @param  string $key The data key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get collection item for key
     * @param string $key The data key
     * @return mixed The key's value, or the default value
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set collection item
     * @param string $key The data key
     * @param mixed $value The data value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Remove item from collection
     * @param string $key The data key
     * @return mixed|null
     */
    public function offsetUnset($key)
    {
        return $this->remove($key);
    }

    /********************************************************************************
     * Countable interface
     *******************************************************************************/

    /**
     * Get number of items in collection
     * @return int
     */
    public function count(): int
    {
        return \count($this->data);
    }

    /********************************************************************************
     * JsonSerializable interface
     *******************************************************************************/

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }

    /********************************************************************************
     * Serializable interface
     *******************************************************************************/

    /**
     * @return string
     */
    public function serialize(): string
    {
        return serialize($this->data);
    }

    /**
     * @param string $serialized
     * @param bool|array $allowedClasses
     */
    public function unserialize($serialized, $allowedClasses = false)
    {
        $this->data = unserialize($serialized, ['allowed_classes' => $allowedClasses]);
    }

    /********************************************************************************
     * IteratorAggregate interface
     *******************************************************************************/

    /**
     * Get collection iterator
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /********************************************************************************
     * Magic method
     ******************************************************************************/

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }
}
