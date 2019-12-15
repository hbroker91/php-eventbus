<?php

namespace Hbroker91\PHPEventBus\Exceptions;

use Exception;

/**
 * ## Base Exception class of EventBus
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