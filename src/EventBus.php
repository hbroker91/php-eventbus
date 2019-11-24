<?php
declare(strict_types=1);

namespace HBroker91\PHPEventBus;

use Hbroker91\PHPEventBus\Contracts\EventBusInterface;
use Hbroker91\PHPEventBus\Contracts\EventInterface;
use Hbroker91\PHPEventBus\Exceptions\EventBusException;

/**
 * Mediator to provide interaction between subscribers - emitters
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

    /** @var EventInterface holds the value of passed event */
    private $event;

    /** @var array holds the subscribers / listeners for various events */
    private $listeners;

    /** @var array holds the current active listeners for the Event under processing */
    private $queue;

    /**
     * ### Provides a static, single object of EventBus class
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
     * EventBus constructor.
     */
    private function __construct()
    {
        $this->listeners = [];
        $this->queue = [];
    }

    /**
     * ### Checks if the origin of the Event is recursive or cross referencing another class
     *
     * @param object $class
     * @param $callback
     *
     * @throws EventBusException
     */
    private function checkCrossReference(object $class, $callback): void
    {
        if (!empty($this->queue)) {

            $size = count($this->queue) - 1;

            $refClass = &$class;
            $refMethod = &$callback;

            // explicit call to itself immediately (recursive)
            if ($refClass === $this->queue[$size][0] &&
                $refMethod === $this->queue[$size][1]) {
                unset($refClass, $refMethod, $this->queue);
                throw new EventBusException('There is a self-reference loop in class: ' .
                    '"' . $this->stripNamespace(get_class($class)) . '"' . ' method: ' . '"' . $callback . '"');
            }

            // sliding window method
            if (($size >= 3) &&
                $this->queue[$size] === $this->queue[$size - 2] &&
                $this->queue[$size - 1] === [&$class, &$callback]) {
                throw new EventBusException('There is a cross-reference loop between classes: ' .
                    $this->stripNamespace(get_class($this->queue[$size][0]) . ' and ' . $this->stripNamespace
                        (get_class($this->queue[$size - 1][0]))));
            }
        }
    }

    /**
     * ### Executes $callback of $class (if class function) or just $callback if it is a Callable
     *
     * @param object $class
     * @param $callback
     *
     * @throws EventBusException
     */
    private function execute(object $class, $callback): void
    {

        $this->checkCrossReference($class, $callback);

        $type = $this->checkFunctionType($callback);

        if ($type === 'string') {
            $declaredMethods = array_flip(get_class_methods($class));
            if (!isset($declaredMethods[$callback])) {
                echo sprintf('%s class doesn\'t have method: %s',
                    $this->stripNamespace(get_class($class)), $callback);
                return;
            };

            $this->queue[] = [&$class, &$callback];
            $class::$callback($this->event, static::$instance);

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
    public function hasListener(string $type): bool
    {
        return isset($this->listeners[$type]) && !empty($this->listeners[$type]);
    }

    /**
     * @inheritDoc
     */
    public function broadcast(EventInterface $event): void
    {
        $this->event = $event;

        if (!$this->hasListener($this->$event->getType())) {
            throw new EventBusException('There isn\'t any listeners attached to '
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
    private function checkFunctionType($toCheck): string
    {
        if (is_string($toCheck)) {
            return 'string';
        }

        if (is_callable($toCheck)) {
            return 'function';
        }

        throw new EventBusException($toCheck . 'is neither a function or callable');
    }

    /**
     * ### Checks if $affinity is valid, autocorrects if needed
     *
     * @param int $affinity
     *
     * @return int
     */
    private function checkAffinity(int $affinity): int
    {
        if ($affinity <= 0) {
            return 1;
        }
        return $affinity > 10 ? 10 : $affinity;
    }

    /**
     * @inheritDoc
     */
    public function addListener(string $type, object $listener, $func, int $affinity): void
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
    private function orderByPriority(string $eventType): void
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
    public function subscribe(Subscriber $subscriber): void
    {

        $aff = $this->checkAffinity($subscriber->getAffinity());

        $this->addListener($subscriber->getEventName(),
            $subscriber->getObject(),
            $subscriber->getHandler(),
            $aff
        );
    }

    /**
     * ### Returns all attached listeners
     *
     * @return array
     */
    public function getAllListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @inheritDoc
     */
    public function unSubscribe(string $type, $subscriber): void
    {
        $this->removeListener($type, $subscriber);
    }

    /**
     * @inheritDoc
     */
    public function removeListener(string $type, object $listener): void
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