<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Contracts;

use Hbroker91\PHPEventBus\Subscriber;

/**
 * Interface SubscriberInterface
 *
 * @package Hbroker91\EventBus\Contracts
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
interface SubscriberInterface
{
    /**
     * ### Returns the model object representing the subbscribing class
     */
    public function subscribe(): Subscriber;
}
