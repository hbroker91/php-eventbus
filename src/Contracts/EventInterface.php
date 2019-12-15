<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Contracts;

/**
 * ## Collection of necessary methods as a contract for an Event object
 *
 * @package Hbroker91\PHPEventBus\Contracts
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
interface EventInterface
{
    /**
     * @return bool
     */
    public function isStopped(): bool;

    /**
     * @param bool $stopped
     */
    public function setStopped(bool $stopped): void;

    /**
     * @return object
     */
    public function getOrigin(): object;

    /**
     * @param object $object
     */
    public function setOrigin(object $object): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param mixed $type
     */
    public function setType($type): void;

    /**
     * @return mixed
     */
    public function getPayload();

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function setPayload($payload);
}