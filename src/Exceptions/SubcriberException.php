<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Exceptions;

/**
 * ## Base Exception class of Subscriber
 *
 * @package Hbroker91\PHPEventBus\Exceptions
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
class SubcriberException extends \Exception
{
    protected $code = 500;
}