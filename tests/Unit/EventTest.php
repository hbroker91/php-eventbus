<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Tests\Unit;

require_once '../TestClasses.php';

use Hbroker91\PHPEventBus\Contracts\EventInterface;
use Hbroker91\PHPEventBus\Event;
use HBroker91\PHPEventBus\EventBus;
use Hbroker91\PHPEventBus\Tests\Observer;
use PHPUnit\Framework\TestCase;

/**
 * Class EventTest
 *
 * @covers \Hbroker91\PHPEventBus\Event
 * @package Hbroker91\PHPEventBus\Tests\Unit
 */
class EventTest extends TestCase
{
    /**
     * @var EventInterface
     */
    private $event;
    private $observer;

    protected function setUp(): void
    {
        $this->observer = new  Observer();
        $this->event = new Event($this->observer, 'titleChanged', ['message']);
    }

    public function testConstruct(): void
    {
        $this->assertEquals($this->event, new Event($this->observer, 'titleChanged', ['message']));
    }

    public function testSetOrigin(): void
    {
        $this->event->setOrigin($this);
        $this->assertNotNull($this->event->getOrigin());
    }

    public function testGetOrigin(): void
    {
        $this->event->setOrigin($this);
        $this->assertEquals($this, $this->event->getOrigin());
    }

    public function testSetPayload(): void
    {
        $this->event->setPayload(['blank content']);
        $this->assertEquals(['blank content'],$this->event->getPayload());
    }

    public function testGetPayload(): void
    {
        $this->event->setPayload([1,2,3,4]);
        $this->assertEquals([1,2,3,4], $this->event->getPayload());
    }

    public function testSetType(): void
    {
        $this->event->setType('listUpdated');
        $this->assertEquals('listUpdated', $this->event->getType());
    }

    public function testGetType(): void
    {
        $this->assertEquals('titleChanged', $this->event->getType());
    }

    public function testSetStopped(): void
    {
        $this->event->setStopped(true);
        $this->assertTrue($this->event->isStopped());
    }

    public function testIsStopped(): void
    {
        $this->assertFalse($this->event->isStopped());
    }

    protected function tearDown(): void
    {
        unset($this->event);
    }
}
