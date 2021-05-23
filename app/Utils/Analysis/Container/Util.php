<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/21
 * Time: 10:38 上午
 */


namespace App\Utils\Analysis\Container;

use ReflectionNamedType;

class Util
{
    /**
     * Get the class name of the given parameter's type, if possible.
     *
     * From Reflector::getParameterClassName() in Illuminate\Support.
     *
     * @param \ReflectionParameter $parameter
     * @return string|null
     */
    public static function getParameterClassName($parameter)
    {
        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return;
        }

        $name = $type->getName();

        if (!is_null($class = $parameter->getDeclaringClass())) {
            if ($name === 'self') {
                return $class->getName();
            }

            if ($name === 'parent' && $parent = $class->getParentClass()) {
                return $parent->getName();
            }
        }

        return $name;
    }
}
