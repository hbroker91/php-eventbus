<?php

namespace Hbroker91\PHPEventBus\Tests;

use Hbroker91\PHPEventBus\Contracts\EventBusInterface;
use Hbroker91\PHPEventBus\Contracts\SubscriberInterface;
use Hbroker91\PHPEventBus\EventBus;
use Hbroker91\PHPEventBus\Subscriber;
use PHPUnit\Framework\TestCase;

/**
 * Class EventDispatcherTest
 *
 * @package Hbroker91\PHPEventBus\Tests
 *
 * @covers Hbroker91\PHPEventBus\EventBus
 * @package Hbroker91\PHPEventBus\Tests\Unit
 */
class EventDispatcherTest extends TestCase
{
    /** @var EventBus */
    private $instance;

    /** @var \ReflectionClass|null */
    private $reflectionedClass;

    protected function setUp(): void
    {
        $this->instance = EventBus::getInstance();
        $this->reflectionedClass = new \ReflectionClass(get_class($this->instance));
    }

    public function testInitState(): void
    {
        $prop = $this->reflectionedClass->getProperty('listeners');
        $prop->setAccessible(true);
        $this->assertEquals([], $prop->getValue(EventBus::getInstance()));
        unset($prop);
    }

    public function testImplementInterface(): void
    {
        $this->assertInstanceOf(EventBusInterface::class, EventBus::getInstance());
    }

    public function testGetInstance()
    {
        $this->assertEquals($this->instance, EventBus::getInstance());
    }

    public function testCheckAffinityNegativeValue(): void
    {
        $method = $this->reflectionedClass->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(1, $method->invokeArgs(EventBus::getInstance(), [-10]));
        unset($method);
    }

    public function testCheckAffinityZeroValue(): void
    {
        $method = $this->reflectionedClass->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(1, $method->invokeArgs(EventBus::getInstance(), [0]));
        unset($method);
    }

    public function testCheckAffinityValueBiggerThanTen(): void
    {
        $method = $this->reflectionedClass->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(10, $method->invokeArgs(EventBus::getInstance(), [1111]));
        unset($method);
    }

    public function testBroadcast()
    {

    }

    public function testUnSubscribe()
    {

    }

    public function testSubscriberInterfaceNotImplementedInClass(): void
    {
        $mock = $this->getMockBuilder('Observer');
        $this->assertNotInstanceOf(SubscriberInterface::class, class_implements($mock->getMock()));
        unset($observer);
    }

    public function testSubscriberInterfaceImplementedInClass(): void
    {
        $observer = new Observer();
        $this->assertEquals(SubscriberInterface::class, class_implements(get_class($observer))[SubscriberInterface::class]);
        unset($observer);
    }

    protected function tearDown(): void
    {
        unset($this->reflectionedClass, $this->instance);
    }

}

class Observer implements SubscriberInterface
{
    /**
     * ### Returns the model object representing the subbscribing class
     */
    public function subscribe(): Subscriber
    {
        return new Subscriber(
            [
                'object' => $this,
                'handler' => function ($event) {
                    var_dump($event);
                },
                'affinity' => 5,
                'eventName' => 'tableUpdated'
            ]);
    }
}




