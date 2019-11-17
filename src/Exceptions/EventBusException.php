<?php

namespace Hbroker91\EventBus\Exceptions;

use Exception;

/**
 * ### Exception class for exc. originating from EventBus
 *
 * @package EventBus
 */
class EventBusException extends Exception
{
    protected $code = 500;
}