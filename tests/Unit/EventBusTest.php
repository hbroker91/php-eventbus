<?php

namespace Hbroker91\PHPEventBus\Tests\Unit;

require_once '../TestClasses.php';

use Hbroker91\PHPEventBus\Contracts\EventBusInterface;
use Hbroker91\PHPEventBus\Contracts\SubscriberInterface;
use Hbroker91\PHPEventBus\EventBus;
use Hbroker91\PHPEventBus\Subscriber;
use Hbroker91\PHPEventBus\Tests\AnotherObserver;
use Hbroker91\PHPEventBus\Tests\Emitter;
use Hbroker91\PHPEventBus\Tests\Observer;
use PHPUnit\Framework\TestCase;

/**
 * Class EventBusTest
 *
 * @covers \Hbroker91\PHPEventBus\EventBus
 * @package Hbroker91\PHPEventBus\Tests\Unit
 */
class EventBusTest extends TestCase
{
    /** @var EventBus */
    private $instance;

    /** @var \ReflectionClass|null */
    private $reflectionedClass;

    protected function setUp(): void
    {
        $this->instance = EventBus::getInstance();
        EventBus::getInstance();
    }

    public function testInitState(): void
    {
        $this->assertEquals([], $this->instance->getAllListeners());
    }

    public function testGetFunctionTypeClosureProvided(): void
    {
        $method = (new \ReflectionClass(EventBus::class))->getMethod('getFunctionType');
        $method->setAccessible(true);
        $this->assertEquals('function', $method->invokeArgs(EventBus::getInstance(), [function () {}]));
        unset($method);
    }

    public function testGetFunctionTypeStringProvided(): void
    {
        $method = (new \ReflectionClass(EventBus::class))->getMethod('getFunctionType');
        $method->setAccessible(true);
        $this->assertEquals('string', $method->invoke(EventBus::getInstance(), 'listen'));
        unset($method);
    }

    public function testStripNamespace(): void
    {
        $method = (new \ReflectionClass(EventBus::class))->getMethod('stripNamespace');
        $method->setAccessible(true);
        $this->assertEquals('EventBus', $method->invoke(EventBus::getInstance(), 'Hbroker91\\PHPEventbus\\EventBus'));
        unset($method);
    }

    public function testImplementInterface(): void
    {
        $this->assertInstanceOf(EventBusInterface::class, EventBus::getInstance());
    }

    public function testGetInstance()
    {
        $instance = EventBus::getInstance();
        $this->assertEquals($instance, EventBus::getInstance());
        unset($instance);
    }

    public function testGetListenersByEventHasListeners(): void
    {
        $observer = new Observer();
        $observer->subscribe();
        $subscriber = new Subscriber(new AnotherObserver());
        $subscriber
            ->addToEvent('tableUpdated')
            ->setHandler('callHook')
            ->withAffinity(7);
        $this->instance->subscribe($subscriber);
        $this->assertCount(2, $this->instance->getListenersByEvent('tableUpdated'));
        $this->instance->resetAll();
    }

    public function testGetAllListenersNoListenerAttached(): void
    {
        $this->assertEquals([], $this->instance->getListenersByEvent('tableUpdated'));
    }

    public function testGetAllListeners(): void
    {
        $observer = new Observer();
        $anotherObserver = new AnotherObserver();
        $this->instance->subscribe((new Subscriber($observer))
            ->addToEvent('tableUpdated')
        ->withAffinity(6)
        ->setHandler(function () {})
        );
        $this->instance->subscribe((new Subscriber($anotherObserver))
            ->addToEvent('tableUpdated')
            ->withAffinity(6)
            ->setHandler(function () {})
        );
        $this->assertArrayHasKey('tableUpdated', $this->instance->getAllListeners());
        $this->instance->resetAll();
        unset($observer, $anotherObserver);
    }

    public function testUnSubscribe(): void
    {
        $observer = new Observer();
        $this->instance->subscribe((new Subscriber($observer))
            ->addToEvent('tableUpdated')
            ->withAffinity()
            ->setHandler(function () {})
        );
        $this->instance->unSubscribe('tableUpdated', $observer);
        $this->assertFalse($this->instance->hasListener('tableUpdated'));
        unset($observer);
    }

    public function testRemoveListener(): void
    {
        $observer = new Observer();
        $this->instance->subscribe((new Subscriber($observer))
            ->addToEvent('tableUpdated')
            ->withAffinity()
            ->setHandler(function () {})
        );
        $this->instance->removeListener('tableUpdated', $observer);
        $this->assertFalse($this->instance->hasListener('tableUpdated'));
        unset($observer);
    }

    public function testCheckCrossReferenceSelfReference(): void
    {
        $this->expectExceptionMessage('There is a self-reference loop in class: "AnotherObserver" method: "selfReference"');
        $observer = new AnotherObserver();
        $emitter = new Emitter('sampleEvent');
        $emitter->emit();
    }

    public function testCheckAffinityNegativeValue(): void
    {
        $method = (new \ReflectionClass(EventBus::class))->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(1, $method->invokeArgs(EventBus::getInstance(), [-10]));
        unset($method);
    }

    public function testCheckAffinityZeroValue(): void
    {
        $method = (new \ReflectionClass(EventBus::class))->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(1, $method->invokeArgs(EventBus::getInstance(), [0]));
        unset($method);
    }

    public function testCheckAffinityValueBiggerThanTen(): void
    {
        $method = (new \ReflectionClass(EventBus::class))->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(10, $method->invokeArgs(EventBus::getInstance(), [1111]));
        unset($method);
    }

    public function testBroadcastOneEventWithSubscribe(): void
    {
        $observer = new Observer();
        $observer->subscribe();
        ob_start();
        $emitter = new Emitter('tableUpdated');
        $observer->subscribe();
        $this->assertEquals('Hbroker91\PHPEventBus\Tests\Emitter', ob_get_clean());
        EventBus::getInstance()->removeListener('tableUpdated', $observer);
        unset($observer, $emitter);
    }

    public function testBroadcastOneEventWithAddListener(): void
    {
        $observer = new Observer();
        $observer->attachListening();
        ob_start();
        $emitter = new Emitter('tableUpdated');
        $dump = ob_get_clean();
        $this->assertEquals("Hbroker91\PHPEventBus\Tests\Emitter", $dump);
    }

    public function testSubscribe(): void
    {
        $observer = new Observer();
        $observer->subscribe();
        $subscriber = new Subscriber(new AnotherObserver());
        $subscriber
            ->addToEvent('tableUpdated')
            ->setHandler('callHook')
            ->withAffinity(5);
        $this->instance->subscribe($subscriber);
        $this->assertArrayHasKey(5, $this->instance->getListenersByEvent('tableUpdated'));
        $this->instance->resetAll();
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
    public function testResetAll(): void
    {
        $observer = new Observer();
        $this->instance->addListener('sampleEvent', $observer, function() {}, 9);
        $this->assertCount(1, $this->instance->getAllListeners());
        $this->instance->resetAll();
        $this->assertCount(0, $this->instance->getAllListeners());
    }


    protected function tearDown(): void
    {
        unset($this->instance);
    }
}




