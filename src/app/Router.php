<?php

declare(strict_types=1);

namespace App;

use App\Container;
use ReflectionClass;
use ReflectionAttribute;
use App\Attributes\Route;
use App\Exceptions\RouteNotFoundException;

class Router
{
    private array $allRoutes;

    public function __construct(private Container $container)
    {
        // ...
    }

    public function registerFromControllerAttrs(array $controllers)
    {
        foreach ($controllers as $controller) {
            $reflectionController = new ReflectionClass($controller);

            foreach ($reflectionController->getMethods() as $method) {
                $attributes = $method->getAttributes(
                    Route::class,
                    flags: ReflectionAttribute::IS_INSTANCEOF
                );

                foreach ($attributes as $attribute) {
                    $route = $attribute->newInstance();

                    $this->register(
                        requestMethod: $route->method,
                        route: $route->route,
                        action: [$controller, $method->getName()]
                    );
                }
            }
        }
    }

    public function register(
        string $requestMethod,
        string $route,
        callable | array $action,
    ): self {
        $this->allRoutes[$requestMethod][$route] = $action;
        return $this;
    }

    public function get(string $route, callable | array $action): self
    {
        return $this->register(
            requestMethod: "get",
            route: $route,
            action: $action,
        );
    }

    public function post(string $route, callable | array $action): self
    {
        return $this->register(
            requestMethod: "post",
            route: $route,
            action: $action,
        );
    }

    public function getAllRoutes(): array
    {
        return $this->allRoutes;
    }

    public function resolve(string $requestUri, string $requestMethod)
    {
        $route = explode(separator: "?", string: $requestUri)[0];
        $action = $this->allRoutes[strtolower($requestMethod)][$route] ?? null;

        if (! $action) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }

        if (is_array($action)) {
            [$cls, $method] = $action;

            if (class_exists($cls)) {
                /*
                $clsInstance = new $cls;
                ----------------------------
                Now we will use the container to resolve the dependencies
                of the controller class, instead of directly instantiating it.
                ----------------------------
                */
                $clsInstance = $this->container->get($cls);

                if (method_exists($clsInstance, $method)) {
                    return call_user_func_array([$clsInstance, $method], []);
                }
            }
        }
        throw new RouteNotFoundException();
    }
}
