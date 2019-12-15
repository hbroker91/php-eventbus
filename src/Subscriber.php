<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus;

use Hbroker91\PHPEventBus\Exceptions\SubcriberException;

/**
 * ## Model class for representing the attributes of the subscribing class
 *
 * @package Hbroker91\EventBus
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
class Subscriber
{
    /** @var object reference of the subscribing class*/
    private $reference;

    /** @var array event(s) to which to subscribe */
    private $events = [];

    /** @var string name of the event to subscribe */
    private $eventName = '';

    /**
     * Subscriber constructor
     *
     * @param object $instance
     */
    public function __construct(object $instance)
    {
        $this->reference = $instance;
    }

    /**
     * ### Returns the associated event's name
     *
     * @return string
     */
    private function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * ### Sets up current event's name, allocates an array for other attributes to load
     *
     * @param string $eventName
     *
     * @return $this
     */
    public function addToEvent(string $eventName): self
    {
        $this->eventName = $eventName;
        $this->events[$eventName] = [];
        return $this;
    }

    /**
     * ### Sets up the handler (callback) of this Subscriber to call later @ dispatching
     *
     * @param $callback
     *
     * @return $this
     *
     * @throws SubcriberException
     */
    public function setHandler($callback): self
    {
        if ($this->eventName === '') {
            throw new SubcriberException('Name of event to subscribe is missing');
        }

        if (! is_callable($callback) && $callback === '') {
            throw new SubcriberException('Invalid callback provided');
        }

        $this->events[$this->eventName]['handler'] = $callback;
        return $this;
    }

    /**
     * ### Sets the affinity (interest level) of the Subscriber
     *
     * @param int $affinity
     *
     * @return $this
     *
     * @throws SubcriberException
     */
    public function withAffinity(int $affinity = 1): self
    {
        if ($this->eventName === '') {
            throw new SubcriberException('Name of event to subscribe is missing');
        }

        $this->events[$this->eventName]['affinity'] = $affinity;
        return $this;
    }

    /**
     * ### Returns the subscribing class' reference
     *
     * @return object
     */
    public function getReference(): object
    {
        return $this->reference;
    }

    /**
     * ### Checks if setAffinity & withHandler were invoked @ object creation
     *
     * @throws SubcriberException
     */
    private function checkEventData(): void
    {
        foreach ($this->events as $name => $data)
        {
            if (! isset($data['handler'], $data['affinity'])) {
                throw new SubcriberException('Handler / affinity or both missing @ event: '.$name);
            }
        }
    }

    /**
     * ### Returns all events, to which the object wants to subscribe
     *
     * @return array
     *
     * @throws SubcriberException
     */
    public function getEventsToSubscribe(): array
    {
        if (empty($this->events)) {
            throw new SubcriberException('No events for subscribe was provided');
        }
        $this->checkEventData();
        return $this->events;
    }


}