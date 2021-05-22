<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/19
 * Time: 2:26 下午
 */


namespace App\Utils\Analysis;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container
{
    private $s = [];

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
            $result = is_null(Util::getParameterClassName($dependency))
                ? $this->resolvePrimitive($dependency)
                : $this->build($dependency->getType()->getName());

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

    /**
     * Dynamically access container services.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->build($this->s[$key]);
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
        $this->s[$key] = $value;
    }
}
