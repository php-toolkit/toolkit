<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2015/3/14
 * Time: 19:44
 * Use : 跟 \stdClass 一样，多的功能是 -- 提供数组方式访问属性
 * File: ActiveData.php
 */

namespace Toolkit\Collection;

/**
 * Class ActiveData
 * @package Toolkit\Collection
 */
class ActiveData implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @param array|\ArrayAccess $data
     * @param bool|false $recursive
     * @return static
     */
    public static function create(array $data = [], $recursive = false)
    {
        return new static($data, $recursive);
    }

    /**
     * ActiveData constructor.
     * @param array $data
     * @param bool $recursive
     */
    public function __construct(array $data = [], $recursive = false)
    {
        if ($data) {
            $this->load($data, $recursive);
        }
    }

    /**
     * 初始化，载入数据
     * @param array $data
     * @param bool $recursive
     * @return $this
     */
    public function load($data, $recursive = false)
    {
        foreach ($data as $name => $value) {
            $name = trim($name);

            if (is_numeric($name)) {
                continue;
            }

            $this->$name = $recursive && \is_array($value) ? static::create($value, $recursive) : $value;
        }

        return $this;
    }

    public function isStrict()
    {
        return false;
    }

    /**
     * @param bool $toArray
     * @return array|\ArrayIterator
     */
    public function all($toArray = false)
    {
        $class = new \ReflectionClass($this);
        $attrs = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isPublic() && !$property->isStatic()) {
                $attrs[$property->getName()] = $property->getValue($this);
            }
        }

        //return $toArray ? $attrs : (new \ArrayObject($attrs));
        return $toArray ? $attrs : new \ArrayIterator($attrs);
    }

    /**
     * 以点连接 快速获取子级节点的值
     * @param string $name
     * @return ActiveData|null
     */
    public function get(string $name)
    {
        if (strpos($name, '.')) {
            $names = explode('.', $name);
            $node = $this;

            foreach ($names as $n) {
                if ($node instanceof self && property_exists($node, $n)) {
                    $node = $node->$n;
                } else {
                    if ($this->isStrict()) {
                        exit("Stored data don't exists node '$n'\n");
                    }

                    $node = null;
                    break;
                }
            }

            return $node;
        }

        return property_exists($this, $name) ? $this->$name : null;
    }

    /**
     * Defined by IteratorAggregate interface
     * Returns an iterator for this object, for use with foreach
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->all();
    }

    /**
     * Checks whether an offset exists in the iterator.
     * @param   mixed $offset The array offset.
     * @return  boolean  True if the offset exists, false otherwise.
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * Gets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  mixed  The array value if it exists, null otherwise.
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Sets an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @param   mixed $value The array value.
     * @return  void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __set($name, $value)
    {
    }

    public function __get($name)
    {
        return $this->get($name);
    }

}
