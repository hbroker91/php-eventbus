<?php
declare(strict_types=1);

namespace Hbroker91\PHPEventBus\Tests;

use Hbroker91\PHPEventBus\Contracts\SubscriberInterface;
use Hbroker91\PHPEventBus\Exceptions\SubcriberException;
use Hbroker91\PHPEventBus\Subscriber;
use PHPUnit\Framework\TestCase;

/**
 * Class SubscriberTest
 *
 * @covers Hbroker91\PHPEventBus\Subscriber
 * @package Hbroker91\PHPEventBus\Tests\Unit
 */
class SubscriberTest extends TestCase
{
    /** @var Subscriber represents one subscribing class */
    private $subscriber;

    /** @var SubscriberInterface */
    private $observer;

    protected function setUp(): void
    {
        $this->observer = new class implements SubscriberInterface
        {
            /**
             * ### Returns the model object representing the subbscribing class
             */
            public function subscribe(): Subscriber
            {
                return new Subscriber([
                    'object' => $this,
                    'handler' => 'listen',
                    'eventName' => 'userAdded',
                    'affinity' => 6,
                ]);
            }
        };

        $this->subscriber = $this->observer->subscribe();
    }

    public function testGetObject(): void
    {
        $this->assertInstanceOf(get_class($this->observer), $this->subscriber->getObject());
    }

    public function testGetEventName(): void
    {
        $this->assertEquals('userAdded', $this->subscriber->getEventName());
    }

    public function testGetAffinity(): void
    {
        $this->assertEquals(6, $this->subscriber->getAffinity());
    }

    public function testGetHandler(): void
    {
        $this->assertEquals('listen', $this->subscriber->getHandler());
    }

    public function testConstructValidInput(): void
    {
        $data = [
            'object' => $this->observer,
            'handler' => function($event) {
            var_dump($event);
            },
            'eventName' => 'userAdded',
            'affinity' => 6,
        ];

        $subscriber = new Subscriber($data);
        $this->assertEquals($this->observer, $subscriber->getObject());
        $this->assertEquals(6, $subscriber->getAffinity());
        $this->assertEquals('userAdded', $subscriber->getEventName());
        $this->assertEquals(function($event) {
            var_dump($event);
        }, $subscriber->getHandler());
        unset($subscriber);
    }

    public function testConstructorMissingObject(): void
    {
        $this->expectException(SubcriberException::class);
        $newSubscriber = new Subscriber([
            'handler' => function($event) {
                var_dump($event);
            },
            'eventName' => 'userAdded',
            'affinity' => 6,
        ]);
        unset($newSubscriber);
    }

    public function testConstructorMissingHandler(): void
    {
        $this->expectException(SubcriberException::class);
        $newSubscriber = new Subscriber([
            'object' => $this->observer,
            'eventName' => 'userAdded',
            'affinity' => 6,
        ]);
        unset($newSubscriber);
    }

    public function testConstructorMissingEventName(): void
    {
        $this->expectException(SubcriberException::class);
        $newSubscriber = new Subscriber([
            'object' => $this->observer,
            'handler' => function($event) {
                var_dump($event);
            },
            'affinity' => 6,
        ]);
        unset($newSubscriber);
    }

    public function testConstructorMissingAffinity(): void
    {
        $newSubscriber = new Subscriber([
            'object' => $this->observer,
            'handler' => function($event) {
                var_dump($event);
            },
            'eventName' => 'userAdded',
        ]);

        $this->assertEquals(1, $newSubscriber->getAffinity());
        unset($newSubscriber);
    }

    public function testSetObject(): void
    {
        $instance = new class {
        };
        $this->subscriber->setObject($instance);
        $this->assertEquals($instance, $this->subscriber->getObject());
        unset($instance);
    }

    public function testSetAffinity(): void
    {
        $this->subscriber->setAffinity(10);
        $this->assertEquals(10, $this->subscriber->getAffinity());
    }

    public function testSetHandler(): void
    {
        $closure = function($event) {
            var_dump($event);
        };
        $this->subscriber->setHandler($closure);
        $this->assertEquals($closure, $this->subscriber->getHandler());
        unset($closure);
    }

    protected function tearDown(): void
    {
        unset($this->subscriber, $this->observer);
    }
}
