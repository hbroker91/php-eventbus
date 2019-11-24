<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Contracts;

/**
 * ## Interface EventInterface
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
     * @return mixed
     */
    public function getOrigin();

    /**
     * @return mixed
     */
    public function getType();

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