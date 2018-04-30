<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-28
 * Time: 9:22
 */

namespace Toolkit\Traits;

/**
 * Class LiteContainerTrait
 * @package Toolkit\Traits
 */
trait LiteContainerTrait
{
    /**
     * all raw register service list
     * @var array
     */
    private static $services = [];

    /**
     * all parsed instance list by call service.
     * @var array
     */
    private static $instances = [];

    /**********************************************************
     * application service
     **********************************************************/

    /**
     * register a app service
     * @param string $name the service name
     * @param mixed $service service
     * @param bool $replace replace exists service
     * @return static
     */
    public function register($name, $service, $replace = false)
    {
        $this->set($name, $service, $replace);

        return $this;
    }

    /**
     * register a app service
     * @param string $name
     * @param mixed $service service
     * @param bool $replace replace exists service
     * @return bool
     * @throws \LogicException
     */
    public function set($name, $service, $replace = false)
    {
        // have been used.
        if (isset(self::$instances[$name])) {
            throw new \LogicException("The service [$name] have been instanced, don't allow override it.");
        }

        // setting
        if ($replace || !isset(self::$services[$name])) {
            self::$services[$name] = $service;
        }

        return true;
    }

    /**
     * get a app service by name
     * if is a closure, only run once.
     * @param string $name
     * @param bool $call if service is 'Closure', call it.
     * @return mixed
     * @throws \RuntimeException
     */
    public function get($name, $call = true)
    {
        if (!isset(self::$services[$name])) {
            throw new \RuntimeException("The service [$name] don't register.");
        }

        $service = self::$services[$name];

        if (\is_object($service) && $service instanceof \Closure && $call) {
            if (!isset(self::$instances[$name])) {
                self::$instances[$name] = $service($this);
            }

            return self::$instances[$name];
        }

        return $service;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \RuntimeException
     */
    public function raw($name)
    {
        if (!isset(self::$services[$name])) {
            throw new \RuntimeException("The service [$name] don't register.");
        }

        return self::$services[$name];
    }

    /**
     * create a app service by name
     * it always return a new instance.
     * @param string $name
     * @return mixed
     * @throws \RuntimeException
     */
    public function factory($name)
    {
        if (!isset(self::$services[$name])) {
            throw new \RuntimeException("The service [$name] don't register.");
        }

        $service = self::$services[$name];

        if (\is_object($service) && method_exists($service, '__invoke')) {
            return $service($this);
        }

        return $service;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset(self::$services[$name]);
    }

    /**
     * @return array
     */
    public function getServiceNames()
    {
        return array_keys(self::$services);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * allow register a app service by property
     * ```
     * $app->logger = function(){
     *     return new xx\yy\Logger;
     * };
     * ```
     * @param string $name
     * @param mixed $service
     * @return bool
     */
    public function __set($name, $service)
    {
        return $this->set($name, $service);
    }

    /**
     * allow call service by property
     * ```
     * $logger = $app->logger;
     * ```
     * @param  string $name service name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}
