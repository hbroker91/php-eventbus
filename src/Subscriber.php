<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus;

use Hbroker91\PHPEventBus\Exceptions\SubcriberException;

/**
 * ## Model class for representing the parameters of subscribing class
 *
 * @package Hbroker91\EventBus
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
class Subscriber
{
    /** @var  object of the subscribing class */
    private $object;

    /** @var string name of the Event to subscribe */
    private $eventName;

    /** @var int the level of interest */
    private $affinity = 1;

    /** @var Callable function of the class / a closure to call @ dispatching */
    private $handler;

    /**
     * Subscriber constructor.
     *
     * @param array $payload
     *
     * @throws SubcriberException
     */
    public function __construct(array $payload)
    {
        if (! isset($payload['object'], $payload['eventName'], $payload['handler'])) {
            throw new SubcriberException('Missing subcriber data');
        }
        foreach ($payload as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object): void
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @param mixed $eventName
     */
    public function setEventName($eventName): void
    {
        $this->eventName = $eventName;
    }

    /**
     * @return int
     */
    public function getAffinity(): int
    {
        return $this->affinity;
    }

    /**
     * @param int $affinity
     */
    public function setAffinity(int $affinity): void
    {
        $this->affinity = $affinity;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param mixed $handler
     */
    public function setHandler($handler): void
    {
        $this->handler = $handler;
    }
}