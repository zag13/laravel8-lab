<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/25
 * Time: 4:09 下午
 */


namespace App\Utils\Analysis\Facades;

use Exception;
use Illuminate\Foundation\Application;

abstract class Facade
{
    protected static $map = [
        'example' => ExampleReal::class
    ];

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws Exception
     */
    protected static function getFacadeAccessor()
    {
        throw new Exception('Facade does not implement getFacadeAccessor method.');
    }

    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$map[$name])) {
            return new static::$map[$name];
        }

        throw new Exception('未找到映射地址');
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     *
     * @throws Exception
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new Exception('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }
}
