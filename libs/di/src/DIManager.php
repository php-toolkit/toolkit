<?php
/**
 * @author Inhere
 * ContainerManager.php
 * Date : 2014-7-10
 */

namespace MyLib\DI;

/**
 * Class DIManager - Container Manager
 * @package MyLib\DI
 */
final class DIManager
{
    /**
     * 一组容器的扼要描述，标记不同的容器(这里一组含有一个基础容器和其他的属于这组[$group]的其它子容器)
     * @var string $group
     */
    private static $defaultGroup = 'di';

    /**
     * $containers 容器列表
     * @var array
     */
    private static $containers = [
        'di' => [
            'root' => null,// 'container name'=> a base Container instance
            'children' => []
        ]
    ];

    /**
     * @return Container
     */
    public static function getDefault()
    {
        return self::make('root', self::$defaultGroup);
    }

    /**
     * @param null|string $name
     * @return Container
     */
    public static function getContainer(string $name = null)
    {
        return self::make($name);
    }

    /**
     * @param string $name
     * @param string $group
     * @return Container
     */
    public static function make(string $name = null, string $group = null)
    {
        $group = $group ?: self::$defaultGroup;

        // No name, return default's base container.
        if (!$name) {
            if (empty(self::$containers[$group]['root'])) {
                $container = new Container;
                $container->name = 'di.root';

                self::$containers[$group]['root'] = $container;
            }

            return self::$containers[$group]['root'];
        }

        // Has name, we return children container.
        if (empty(self::$containers[$group][$name]) || !(self::$containers[$group][$name] instanceof Container)) {
            self::$containers[$group][$name] = new Container([], self::make(null, $group));
            self::$containers[$group][$name]->name = $name;
        }

        return self::$containers[$group][$name];
    }

    /**
     * setProfile
     * @param string $group
     * @return  void
     */
    public static function setDefaultGroup($group = 'di')
    {
        $group = strtolower(trim($group));

        if (!isset(static::$containers[$group])) {
            static::$containers[$group] = [
                'root' => null,
                'children' => []
            ];
        }

        static::$defaultGroup = $group;
    }

    /**
     * Method to get property Profile
     * @return  string
     */
    public static function getDefaultGroup()
    {
        return static::$defaultGroup;
    }

    /**
     * reset
     * @param string $group
     */
    public static function reset($group = null)
    {
        if (!$group) {
            static::$containers = [];
        } else {
            static::$containers[$group] = [];
        }
    }
}
