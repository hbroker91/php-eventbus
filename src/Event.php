<?php

declare(strict_types=1);

namespace Hbroker91\PHPEventBus;

use Hbroker91\PHPEventBus\Contracts\EventInterface;

/**
 * ## Model class for representing an Event
 *
 * @package Hbroker91\PHPEventBus
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
class Event implements EventInterface
{
    /** @var object holds the source class' name */
    private $origin;

    /** @var string holds type of the event */
    private $type;

    /** @var array|object holds the value of event payload (if any) */
    private $payload;

    /** @var bool holds the value of event's exec. state */
    private $stopped = false;

    /**
     * Event constructor.
     *
     * @param object $origin - source class of the Event
     * @param string $type - name of the Event
     * @param $payload - optional data to send with the Event
     */
    public function __construct(object $origin, string $type, $payload)
    {
        $this->origin = $origin;
        $this->type = $type;
        $this->payload = $payload;
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return $this->stopped;
    }

    /**
     * @param bool $stopped
     */
    public function setStopped(bool $stopped): void
    {
        $this->stopped = $stopped;
    }

    /**
     * @return mixed
     */
    public function getOrigin(): object
    {
        return $this->origin;
    }

    /**
     * @param object $origin
     */
    public function setOrigin(object $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }
}