<?php

namespace Dispatcher;

use Hbroker91\EventBus\Exceptions\EventBusException;
use PHPUnit\Framework\TestCase;

/**
 * Class EventDispatcherTest
 *
 * @package Dispatcher
 * @covers  \Dispatcher\EventBus
 */
class EventDispatcherTest extends TestCase
{
    /** @var EventBusException */
    private $instance = null;
    /** @var \ReflectionClass|null */
    private $reflectionedClass = null;

    protected function setUp(): void
    {
        $this->instance = EventBus::getInstance();
        $this->reflectionedClass = new \ReflectionClass(EventBus::class);
    }

    protected function tearDown(): void
    {
        unset($this->reflectionedClass);
        unset($this->instance);
    }

    public function testInitState()
    {
        $prop = $this->reflectionedClass->getProperty('listeners');
        $prop->setAccessible(true);
        $this->assertEquals([], $prop->getValue(EventBus::getInstance()));
        unset($prop);
    }

    public function testImplementInterface()
    {
        $this->assertInstanceOf(EventDispatcherInterface::class, EventBus::getInstance());
    }

    public function testGetInstance()
    {
        $this->assertEquals($this->instance, EventBus::getInstance());
    }

    public function testCheckAffinityNegativeValue()
    {
        $method = $this->reflectionedClass->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(1, $method->invokeArgs(EventBus::getInstance(), [-10]));
        unset($method);
    }

    public function testCheckAffinityZeroValue()
    {
        $method = $this->reflectionedClass->getMethod('checkAffinity');
        $method->setAccessible(true);
        $this->assertEquals(1, $method->invokeArgs(EventBus::getInstance(), [0]));
        unset($method);
    }

    public function testCheckAffinityValueBiggerThanTen()
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

    public function testSubscribeInterfaceNotImplementedInClass()
    {
        $observer = new Observer();
        $this->expectErrorMessage('Argument 1 passed to Dispatcher\EventDispatcher::subscribe()');
        EventBus::getInstance()->subscribe($observer);
        unset($observer);
    }

    public function testSubscribeInterfaceImplementedInClassMissingAffinity()
    {
        $exampleObserver = new ExampleObserver();
        EventBus::getInstance()->subscribe($exampleObserver);
        $prop = $this->reflectionedClass->getProperty('listeners');
        $prop->setAccessible(true);
        $this->assertEquals([], $prop->getValue(EventBus::getInstance()));
        unset($exampleObserver);
    }

    public function testAddListener()
    {
        $observer = new Observer();
        EventBus::getInstance()->addListener(
            'listChanged',
            $observer,
            'listen',
            5
        );

        $this->assertTrue(EventBus::getInstance()->hasListener('listChanged'));
        $prop = $this->reflectionedClass->getProperty('listeners');
        $prop->setAccessible(true);
        $this->assertEquals([
            'listChanged' => [
                5 => [
                    [$observer, 'listen'],
                ],
            ],
        ], $prop->getValue(EventBus::getInstance()));
        unset($prop, $observer);
    }

    public function testHasListener()
    {
        $observer = new Observer();
        EventBus::getInstance()->addListener(
            'listChanged',
            $observer,
            'listen',
            5
        );
        $this->assertTrue(EventBus::getInstance()->hasListener('listChanged'));
        unset($observer);
    }

    public function testRemoveListenerOneListener()
    {
        $observer = new Observer();
        EventBus::getInstance()->addListener(
            'listChanged',
            $observer,
            'listen',
            5
        );

        EventBus::getInstance()->removeListener('listChanged', $observer);
        $prop = $this->reflectionedClass->getProperty('listeners');
        $prop->setAccessible(true);
        $this->assertEquals(['listChanged' => []], $prop->getValue(EventBus::getInstance()));
        unset($prop, $observer);
    }

    public function testRemoveListenerMoreListenersSameEvent()
    {
        $observer = new Observer();
        $exampleObserver = new ExampleObserver();
        EventBus::getInstance()->addListener(
            'listChanged',
            $observer,
            'listen',
            5
        );

        EventBus::getInstance()->addListener(
            'listChanged',
            $exampleObserver,
            'listen',
            7
        );

        EventBus::getInstance()->removeListener('listChanged', $observer);
        $prop = $this->reflectionedClass->getProperty('listeners');
        $prop->setAccessible(true);
        var_dump($prop->getValue(EventBus::getInstance()));
        $this->assertEquals([
            'listChanged' => [
                7 => [
                    [$exampleObserver, 'listen'],
                ],
            ],
        ], $prop->getValue(EventBus::getInstance()));
        unset($prop, $observer, $exampleObserver);
    }

}

class Observer
{

    public function __construct()
    {
    }

    public function listen(EventInterface $event, EventDispatcherInterface $dispatcher)
    {

    }
}

class ExampleObserver implements SubscriberInterface
{
    public $payload = [];

    public function __construct()
    {
        $this->payload = [
            'tableUpdated' => [
                'function' => function ($event) {
                    dump($event->getPayload());
                },
                'affinity' => 8,
            ],
        ];
    }

    /**
     * Returns an array containing pairs of *eventName - affinity* to subscribe
     *
     * @return array
     */
    public static function toSubscribe(): array
    {
        return [
            'tableUpdated' => [
                'function' => function ($event) {
                    dump($event->getPayload());
                },
            ],
        ];
    }
}




