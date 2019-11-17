<?php

namespace Hbroker91\EventBus\Contracts;

/**
 * ## Interface EventInterface
 *
 * @package Hbroker91\EventBus
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