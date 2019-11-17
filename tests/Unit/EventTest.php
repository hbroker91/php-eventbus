<?php

namespace Hbroker91\EventBus\Tests\Unit;

use Hbroker91\EventBus\Contracts\EventInterface;
use Hbroker91\EventBus\Event;
use PHPUnit\Framework\TestCase;

/**
 * Class EventTest
 *
 * @package Hbroker91\EventBus\Tests\Unit
 */
class EventTest extends TestCase
{
    /**
     * @var EventInterface
     */
    private $event = null;

    protected function tearDown(): void
    {
        unset($this->event);
    }

    protected function setUp(): void
    {
        $this->event = new Event('Observer', 'titleChanged', ['message']);
    }

    public function testConstruct()
    {
        $this->assertEquals($this->event, new Event('Observer', 'titleChanged', ['message']));
    }

    public function testSetOrigin()
    {
        $this->event->setOrigin('SQLHandler');
        $this->assertNotNull($this->event->getOrigin());
    }

    public function testGetOrigin()
    {
        $this->event->setOrigin('SQLHandler');
        $this->assertEquals('SQLHandler', $this->event->getOrigin());
    }

    public function testSetPayload()
    {
        $this->event->setPayload(['blank content']);
        $this->assertEquals(['blank content'],$this->event->getPayload());
    }

    public function testGetPayload()
    {
        $this->event->setPayload([1,2,3,4]);
        $this->assertEquals([1,2,3,4], $this->event->getPayload());
    }

    public function testSetType()
    {
        $this->event->setType('listUpdated');
        $this->assertEquals('listUpdated', $this->event->getType());
    }

    public function testGetType()
    {
        $this->assertEquals('titleChanged', $this->event->getType());
    }

    public function testSetStopped()
    {
        $this->event->setStopped(true);
        $this->assertTrue($this->event->isStopped());
    }

    public function testIsStopped()
    {
        $this->assertFalse($this->event->isStopped());
    }
}
