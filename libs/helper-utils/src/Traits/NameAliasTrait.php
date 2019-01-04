<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-28
 * Time: 9:20
 */

namespace Toolkit\Traits;

/**
 * Class NameAliasTrait
 * @package Toolkit\Traits
 * @property array $aliases path alias array
 */
trait NameAliasTrait
{
    // protected $aliases = [];

    /**
     * set/get name alias
     * @param array|string $name
     * @param string|null $alias
     * @return bool|string
     */
    public function alias($name, $alias = null)
    {
        // get real name for $id
        if (null === $alias) {
            return $this->resolveAlias($name);
        }

        foreach ((array)$alias as $aliasName) {
            if (!isset($this->aliases[$aliasName])) {
                $this->aliases[$aliasName] = $name;
            }
        }

        return true;
    }

    /**
     * @param string $alias
     * @return mixed
     */
    public function resolveAlias(string $alias)
    {
        return $this->aliases[$alias] ?? $alias;
    }

    /**
     * @param $alias
     * @return bool
     */
<<<<<<< HEAD
    public function hasAlias($alias): bool
=======
    public function hasAlias($alias)
>>>>>>> ec7510e9cfa02de7874c2a35fe5706305f2ac069
    {
        return isset($this->aliases[$alias]);
    }

    /**
     * @return array
     */
<<<<<<< HEAD
    public function getAliases(): array
=======
    public function getAliases()
>>>>>>> ec7510e9cfa02de7874c2a35fe5706305f2ac069
    {
        return $this->aliases;
    }
}
