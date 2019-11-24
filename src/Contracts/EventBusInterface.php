<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Contracts;

use Hbroker91\PHPEventBus\Exceptions\EventBusException;
use Hbroker91\PHPEventBus\Subscriber;

/**
 * Interface EventBusInterface
 *
 * @package Hbroker91\PHPEventBus\Contracts
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
interface EventBusInterface
{
    /**
     * ### Attaches a listener to a specific event
     *
     * @param string $type     - the type (name) of the Event
     * @param object $listener - the listener to add
     * @param mixed  $func     - a callable or a function of $listener to invoke
     * @param int    $affinity - a number between 1 - 10 shows the importance of the event for the listener
     */
    public function addListener(string $type, object $listener, $func, int $affinity): void;

    /**
     * ### Removes $listener from the listeners / subscribers of event $type
     *
     * @param string $type
     * @param object $listener
     *
     * @throws EventBusException
     */
    public function removeListener(string $type, object $listener): void;

    /**
     * ### Checks if $type event has any listeners / subscribers in EventBus's registry
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasListener(string $type): bool;

    /**
     * ### Subscribes a given object of a class to a specific event with the desired affinity
     *
     * The more bigger the affinity, the given subscriber will notified more
     * earlier about the happening of the specified event
     *
     * @param Subscriber $subscriber
     *
     * @throws EventBusException
     */
    public function subscribe(Subscriber $subscriber): void;

    /**
     * Unsubscribes a subscriber from a specific event
     *
     * @param string              $type       - type (name) of the event
     * @param SubscriberInterface $subscriber - object of the subscriber class
     *
     * @throws EventBusException
     */
    public function unSubscribe(string $type, SubscriberInterface $subscriber): void;

    /**
     * ### Broadcasts passed ``$event`` to registered listeners and subscribers
     *
     * @param EventInterface $event
     *
     * @throws EventBusException
     */
    public function broadcast(EventInterface $event): void;


}
