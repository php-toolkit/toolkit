<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 15-1-18
 * Time: 10:35
 * Used: 存放单个服务的相关信息
 * Service.php
 */

namespace Toolkit\DI;

use InvalidArgumentException;
use function method_exists;

/**
 * Class ObjectItem
 *
 * @package Toolkit\DI
 */
final class ObjectItem
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var mixed
     */
    private $instance;

    /**
     * @var array
     */
    private $arguments;

    /**
     * 锁定服务，不允许覆盖设置(一旦激活服务也会自动锁定)
     *
     * @var bool
     */
    private $locked;

    /**
     * 共享的服务，获取的总是第一次激活的服务实例(即是单例模式)
     *
     * @var bool
     */
    private $shared;

    /**
     * Service constructor.
     *
     * @param       $callback
     * @param array $arguments
     * @param bool  $shared
     * @param bool  $locked
     */
    public function __construct($callback, array $arguments = [], $shared = false, $locked = false)
    {
        $this->arguments = $arguments;

        $this->shared = (bool)$shared;
        $this->locked = (bool)$locked;

        $this->setCallback($callback);
    }

    /**
     * __clone
     */
    private function __clone()
    {
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        $this->instance = $this->callback = $this->arguments = null;
    }

    /**
     * @param Container $container
     * @param bool      $forceNew
     *
     * @return mixed|null
     */
    public function get(Container $container, $forceNew = false)
    {
        if ($this->shared) {
            if (!$this->instance || $forceNew) {
                $cb             = $this->callback;
                $this->instance = $cb($container);
            }

            // 激活后就锁定，不允许再覆盖设置服务
            $this->locked = true;
            return $this->instance;
        }

        $cb = $this->callback;

        return $cb($container);
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param $callback
     */
    public function setCallback($callback): void
    {
        if (!method_exists($callback, '__invoke')) {
            $this->instance = $callback;
            $callback       = function () use ($callback) {
                return $callback;
            };
        }

        $this->callback = $callback;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * 给服务设置参数，在获取服务实例前
     *
     * @param array $params 设置参数
     *
     * @throws InvalidArgumentException
     */
    public function setArguments(array $params): void
    {
        $this->arguments = $params;
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked($locked = true): void
    {
        $this->locked = (bool)$locked;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @param bool $shared
     */
    public function setShared($shared = true): void
    {
        $this->shared = (bool)$shared;
    }
}
