<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-27
 * Time: 16:17
 */

namespace Toolkit\Traits\Event;

use Toolkit\PhpUtil\PhpHelper;

/**
 * Class EventTrait
 * @package Toolkit\Traits\Event
 */
trait EventTrait
{
    /**
     * set the supported events, if you need.
     *  if it is empty, will allow register any event.
     * @var array
     */
    protected $supportedEvents = [];

    /**
     * registered Events
     * @var array
     * [
     *  'event' => bool, // is once event
     * ]
     */
    private $events = [];

    /**
     * @var array
     */
    private $eventHandlers = [];

    /**
     * register a event handler
     * @param $event
     * @param callable $handler
     * @param bool $once
     */
    public function on($event, callable $handler, $once = false)
    {
        if ($this->isSupportedEvent($event)) {
            $this->eventHandlers[$event][] = $handler;
            $this->events[$event] = (bool)$once;
        }
    }

    /**
     * register a once event handler
     * @param $event
     * @param callable $handler
     */
    public function once($event, callable $handler)
    {
        $this->on($event, $handler, true);
    }

    /**
     * trigger event
     * @param $event
     * @param array $args
     * @return bool
     */
    public function fire($event, array $args = []): bool
    {
        if (!isset($this->events[$event])) {
            return false;
        }

        // call event handlers of the event.
        foreach ((array)$this->eventHandlers[$event] as $cb) {
            // return FALSE to stop go on handle.
            if (false === PhpHelper::call($cb, ...$args)) {
                break;
            }
        }

        // is a once event, remove it
        if ($this->events[$event]) {
            return $this->off($event);
        }

        return true;
    }

    /**
     * remove event and it's handlers
     * @param string $event
     * @return mixed
     */
    public function off($event)
    {
        if ($this->hasEvent($event)) {
            $handler = $this->eventHandlers[$event];

            unset($this->events[$event], $this->eventHandlers[$event]);

            return $handler;
        }

        return null;
    }

    /**
     * clearEvents
     */
    public function clearEvents()
    {
        $this->events = $this->eventHandlers = [];
    }

    /**
     * @param $event
     * @return bool
     */
    public function hasEvent($event): bool
    {
        return isset($this->events[$event]);
    }

    /**
     * @param $event
     * @return bool
     */
    public function isOnce($event): bool
    {
        if ($this->hasEvent($event)) {
            return $this->events[$event];
        }

        return false;
    }

    /**
     * check $name is a supported event name
     * @param $event
     * @return bool
     */
    public function isSupportedEvent($event): bool
    {
        if (!$event || !preg_match('/[a-zA-z][\w-]+/', $event)) {
            return false;
        }

        if ($ets = $this->supportedEvents) {
            return \in_array($event, $ets, true);
        }

        return true;
    }

    /**
     * @return array
     */
    public function getSupportEvents(): array
    {
        return $this->supportedEvents;
    }

    /**
     * @param array $supportedEvents
     */
    public function setSupportEvents(array $supportedEvents)
    {
        $this->supportedEvents = $supportedEvents;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @return int
     */
    public function getEventCount(): int
    {
        return \count($this->events);
    }
}
