<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/30 0030
 * Time: 23:47
 */

namespace Toolkit\Traits\Event;

use Toolkit\SysUtil\PhpHelper;

/**
 * Class FixedEventTrait
 * @package Toolkit\Traits\Event
 */
trait FixedEventTrait
{
    /**
     * @var \SplFixedArray
     */
    protected $eventHandlers;

    /**
     * @return string[]
     */
    public function getSupportedEvents(): array
    {
        // return [ self::ON_CONNECT, self::ON_HANDSHAKE, self::ON_OPEN, self::ON_MESSAGE, self::ON_CLOSE, self::ON_ERROR];
        return [];
    }

    /**
     * @param string $event
     * @return bool
     */
    public function isSupportedEvent(string $event): bool
    {
        return \in_array($event, $this->getSupportedEvents(), true);
    }

    /**
     * @param $event
     * @return bool
     */
    public function hasEventHandler($event)
    {
        if (false === ($key = array_search($event, $this->getSupportedEvents(), true))) {
            return false;
        }

        return isset($this->eventHandlers[$key]);
    }

    /**
     * @return \SplFixedArray
     */
    public function getEventHandlers(): \SplFixedArray
    {
        return $this->eventHandlers;
    }

    /**
     * @return int
     */
    public function getEventCount()
    {
        return $this->eventHandlers->count();
    }

    /**
     * @param string $event
     * @return callable
     */
    public function getEventHandler(string $event)
    {
        if (false === ($key = array_search($event, $this->getSupportedEvents(), true))) {
            return null;
        }

        if (!isset($this->eventHandlers[$key])) {
            return null;
        }

        return $this->eventHandlers[$key];
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    /// events method
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * register a event callback
     * @param string $event event name
     * @param callable $cb event callback
     * @param bool $replace replace exists's event cb
     * @throws \InvalidArgumentException
     */
    public function on(string $event, callable $cb, bool $replace = false)
    {
        if (false === ($key = array_search($event, $this->getSupportedEvents(), true))) {
            $sup = implode(',', $this->getSupportedEvents());

            throw new \InvalidArgumentException("The want registered event [$event] is not supported. Supported: $sup");
        }

        // init property
        if ($this->eventHandlers === null) {
            $this->eventHandlers = new \SplFixedArray(\count($this->getSupportedEvents()));
        }

        if (!$replace && isset($this->eventHandlers[$key])) {
            throw new \InvalidArgumentException("The want registered event [$event] have been registered! don't allow replace.");
        }

        $this->eventHandlers[$key] = $cb;
    }

    /**
     * remove event handler
     * @param string $event
     * @return bool
     */
    public function off(string $event)
    {
        if (false === ($key = array_search($event, $this->getSupportedEvents(), true))) {
            return null;
        }

        if (!isset($this->eventHandlers[$key]) || !($cb = $this->eventHandlers[$key])) {
            return null;
        }

        $this->eventHandlers[$key] = null;

        return $cb;
    }

    /**
     * @param string $event
     * @param array $args
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function fire(string $event, array $args = [])
    {
        if (false === ($key = array_search($event, $this->getSupportedEvents(), true))) {
            throw new \InvalidArgumentException("Trigger a not exists's event: $event.");
        }

        if (!isset($this->eventHandlers[$key]) || !($cb = $this->eventHandlers[$key])) {
            return null;
        }

        return PhpHelper::call($cb, ...$args);
    }

}
