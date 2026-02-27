<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Router;
use App\Container;
use App\Exceptions\RouteNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Fixtures\FakeController;

class RouterTest extends TestCase
{
    private Router $router;
    private MockObject&Container $mockContainer;

    protected function setUp(): void
    {
        $this->mockContainer = $this->createMock(Container::class);
        $this->router = new Router($this->mockContainer);
    }

    // --- register() & getAllRoutes() ---

    public function testRegisterAddsRoute(): void
    {
        $this->router->register("get", "/home", fn() => "home.twig");

        $this->assertArrayHasKey("/home", $this->router->getAllRoutes()["get"]);
    }

    public function testGetHelperRegistersGetRoute(): void
    {
        $this->router->get("/about", fn() => "about.twig");

        $this->assertArrayHasKey("/about", $this->router->getAllRoutes()["get"]);
    }

    public function testPostHelperRegistersPostRoute(): void
    {
        $this->router->post("/submit", fn() => "submit.twig");

        $this->assertArrayHasKey("/submit", $this->router->getAllRoutes()["post"]);
    }

    public function testRegisterIsChainable(): void
    {
        $result = $this->router->get("/foo", fn() => "foo.twig");

        $this->assertInstanceOf(Router::class, $result);
    }

    // --- resolve() with callable ---

    public function testResolveCallsCallableAction(): void
    {
        $this->router->get("/hello", fn() => "hello.twig");

        $result = $this->router->resolve("/hello", "GET");

        $this->assertSame("hello.twig", $result);
    }

    public function testResolveStripsQueryString(): void
    {
        $this->router->get("/search", fn() => "search.twig");

        $result = $this->router->resolve("/search?q=php", "GET");

        $this->assertSame("search.twig", $result);
    }

    // --- resolve() with array [class, method] ---

    public function testResolveUsesContainerToInstantiateController(): void
    {
        // Arrange - use an anonymous class as a fake controller
        $fakeController = new class {
            public function index(): string
            {
                return "controller.index.twig";
            }
        };

        $controllerClass = get_class($fakeController);

        // Tell the mock container to return our fake controller
        $this->mockContainer
            ->expects($this->once())       // assert container->get() is called once
            ->method("get")
            ->with($controllerClass)
            ->willReturn($fakeController);

        $this->router->get("/dashboard", [$controllerClass, "index"]);

        $result = $this->router->resolve("/dashboard", "GET");

        $this->assertSame("controller.index.twig", $result);
    }

    // --- resolve() exceptions ---

    public function testResolveThrowsRouteNotFoundForUnknownRoute(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->router->resolve("/does-not-exist", "GET");
    }

    public function testResolveThrowsRouteNotFoundForWrongMethod(): void
    {
        $this->router->get("/home", fn() => "home.twig");

        $this->expectException(RouteNotFoundException::class);

        $this->router->resolve("/home", "POST"); // registered as GET, not POST
    }

    public function testRegisterFromControllerAttrsRegistersRoutesFromAttributes(): void
    {
        $this->router->registerFromControllerAttrs([FakeController::class]);

        $routes = $this->router->getAllRoutes();

        $this->assertArrayHasKey("/fake", $routes["get"]);
        $this->assertArrayHasKey("/fake/store", $routes["post"]);
    }

    public function testRegisterFromControllerAttrsIgnoresMethodsWithoutRouteAttribute(): void
    {
        $this->router->registerFromControllerAttrs([FakeController::class]);

        $routes = $this->router->getAllRoutes();

        // notARoute() should not have been registered anywhere
        $allActions = array_merge(...array_values($routes));
        $registeredMethods = array_column($allActions, 1);

        $this->assertNotContains("notARoute", $registeredMethods);
    }

    public function testRegisterFromControllerAttrsHandlesMultipleControllers(): void
    {
        $this->router->registerFromControllerAttrs([
            FakeController::class,
            // add another fake controller here
        ]);

        $this->assertCount(1, $this->router->getAllRoutes()["get"]);
        $this->assertCount(1, $this->router->getAllRoutes()["post"]);
    }
}
