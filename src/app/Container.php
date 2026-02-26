<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\Container\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * Registered services and their definitions
     * @var array<string, callable>
     */
    protected array $bindings = [];

    public function get(string $id)
    {
        /*
        AUTOWIRING:
        We will give the container another chance
        to try and resolve the class on its own, without a binding.
        */
        if ($this->has($id)) {
            $callback = $this->bindings[$id];
            // if we have an explicit binding, we call the callback function.
            return $callback($this);
            // this way the callback has access to the container instance,
            // so it can resolve its dependencies from the container itself
        }
        /*
        if there is no binding we will call some kind of resolve method
        which will do the autowiring magic;
        */
        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    public function bind(string $id, callable $concrete): void
    {
        $this->bindings[$id] = $concrete;
    }

    public function resolve(string $id)
    {
        /*
        here we will implement the autowiring logic
        using reflection to inspect the class constructor
        and its parameters and recursively resolve
        dependencies from the container.

        STEPS:
        */
        // 1. Inspect the class that we are trying to get from the container
        $reflectionClass = new \ReflectionClass($id);
        if (! $reflectionClass->isInstantiable()) {
            throw new ContainerException("Cannot instantiate $id.");
        }
        // 2. Inspect the constructor of that class
        $constructor = $reflectionClass->getConstructor();
        if (! $constructor) {
            // if there is no constructor, just instantiate the class
            return new $id();
        }
        // 3. Inspect the parameters of the constructor (dependencies)
        $parameters = $constructor->getParameters();
        if (! $parameters) {
            // if there are no parameters, just instantiate the class
            return new $id();
        }
        // 4. Recursively resolve each dependency from the container
        $dependencies = array_map(
            function (\ReflectionParameter $parameter) {
                $name = $parameter->getName();
                $type = $parameter->getType();

                if (! $type) {
                    throw new ContainerException("Cannot resolve parameter '$name' without type hint.");
                }

                if ($type instanceof \ReflectionUnionType) {
                    throw new ContainerException("Cannot resolve parameter '$name' with union type.");
                }

                if ($type instanceof \ReflectionNamedType && ! $type->isBuiltin()) {
                    // If the parameter is a class, resolve it from the container
                    return $this->get($type->getName());
                }
                throw new ContainerException("Cannot resolve parameter '$name' of type '{$type->getName()}'.");
            },
            $parameters
        );
        // 5. Instantiate the class with the resolved dependencies
        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
