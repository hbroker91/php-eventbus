<?php

namespace Hbroker91\PHPEventBus\Exceptions;

use Exception;

/**
 * Class EventBusException
 *
 * @package Hbroker91\PHPEventBus\Exceptions
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
class EventBusException extends Exception
{
    protected $code = 500;
}