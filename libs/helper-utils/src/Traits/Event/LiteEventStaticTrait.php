<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-28
 * Time: 17:03
 */

namespace Toolkit\Traits\Event;

use Toolkit\PhpUtil\Php;

/**
 * Trait LiteEventStaticTrait - 简洁版的事件处理trait，一个事件只允许一个回调
 * @package Toolkit\Traits\Event
 */
trait LiteEventStaticTrait
{
    /**
     * @var array
     */
    private static $_events = [];

//////////////////////////////////////////////////////////////////////
/// events method
//////////////////////////////////////////////////////////////////////

    /**
     * register a event callback
     * @param string $name event name
     * @param callable $cb event callback
     * @param bool $replace replace exists's event cb
     */
    public static function on($name, callable $cb, $replace = false)
    {
        if ($replace || !isset(self::$_events[$name])) {
            self::$_events[$name] = $cb;
        }
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function fire($name, array $args = [])
    {
        if (!isset(self::$_events[$name]) || !($cb = self::$_events[$name])) {
            return null;
        }

        return Php::call($cb, ...$args);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function off($name)
    {
        $cb = null;

        if (isset(self::$_events[$name])) {
            $cb = self::$_events[$name];
            unset(self::$_events[$name]);
        }

        return $cb;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function getEventHandler($name)
    {
        $cb = null;

        if (isset(self::$_events[$name])) {
            $cb = self::$_events[$name];
        }

        return $cb;
    }

    /**
     * @return array
     */
    public static function getEvents(): array
    {
        return self::$_events;
    }

    /**
     * @return int
     */
    public static function getEventCount(): int
    {
        return \count(self::$_events);
    }
}
