<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-29
 * Time: 14:20
 */

namespace Toolkit\Util;

use Inhere\Library\Helpers\PhpHelper;

/**
 * Class DeferredCallable
 * @package Toolkit\Util
 */
class DeferredCallable
{
    /** @var callable|string $callable */
    private $callable;

    /**
     * DeferredMiddleware constructor.
     * @param callable|string $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param array $args
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __invoke(...$args)
    {
        $callable = $this->callable;

        if (\is_callable($callable)) {
            return PhpHelper::call($callable, ...$args);
        }

        if (\is_string($callable) && class_exists($callable)) {
            $obj = new $callable;

            if (method_exists($obj, '__invoke')) {
                return $obj(...$args);
            }

            throw new \InvalidArgumentException('the defined callable is cannot be called!');
        }

        throw new \InvalidArgumentException('the defined callable is error!');
    }
}
