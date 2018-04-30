<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-30
 * Time: 11:56
 */

namespace Toolkit\Traits;

use Toolkit\PhpUtil\PhpHelper;

/**
 * Class AopProxyAwareTrait
 * - AOP 切面编程
 * @package Toolkit\Helpers\Traits
 * @property array $proxyMap 要经过AOP代理的方法配置
 * e.g:
 * [
 *   'XyzClass::methodBefore' => [handler0, handler1],
 *   'XyzClass::methodAfter'  => [handler2, handler3],
 * ]
 */
trait AopProxyAwareTrait
{
    /**
     * @var array
     */
    private static $proxyPoints = ['before', 'after'];

    /**
     * @var mixed the proxy target is a class name or a object
     */
    private $proxyTarget;

    public function proxy($class, $method = null, array $args = [])
    {
        $this->proxyTarget = $class;

        if ($method) {
            return $this->call($method, $args);
        }

        return $this;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \LogicException
     */
    public function call($method, array $args = [])
    {
        if (!$target = $this->proxyTarget) {
            throw new \LogicException('Please setting the proxy target [proxyTarget]');
        }

        // on before exec method
        if ($cbList = $this->findProxyCallback($target, $method)) {
            foreach ($cbList as $cb) {
                PhpHelper::call($cb, $target, $method, $args);
            }
        }

        // exec method
        $ret = PhpHelper::call([$target, $method], ...$args);

        // on after exec method
        if ($cb = $this->findProxyCallback($target, $method, 'after')) {
            foreach ($cbList as $cb) {
                PhpHelper::call($cb, $target, $method, $args);
            }
        }

        // clear
        $this->proxyTarget = null;

        return $ret;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        return $this->call($method, $args);
    }

    /**
     * @param array ...$args
     * @return $this|mixed
     * @throws \InvalidArgumentException
     */
    public function __invoke(...$args)
    {
        $num = \count($args);

        // only a object
        if ($num === 1) {
            return $this->proxy($args[0]);
        }

        // has object and method
        if ($num > 1) {
            $class = array_shift($args);
            $method = array_shift($args);

            return $this->proxy($class, $method, $args);
        }

        throw new \InvalidArgumentException('Missing parameters!');
    }

    /**
     * @param $target
     * @param string $method
     * @param string $prefix
     * @return null|array
     */
    protected function findProxyCallback($target, $method, $prefix = 'before')
    {
        $className = \is_string($target) ? $target : \get_class($target);

        // e.g XyzClass::methodAfter
        $key = $className . '::' . $method . ucfirst($prefix);

        return $this->proxyMap[$key] ?? null;
    }

    /**
     * @see addProxy()
     * @param $key
     * @param $handler
     * @param string $position
     * @return $this
     */
    public function register($key, $handler, $position = 'before'): self
    {
        return $this->addProxy($key, $handler, $position);
    }

    /**
     * @param string $key eg 'XyzClass::method'
     * @param callable $handler
     * @param string $position 'before' 'after'
     * @return $this
     */
    public function addProxy($key, $handler, $position = 'before'): self
    {
        if (!\in_array($position, self::$proxyPoints, true)) {
            return $this;
        }

        $key .= ucfirst($position);
        $this->proxyMap[$key][] = $handler;

        return $this;
    }

    /**
     * @param array $map
     * @return $this
     */
    public function addProxies(array $map): self
    {
        foreach ($map as $key => $handler) {
            $position = 'before';

            if (\is_array($handler)) {
                if (!isset($handler['handler'])) {
                    continue;
                }

                $position = $handler['position'] ?? 'before';
                $handler = $handler['handler'];
            }

            $this->addProxy($key, $handler, $position);
        }

        return $this;
    }

    /**
     * @return array
     */
    public static function getProxyPoints(): array
    {
        return self::$proxyPoints;
    }

    /**
     * @return mixed
     */
    public function getProxyTarget()
    {
        return $this->proxyTarget;
    }

    /**
     * @return array
     */
    public function getProxyMap(): array
    {
        return $this->proxyMap;
    }

    /**
     * @param array $proxyMap
     */
    public function setProxyMap(array $proxyMap)
    {
        $this->proxyMap = $proxyMap;
    }
}
