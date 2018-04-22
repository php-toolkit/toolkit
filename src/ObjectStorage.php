<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-01-30
 * Time: 14:56
 */

namespace MyLib\ObjUtil;

/**
 * Class ObjectStorage
 *  - 允许使用非对象作为key，会自动使用 \stdClass 转成对象
 * @package MyLib\ObjUtil
 */
class ObjectStorage extends \SplObjectStorage
{
    /**
     * @param mixed $key
     * @param null $data
     */
    public function attach($key, $data = null)
    {
        if (!\is_object($key)) {
            $raw = $key;
            $key = new \stdClass();
            $key->value = $raw;
        }

        parent::attach($key, $data);
    }

    /**
     * @param mixed $key
     */
    public function detach($key)
    {
        if (!\is_object($key)) {
            $raw = $key;
            $key = new \stdClass();
            $key->value = $raw;
        }

        parent::detach($key);
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function contains($key)
    {
        if (!\is_object($key)) {
            $raw = $key;
            $key = new \stdClass();
            $key->value = $raw;
        }

        return parent::contains($key);
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->contains($key);
    }
}