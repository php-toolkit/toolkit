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
 * Trait LiteEventTrait - 简洁版的事件处理trait，一个事件只允许一个回调
 * @package Toolkit\Traits\Event
 */
trait LiteEventTrait
{
    /**
     * @var array
     */
    private $_events = [];

    //////////////////////////////////////////////////////////////////////
    /// events method
    //////////////////////////////////////////////////////////////////////

    /**
     * register a event callback
     * @param string   $name event name
     * @param callable $cb event callback
     * @param bool     $replace replace exists's event cb
     */
    public function on($name, callable $cb, $replace = false): void
    {
        if ($replace || !isset($this->_events[$name])) {
            $this->_events[$name] = $cb;
        }
    }

    /**
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    protected function fire($name, array $args = [])
    {
        if (!isset($this->_events[$name]) || !($cb = $this->_events[$name])) {
            return null;
        }

        return Php::call($cb, ...$args);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function off($name)
    {
        $cb = null;

        if (isset($this->_events[$name])) {
            $cb = $this->_events[$name];
            unset($this->_events[$name]);
        }

        return $cb;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getEventHandler($name)
    {
        return $this->_events[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->_events;
    }

    /**
     * @return int
     */
    public function getEventCount(): int
    {
        return \count($this->_events);
    }

    /**
     * clearEvents
     */
    public function clearEvents(): void
    {
        $this->_events = [];
    }
}
