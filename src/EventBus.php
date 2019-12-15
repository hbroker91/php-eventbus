<?php
declare(strict_types=1);

namespace HBroker91\PHPEventBus;

use Hbroker91\PHPEventBus\Contracts\EventBusInterface;
use Hbroker91\PHPEventBus\Contracts\EventInterface;
use Hbroker91\PHPEventBus\Exceptions\EventBusException;

/**
 * ### Mediator to provide interaction between subscribers - emitters
 *
 * @package Hbroker91\PHPEventBus
 *
 * @copyright 2019. Adam Szalmasagi
 * @license MIT
 */
final class EventBus implements EventBusInterface
{
    /** @var EventBus holds the singleton of the class */
    private static $instance;

    /** @var EventInterface holds the passed event object */
    private $event;

    /** @var array holds the subscribers / listeners for various events */
    private $listeners;

    /** @var array holds the current active listeners for the Event under processing */
    private $queue;

    /** @var object holds the address of subscriber at reference analysis */
    private $refClass;

    /** @var mixed holds the name of the function or the explicit Closure at reference analysis */
    private $refMethod;

    /** @var int holds the actual size of the queue */
    private $queueSize;

    /**
     * ### Singleton of this class
     *
     * @return EventBus
     */
    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * EventBus constructor
     */
    private function __construct()
    {
        $this->listeners = [];
        $this->queue = [];
    }

    /**
     * ### Removes all attached listeners of all events from the registry
     */
    public function resetAll(): void
    {
        $this->listeners = [];
    }

    /**
     * ### Checks if some class referencing itself in its' listener function
     *
     * @return bool
     */
    private function isClassSelfReferencing(): bool
    {
        if ($this->refClass === $this->queue[$this->queueSize][0] &&
            $this->refMethod === $this->queue[$this->queueSize][1]) {
            unset($refClass, $refMethod, $this->queue);
            return true;
        }
        return false;
    }

    /**
     * ### Checks if a class cross reference with an another listener / subscriber
     *
     * @return bool
     */
    private function isClassCrossReferencing(): bool
    {
        if (($this->queueSize >= 3) &&
            $this->queue[$this->queueSize] === $this->queue[$this->queueSize - 2] &&
            $this->queue[$this->queueSize - 1] === [$this->refClass, $this->refMethod]) {
            return true;
        }
        return false;
    }

    /**
     * ### Checks if the origin of the Event is self-referencing or cross referencing with another class
     *
     * @param object $class
     * @param $callback
     *
     * @throws EventBusException
     */
    private function checkCrossReference(object $class, $callback): void
    {
        if (! empty($this->queue)) {

            $this->queueSize = count($this->queue) - 1;

            $this->refClass = &$class;
            $this->refMethod = &$callback;

            if ($this->isClassSelfReferencing()) {
                throw new EventBusException('There is a self-reference loop in class: ' .
                    '"' . $this->stripNamespace(get_class($class)) . '"' . ' method: ' . '"' . $callback . '"');
            }

            if ($this->isClassCrossReferencing()) {
                throw new EventBusException('There is a cross-reference loop between classes: ' .
                    $this->stripNamespace(get_class($this->queue[$size][0]) . ' and ' . $this->stripNamespace
                        (get_class($this->queue[$size - 1][0]))));
            }
        }
    }

    /**
     * ### Executes $callback of $class (if class has that function) or just $callback if it is a Callable
     *
     * @param object $class
     * @param $callback
     *
     * @throws EventBusException
     */
    private function execute(object $class, $callback): void
    {

        $this->checkCrossReference($class,$callback);

        $type = $this->getFunctionType($callback);

        if ($type === 'string') {

            if (!method_exists($class, $callback)) {
                echo sprintf('%s class doesn\'t have method: %s',
                    $this->stripNamespace(get_class($class)), $callback);
                return;
            }

            $this->queue[] = [&$class, &$callback];
            $class->$callback($this->event, static::$instance);
            return;
        }
        if ($type === 'function') {
            $callback($this->event, static::$instance);
        }
    }

    /**
     * ### Strips out namespace part from class's FQN name
     *
     * @param string $FQN
     *
     * @return string
     */
    private function stripNamespace(string $FQN): string
    {
        $namespaceParts = explode('\\', $FQN);
        $length = count($namespaceParts);

        return $namespaceParts[$length - 1];
    }

