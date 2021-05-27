<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/26
 * Time: 3:39 下午
 */


namespace App\Utils\Analysis\Events;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Dispatcher
{
    protected $map = [
        Example::class => ExampleListener::class . '@doSomething'
    ];

    protected $container;

    protected $listeners = [];

    public function __construct(ContainerContract $container = null)
    {
        $this->container = $container ?: new Container;

        foreach ($this->map as $key => $value) {
            $this->listen($key, $value);
        }
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param string|array $events
     * @param string|array|null $listener
     * @return void
     */
    public function listen($events, $listener = null)
    {
        foreach ((array)$events as $event) {
            $this->listeners[$event][] = $this->makeListener($listener);
        }
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  string|array  $listener
     * @return Closure
     */
    public function makeListener($listener)
    {
        if (is_string($listener)) {
            return $this->createClassListener($listener);
        }

        if (is_array($listener) && isset($listener[0]) && is_string($listener[0])) {
            return $this->createClassListener($listener);
        }

        return function ($event, $payload) use ($listener) {
            return $listener(...array_values($payload));
        };
    }

    /**
     * Create a class based listener using the IoC container.
     *
     * @param  string|array  $listener
     * @return Closure
     */
    public function createClassListener($listener)
    {
        return function ($event, $payload) use ($listener) {
            $callable = $this->createClassCallable($listener);

            return $callable(...array_values($payload));
        };
    }

    /**
     * Create the class based event callable.
     *
     * @param  array|string  $listener
     * @return callable
     */
    protected function createClassCallable($listener)
    {
        [$class, $method] = is_array($listener)
            ? $listener
            : $this->parseClassCallable($listener);

        if (!method_exists($class, $method)) {
            $method = '__invoke';
        }

        $listener = $this->container->make($class);

        return [$listener, $method];
    }

    /**
     * Parse the class listener into class and method.
     *
     * @param  string  $listener
     * @return array
     */
    protected function parseClassCallable($listener)
    {
        return Str::parseCallback($listener, 'handle');
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        [$event, $payload] = $this->parseEventAndPayload(
            $event, $payload
        );

        $responses = [];

        foreach ($this->getListeners($event) as $listener) {
            $response = $listener($event, $payload);

            if ($halt && !is_null($response)) {
                return $response;
            }

            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        return $halt ? null : $responses;
    }

    /**
     * Parse the given event and payload and prepare them for dispatching.
     *
     * @param  mixed  $event
     * @param  mixed  $payload
     * @return array
     */
    protected function parseEventAndPayload($event, $payload)
    {
        if (is_object($event)) {
            [$payload, $event] = [[$event], get_class($event)];
        }

        return [$event, Arr::wrap($payload)];
    }

    /**
     * Get all of the listeners for a given event name.
     *
     * @param  string  $eventName
     * @return array
     */
    public function getListeners($eventName)
    {
        $listeners = $this->listeners[$eventName] ?? [];

        return class_exists($eventName, false)
            ? $this->addInterfaceListeners($eventName, $listeners)
            : $listeners;
    }

    /**
     * Add the listeners for the event's interfaces to the given array.
     *
     * @param  string  $eventName
     * @param  array  $listeners
     * @return array
     */
    protected function addInterfaceListeners($eventName, array $listeners = [])
    {
        foreach (class_implements($eventName) as $interface) {
            if (isset($this->listeners[$interface])) {
                foreach ($this->listeners[$interface] as $names) {
                    $listeners = array_merge($listeners, (array)$names);
                }
            }
        }

        return $listeners;
    }
}
