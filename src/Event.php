<?php declare(strict_types=1);

namespace Hbroker91\EventBus;

use Hbroker91\EventBus\Contracts\EventInterface;

/**
 * Class Event
 *
 * @package Dispatcher
 */
class Event implements EventInterface
{
    /** @var string holds the source class' name */
    private $origin;

    /** @var string holds type of the event */
    private $type;

    /** @var array|object holds the value of event payload (if any) */
    private $payload;

    /** @var bool holds the value of event's exec. state */
    private $stopped;

    /**
     * Event constructor.
     * @param string $origin
     * @param string $type
     * @param $payload
     */
    public function __construct(string $origin, string $type, $payload)
    {
        $this->origin = $origin;
        $this->type = $type;
        $this->payload = $payload;
        $this->stopped = false;
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
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param mixed $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
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