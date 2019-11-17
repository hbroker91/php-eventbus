<?php
declare(strict_types=1);

namespace Hbroker91\EventBus\Contracts;

/**
 * Interface SubscriberInterface
 *
 * @package Dispatcher
 */
interface SubscriberInterface
{
    /**
     * ### Returns an array containing pairs of *eventName - affinity* to subscribe
     *
     * @return array
     */
    public static function toSubscribe(): array;
}