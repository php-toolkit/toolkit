<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-28
 * Time: 9:20
 */

namespace Toolkit\DI;

/**
 * Class NameAliasTrait
 * @package Toolkit\DI
 */
trait NameAliasTrait
{
    /**
     * 别名
     * @var array
     * [
     *  'alias name' => 'id',
     *  'alias name2' => 'id'
     * ]
     */
    private $aliases = [];

    /**
     * set name alias
     * @param string       $name
     * @param array|string $alias
     */
    public function setAlias(string $name, $alias)
    {
        if (!$name || !$alias) {
            return;
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
    public function isAlias(string $alias): bool
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
    public function setAliases(array $aliases): self
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * @param array $aliases
     * @return $this
     */
    public function addAliases(array $aliases): self
    {
        $this->aliases = \array_merge($this->aliases, $aliases);

        return $this;
    }
}
