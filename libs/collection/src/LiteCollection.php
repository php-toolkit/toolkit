<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/21
 * Time: 下午2:04
 */

namespace MyLib\Collection;

/**
 * Class LiteCollection
 * @package Inhere\Http
 */
class LiteCollection extends \ArrayObject implements CollectionInterface
{
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
        parent::__construct();

        $this->replace($items);
    }

    /**
     * @param string $name
     * @param null|mixed $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed|null
     */
    public function add($name, $value)
    {
        if (isset($this[$name])) {
            return null;
        }

        $this[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed|null
     */
    public function set($name, $value)
    {
        return $this[$name] = $value;
    }

    /**
     * @param array $items
     */
    public function replace(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
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

        foreach ($this as $key => $value) {
            $data[$key] = $callback($value, $key);
            unset($this[$key]);
        }

        return new static($data);
    }

    /**
     * @param string $char
     * @return string
     */
    public function implode($char = ',')
    {
//        $string = '';
//
//        foreach ($this as $key => $value) {
//            $string .= is_array($value) ? $this->implode($char, $value) : implode($char, $value);
//            $string .= implode($char, $value);
//        }

        return implode($char, $this->all());
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        return $this->getArrayCopy();
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        if (isset($this[$key])) {
            $val = $this[$key];
            unset($this[$key]);

            return $val;
        }

        return null;
    }

    /**
     * clear all data
     */
    public function clear()
    {
        foreach ($this as $key) {
            unset($this[$key]);
        }
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}
