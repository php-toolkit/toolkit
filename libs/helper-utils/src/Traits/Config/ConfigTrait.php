<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2015/2/7
 * Time: 19:14
 * Use :
 * File: TraitUseOption.php
 */

namespace Toolkit\Traits\Config;

use Toolkit\ArrUtil\Arr;

/**
 * Class ConfigTrait
 * @package Toolkit\Traits\Config
 * @property array $config 必须在使用的类定义此属性, 在 Trait 中已定义的属性，在使用 Trait 的类中不能再次定义
 */
trait ConfigTrait
{
    /**
     * @param $name
     * @return bool
     */
    public function hasConfig($name): bool
    {
        return array_key_exists($name, $this->config);
    }

    /**
     * Method to get property Options
     * @param   string $name
     * @param   mixed $default
     * @return  mixed
     */
    public function getValue(string $name, $default = null)
    {
        $value = Arr::getByPath($this->config, $name, $default);

        if ($value && $value instanceof \Closure) {
            $value = $value();
        }

        return $value;
    }

    /**
     * Method to set property config
     * @param   string $name
     * @param   mixed $value
     * @return  static  Return self to support chaining.
     */
    public function setValue($name, $value)
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * delete a option
     * @param $name
     * @return mixed|null
     */
    public function delValue($name)
    {
        $value = null;

        if ($this->hasConfig($name)) {
            $value = $this->getValue($name);

            unset($this->config[$name]);
        }

        return $value;
    }

    /**
     * Method to get property Options
     * @param string|null $key
     * @return array
     */
    public function getConfig(string $key = null): array
    {
        if ($key) {
            return $this->getValue($key);
        }

        return $this->config;
    }

    /**
     * Method to set property config
     * @param  array $config
     * @param  bool $loopMerge
     * @return static Return self to support chaining.
     */
    public function setConfig(array $config, $loopMerge = true)
    {
        $this->config = $loopMerge ? Arr::merge($this->config, $config) : $config;

        return $this;
    }
}
