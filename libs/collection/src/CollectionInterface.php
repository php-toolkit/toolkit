<?php

namespace Toolkit\Collection;

/**
 * Collection Interface
 */
interface CollectionInterface extends \Serializable, \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable
{
    public function set($key, $value);

    public function get(string $key, $default = null);

    /**
     * @param array $items
     */
    public function replace(array $items);

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param $key
     * @return mixed
     */
    public function remove($key);

    /**
     * clear all data
     */
    public function clear();
}
