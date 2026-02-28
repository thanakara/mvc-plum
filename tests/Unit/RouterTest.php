<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Router;
use App\Container;
use Tests\Fixtures\FakeController;
use Tests\Fixtures\AnotherFakeController;
use App\Exceptions\RouteNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;

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
        $this->router->register("get", "/home", fn() => "home.html.twig");

        $this->assertArrayHasKey("/home", $this->router->getAllRoutes()["get"]);
    }

    public function testGetHelperRegistersGetRoute(): void
    {
        $this->router->get("/about", fn() => "about.html.twig");

        $this->assertArrayHasKey("/about", $this->router->getAllRoutes()["get"]);
    }

    public function testPostHelperRegistersPostRoute(): void
    {
        $this->router->post("/submit", fn() => "submit.html.twig");

        $this->assertArrayHasKey("/submit", $this->router->getAllRoutes()["post"]);
    }

    public function testRegisterIsChainable(): void
    {
        $result = $this->router->get("/foo", fn() => "foo.html.twig");

        $this->assertInstanceOf(Router::class, $result);
    }

    // --- resolve() with callable ---

    public function testResolveCallsCallableAction(): void
    {
        $this->router->get("/trunk", fn() => "trunk.html.twig");

        $result = $this->router->resolve("/trunk", "GET");

        $this->assertSame("trunk.html.twig", $result);
    }

    public function testResolveStripsQueryString(): void
    {
        $this->router->get("/search", fn() => "search.html.twig");

        $result = $this->router->resolve("/search?q=php", "GET");

        $this->assertSame("search.html.twig", $result);
    }

    // --- resolve() with array [class, method] ---

    public function testResolveUsesContainerToInstantiateController(): void
    {
        // Arrange - use an anonymous class as a fake controller
        $fakeController = new class {
            public function index(): string
            {
                return "controller-index.html.twig";
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

        $this->assertSame("controller-index.html.twig", $result);
    }

    // --- resolve() exceptions ---

    public function testResolveThrowsRouteNotFoundForUnknownRoute(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->router->resolve("/does-not-exist", "GET");
    }

    public function testResolveThrowsRouteNotFoundForWrongMethod(): void
    {
        $this->router->get("/home", fn() => "home.html.twig");

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
            AnotherFakeController::class,
        ]);

        $this->assertCount(2, $this->router->getAllRoutes()["get"]);
        $this->assertCount(2, $this->router->getAllRoutes()["post"]);
    }
}
