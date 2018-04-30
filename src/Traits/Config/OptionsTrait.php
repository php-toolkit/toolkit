<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2015/2/7
 * Time: 19:14
 * Use :
 * File: TraitUseOption.php
 */

namespace Toolkit\SimpleConfig;

use Toolkit\ArrUtil\Arr;

/**
 * Class OptionsTrait
 * @package Toolkit\SimpleConfig
 * @property array $options 必须在使用的类定义此属性, 在 Trait 中已定义的属性，在使用 Trait 的类中不能再次定义
 */
trait OptionsTrait
{
    /**
     * @param $name
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Method to get property Options
     * @param   string $name
     * @param   mixed $default
     * @return  mixed
     */
    public function getOption(string $name, $default = null)
    {
        $value = array_key_exists($name, $this->options) ? $this->options[$name] : $default;

        if ($value && ($value instanceof \Closure)) {
            $value = $value();
        }

        return $value;
    }

    /**
     * Method to set property options
     * @param   string $name
     * @param   mixed $value
     * @return  static  Return self to support chaining.
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * delete a option
     * @param $name
     * @return mixed|null
     */
    public function delOption($name)
    {
        $value = null;

        if ($this->hasOption($name)) {
            $value = $this->getOption($name);

            unset($this->options[$name]);
        }

        return $value;
    }

    /**
     * Method to get property Options
     * @return  array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Method to set property options
     * @param  array $options
     * @param  bool $merge
     * @return static Return self to support chaining.
     */
    public function setOptions(array $options, $merge = true)
    {
        $this->options = $merge ? Arr::merge($this->options, $options) : $options;

        return $this;
    }
}
