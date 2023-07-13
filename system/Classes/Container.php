<?php

/*
// This file is a part of K-MVC
// version: 1.1.0
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
// docs: https://github.com/buihanh2304/simple-php-mvc-framework/wiki
*/

namespace System\Classes;

use Closure;
use Exception;
use ReflectionNamedType;

class Container
{
    protected static $instance;

    protected $instances = [];

    protected $bindings = [];

    protected function __construct()
    {
        //
    }

    public static function getInstance(): Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    public function bind($abstract, $concrete = null, $shared = false)
    {
        unset($this->instances[$abstract]);

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!$concrete instanceof Closure) {
            $concrete = function ($c) use ($abstract, $concrete) {
                $method = ($abstract == $concrete) ? 'build' : 'make';

                return $c->{$method}($concrete);
            };
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    public function singleton($abstract, $concrete = null)
    {
        return $this->bind($abstract, $concrete, true);
    }

    public function make($abstract, $parameters = [])
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = !isset($this->bindings[$abstract]) ? $abstract : $this->bindings[$abstract]['concrete'];

        if ($concrete === $abstract || $concrete instanceof Closure) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->make($concrete, $parameters);
        }

        if (is_callable($object)) {
            $object = $object();
        }

        if (isset($this->bindings[$abstract]['shared']) && $this->bindings[$abstract]['shared'] === true) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function build($concrete, $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        $reflector = new \ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = [];
        $parameters = $constructor->getParameters();

        foreach ($parameters as $parameter) {
            $result = null;

            if (is_null($this->getParameterClassName($parameter))) {
                if ($parameter->isDefaultValueAvailable()) {
                    $result = $parameter->getDefaultValue();
                } elseif ($parameter->isVariadic()) {
                    $result = [];
                } else {
                    throw new Exception("Unresolvable dependency resolving [$parameter].");
                }
            } else {
                try {
                    $result = $this->make($this->getParameterClassName($parameter));
                } catch (Exception $e) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $result = $parameter->getDefaultValue();
                    } elseif ($parameter->isVariadic()) {
                        $result = [];
                    } else {
                        throw $e;
                    }
                }
            }

            if ($parameter->isVariadic()) {
                $dependencies = array_merge($dependencies, $result);
            } else {
                $dependencies[] = $result;
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    protected function getParameterClassName($parameter)
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        if (! is_null($class = $parameter->getDeclaringClass())) {
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
