<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-28
 * Time: 9:20
 */

namespace MyLib\Helpers\Traits;

/**
 * Class NameAliasTrait
 * @package MyLib\Helpers\Traits
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
    public function hasAlias($alias)
    {
        return isset($this->aliases[$alias]);
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}
