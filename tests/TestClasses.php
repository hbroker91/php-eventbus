<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Tests;

use Hbroker91\PHPEventBus\Contracts\EventBusInterface;
use Hbroker91\PHPEventBus\Contracts\SubscriberInterface;
use Hbroker91\PHPEventBus\Event;
use HBroker91\PHPEventBus\EventBus;
use Hbroker91\PHPEventBus\Subscriber;

class Emitter
{
    public function __construct($eventName)
    {
        $this->emit($eventName);
    }

    public function emit($eventName = 'tableUpdated'): void
    {
        EventBus::getInstance()->broadcast(
            new Event($this, $eventName, __CLASS__)
        );
    }
}

class AnotherObserver {

    public function __construct()
    {
        EventBus::getInstance()->addListener('sampleEvent', $this, 'selfReference', 4);
    }

    public function selfReference(Event $event, EventBusInterface $eBus)
    {
        $eBus->broadcast(new Event($this, 'sampleEvent',[]));
    }
}

class Observer implements SubscriberInterface
{
    public function attachListening(): void
    {
        EventBus::getInstance()->addListener('tableUpdated', $this, 'listen', 6);
    }

    /**
     * ### Returns the model object representing the subscribing class
     */
    public function subscribe(): void
    {
        EventBus::getInstance()->subscribe((new Subscriber($this))->addToEvent('tableUpdated')
            ->setHandler('listen')
            ->withAffinity(8));
    }

    public static function listen(Event $event, EventBus $eventBus): void
    {
        echo $event->getPayload();
    }
}