<?php
/**
 * @from slim 3
 * @license   MIT
 */

namespace Toolkit\DI;

use Inhere\Middleware\CallableResolverInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * This class resolves a string of the format 'class:method' into a closure
 * that can be dispatched.
 */
final class CallableResolver implements CallableResolverInterface
{
    public const CALLABLE_PATTERN = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';

    /**
     * @var ContainerInterface|Container
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve toResolve into a closure that that the router can dispatch.
     *
     * If toResolve is of the format 'class:method', then try to extract 'class'
     * from the container otherwise instantiate it and then dispatch 'method'.
     *
     * @param mixed $toResolve
     *
     * @return callable
     *
     * @throws RuntimeException if the callable does not exist
     * @throws RuntimeException if the callable is not resolvable
     */
    public function resolve($toResolve): callable
    {
        if (\is_callable($toResolve)) {
            return $toResolve;
        }

        if (!\is_string($toResolve)) {
            $this->assertCallable($toResolve);
        }

        // check for slim callable as "class:method"
        if (preg_match(self::CALLABLE_PATTERN, $toResolve, $matches)) {
            $resolved = $this->resolveCallable($matches[1], $matches[2]);
            $this->assertCallable($resolved);

            return $resolved;
        }

        $resolved = $this->resolveCallable($toResolve);
        $this->assertCallable($resolved);

        return $resolved;
    }

    /**
     * Check if string is something in the DIC
     * that's callable or is a class name which has an __invoke() method.
     *
     * @param string $class
     * @param string $method
     * @return callable
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException if the callable does not exist
     */
    private function resolveCallable($class, $method = '__invoke'): callable
    {
        if ($cb = $this->container->getIfExist($class)) {
            return [$cb, $method];
        }

        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Callable %s does not exist', $class));
        }

        return [new $class($this->container), $method];
    }

    /**
     * @param Callable $callable
     *
     * @throws \RuntimeException if the callable is not resolvable
     */
    private function assertCallable($callable)
    {
        if (!\is_callable($callable)) {
            throw new RuntimeException(sprintf(
                '%s is not resolvable',
                \is_array($callable) || \is_object($callable) ? json_encode($callable) : $callable
            ));
        }
    }
}
