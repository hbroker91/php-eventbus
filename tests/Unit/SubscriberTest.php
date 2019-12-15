<?php

namespace Hbroker91\EventBus\Tests\Unit;

require_once '../../src/EventBus.php';
require_once '../TestClasses.php';

use Hbroker91\PHPEventBus\Event;
use HBroker91\PHPEventBus\EventBus;
use Hbroker91\PHPEventBus\Exceptions\SubcriberException;
use Hbroker91\PHPEventBus\Subscriber;
use Hbroker91\PHPEventBus\Tests\Observer;
use PHPUnit\Framework\TestCase;

/**
 * Class SubscriberTest
 *
 * @package Hbroker91\PHPEventBus\Tests\Unit
 * @covers \Hbroker91\PHPEventBus\Subscriber
 */
class SubscriberTest extends TestCase
{
    /** @var Subscriber */
    private $subscriber;

    protected function setUp(): void
    {
        $this->subscriber = new Subscriber($this);
        $this->subscriber
            ->addToEvent('sampleEvent')
            ->setHandler('listen')
            ->withAffinity(8);
    }

    public function testConstruct(): void
    {
        EventBus::getInstance()->subscribe($this->subscriber);
        $this->assertTrue(EventBus::getInstance()->hasListener('sampleEvent'));
    }

    public function testAddToEventEmptyEventName()
    {
        $this->subscriber = new Subscriber($this);
        $this->expectException(SubcriberException::class);
        $this->subscriber->addToEvent('');
        $this->subscriber->setHandler(function() {});
    }

    public function testAddToEventValidEvent()
    {
        $this->subscriber = new Subscriber($this);
        $this->subscriber->addToEvent('tableUpdated');
        $this->subscriber->setHandler(function($payload) {
            //var_dump($payload);
        })
        ->withAffinity(8);
        EventBus::getInstance()->subscribe($this->subscriber);
        $this->assertTrue(EventBus::getInstance()->hasListener('tableUpdated'));
        EventBus::getInstance()->resetAll();
        unset($this->subscriber);
    }

    public function testWithAffinityWithoutGivenValue()
    {
        $this->subscriber = new Subscriber(new Observer());
        $this->subscriber->addToEvent('tableUpdated');
        $this->subscriber->setHandler(function($payload) {
            //var_dump($payload);
        })
            ->withAffinity();
        EventBus::getInstance()->subscribe($this->subscriber);
        $this->assertEquals(1, $this->subscriber->getEventsToSubscribe()['tableUpdated']['affinity']);
        EventBus::getInstance()->resetAll();
        unset($this->subscriber);
    }

    public function testWithAffinityWithValueGiven()
    {
        $this->subscriber = new Subscriber(new Observer());
        $this->subscriber->addToEvent('tableUpdated');
        $this->subscriber->setHandler(function($payload) {
            //var_dump($payload);
        })
            ->withAffinity(10);
        EventBus::getInstance()->subscribe($this->subscriber);
        $this->assertEquals(10, $this->subscriber->getEventsToSubscribe()['tableUpdated']['affinity']);
        EventBus::getInstance()->resetAll();
        unset($this->subscriber);
    }

    public function testGetEventsToSubscribeNoEventWasSetup()
    {
        $this->subscriber = new Subscriber(new Observer());
        $this->expectExceptionMessage('No events for subscribe was provided');
        EventBus::getInstance()->subscribe($this->subscriber);
        unset($this->subscriber);
    }

    public function testGetEventsToSubscribe()
    {
        $this->subscriber = new Subscriber(new Observer());
        $this->subscriber->addToEvent('tableUpdated');
        $this->subscriber->setHandler(function($payload) {
            //var_dump($payload);
        })
            ->withAffinity(10);
        EventBus::getInstance()->subscribe($this->subscriber);
        $this->assertArrayHasKey('tableUpdated', $this->subscriber->getEventsToSubscribe());
        EventBus::getInstance()->resetAll();
        unset($this->subscriber);
    }

    public function testGetReference()
    {
        $this->subscriber = new Subscriber($this);
        $this->subscriber->addToEvent('tableUpdated');
        $this->subscriber->setHandler(function($payload) {})
            ->withAffinity(10);
        EventBus::getInstance()->subscribe($this->subscriber);
        $this->assertEquals($this, $this->subscriber->getReference());
        EventBus::getInstance()->resetAll();
        unset($this->subscriber);
    }

    public function testSetHandlerWithoutAddEvent()
    {
        $this->subscriber = new Subscriber($this);
        $this->expectExceptionMessage('Name of event to subscribe is missing');
        $this->subscriber->setHandler(function($payload) {})
            ->withAffinity(10);
        unset($this->subscriber);
    }

    public function testSetHandlerInvalidCallback()
    {
        $this->subscriber = new Subscriber($this);
        $this->subscriber->addToEvent('tableUpdated');
        $this->expectExceptionMessage('Invalid callback provided');
        $this->subscriber->setHandler('')
            ->withAffinity(10);
        unset($this->subscriber);
    }
}
