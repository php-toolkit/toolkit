<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-28
 * Time: 9:20
 */

namespace MyLib\DI;

/**
 * Class NameAliasTrait
 * @package MyLib\DI
 * @property array $aliases path alias array
 */
trait NameAliasTrait
{
    // protected $aliases = [];

    /**
     * set/get name alias
     * @param array|string $name
     * @param array|string|null $alias
     * @return bool|string
     */
    public function alias($name, $alias = null)
    {
        if (!$name) {
            return false;
        }

        if (\is_string($name)) {
                // get real name for $name
            if (!$alias) {
                return $this->resolveAlias($name);
            }

            // setting
            if (\is_array($alias)) {
                foreach ($alias as $aliasName) {
                    if (!isset($this->aliases[$aliasName])) {
                        $this->aliases[$aliasName] = $name;
                    }
                }
            } else {
                $this->aliases[$alias] = $name;
            }

        // setting
        } elseif (\is_array($name)) {
            foreach ($name as $a => $n) {
                $this->aliases[$a] = $n;
            }
        }

        return true;
    }

    /**
     * @param string $alias
     * @return mixed
     */
    public function resolveAlias(string $alias): string
    {
        return $this->aliases[$alias] ?? $alias;
    }

    /**
     * @param $alias
     * @return bool
     */
    public function hasAlias(string $alias): bool
    {
        return isset($this->aliases[$alias]);
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param array $aliases
     * @return $this
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;

        return $this;
    }
}
