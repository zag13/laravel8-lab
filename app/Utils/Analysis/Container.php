<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/19
 * Time: 2:26 下午
 */


namespace App\Utils\Analysis;

use ArrayAccess;
use Closure;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use TypeError;

class Container implements ArrayAccess
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * An array of the types that have been resolved.
     *
     * @var bool[]
     */
    protected $resolved = [];

    /**
     * The container's bindings.
     *
     * @var array[]
     */
    protected $bindings = [];

    /**
     * The container's shared instances.
     *
     * @var object[]
     */
    protected $instances = [];

    /**
     * The registered type aliases.
     *
     * @var string[]
     */
    protected $aliases = [];

    private $s = [];


    /**
     * Determine if the given abstract type has been bound.
     *
     * @param string $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) ||
            isset($this->instances[$abstract]) ||
            $this->isAlias($abstract);
    }

    /**
     * Determine if a given string is an alias.
     *
     * @param string $name
     * @return bool
     */
    public function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param $concrete
     * @return mixed|object
     *
     * @throws ReflectionException
     */
    public function build($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new Exception("Target class [$concrete] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new Exception("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        try {
            $instances = $this->resolveDependencies($dependencies);
        } catch (Exception $e) {
            throw $e;
        }

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param \ReflectionParameter[] $dependencies
     * @return array
     *
     * @throws Exception
     */
    protected function resolveDependencies(array $dependencies)
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $className = Util::getParameterClassName($dependency);

            $result = is_null($className)
                ? $this->resolvePrimitive($dependency)
                : $this->build($className);

            if ($dependency->isVariadic()) {
                $results = array_merge($results, $result);
            } else {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Resolve a non-class hinted primitive dependency.
     *
     * @param ReflectionParameter $parameter
     * @return mixed
     *
     * @throws ReflectionException
     */
    protected function resolvePrimitive(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception("Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}");
    }

    public function getAlias($abstract)
    {
        return isset($this->aliases[$abstract])
            ? $this->getAlias($this->aliases[$abstract])
            : $abstract;
    }

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->bound($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->build($this->s[$key]);
    }

    /**
     * Set the value at a given offset.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        return $this->s[$key] = $value;
    }

    /**
     * Unset the value at a given offset.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
    }

    /**
     * Dynamically access container services.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set container services.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}