    /**
     * @inheritDoc
     */
    public
    function hasListener(string $type): bool
    {
        return isset($this->listeners[$type]) && !empty($this->listeners[$type]);
    }

    /**
     * @inheritDoc
     */
    public
    function broadcast(EventInterface $event): void
    {
        $this->event = $event;

        if (!$this->hasListener($this->event->getType())) {
            throw new EventBusException('There isn\'t any listener attached to '
                . '"' . $this->event->getType() . '"' . ' event');
        }

        $this->orderByPriority($event->getType());

        if (isset($this->listeners[$event->getType()])) {
            foreach ($this->listeners[$event->getType()] as $priorityClass) {
                foreach ($priorityClass as $key => $listener) {

                    $this->execute($listener[0], $listener[1]);

                    if ($this->event->isStopped()) {
                        echo sprintf('Propagation of event: ' . '"'
                            . $this->event->getType() . '"'
                            . ' stopped @ listener: ' . $this->stripNamespace
                            (get_class($listener[0])));

                        return;
                    }
                }
            }
        }
    }

    /**
     * ### Checks if second parameter @ listener / subscriber is a callable or a string
     *
     * @param $toCheck
     *
     * @return string
     *
     * @throws EventBusException
     */
    private
    function getFunctionType($toCheck): string
    {
        if (is_string($toCheck)) {
            return 'string';
        }

        if (is_callable($toCheck)) {
            return 'function';
        }

        throw new EventBusException($toCheck . 'is neither function nor a callable');
    }

    /**
     * ### Checks if $affinity`has valid value, autocorrects if needed
     *
     * @param int $affinity
     *
     * @return int
     */
    private
    function checkAffinity(int $affinity): int
    {
        if ($affinity <= 0) {
            return 1;
        }
        return $affinity > 10 ? 10 : $affinity;
    }

    /**
     * @inheritDoc
     */
    public
    function addListener(string $type, object $listener, $func, int $affinity): void
    {
        $aff = $this->checkAffinity($affinity);
        if (empty($this->listeners[$type][$aff])) {
            $this->listeners[$type][$aff][] = [$listener, $func];
        } else {
            $this->listeners[$type][$aff] = array_merge($this->listeners[$type][$aff],
                [[$listener, $func]]);
        }
    }

    /**
     * ### Sorts listeners / subscribers of a specified event in descending priority order
     *
     * @param string $eventType
     */
    private
    function orderByPriority(string $eventType): void
    {
        if (!empty($this->listeners[$eventType])) {
            ksort($this->listeners[$eventType]);
            $this->listeners[$eventType]
                = array_reverse($this->listeners[$eventType], true);
        }
    }

    /**
     * @inheritDoc
     */
    public
    function subscribe(Subscriber $subscriber): void
    {

        foreach ($subscriber->getEventsToSubscribe() as $event => $eventData) {

            $aff = $this->checkAffinity($eventData['affinity']);

            $this->addListener($event,
                $subscriber->getReference(),
                $eventData['handler'],
                $aff
            );
        }
    }

    /**
     * ### Returns all registered listener
     *
     * @return array
     */
    public
    function getAllListeners(): array
    {
        return $this->listeners;
    }

    /**
     * ### Returns the registered listeners of $type`Event
     *
     * @param string $type
     *
     * @return array
     */
    public
    function getListenersByEvent(string $type): array
    {
        if ($this->hasListener($type)) {
            return $this->listeners[$type];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public
    function unSubscribe(string $type, $subscriber): void
    {
        $this->removeListener($type, $subscriber);
    }

    /**
     * @inheritDoc
     */
    public
    function removeListener(string $type, object $listener): void
    {
        if (isset($this->listeners[$type])) {
            foreach ($this->listeners[$type] as $affinity => $listeners) {
                foreach ($listeners as $idx => $subscriber) {
                    if ($subscriber[0] === $listener) {
                        unset($this->listeners[$type][$affinity][$idx]);
                    }

                    if (empty ($this->listeners[$type][$affinity])) {
                        unset($this->listeners[$type][$affinity]);
                    }
                }
            }
        } else {
            throw new EventBusException('There isn\'t any listener / subscriber attached to event: ' .
                $type);
        }
    }
}