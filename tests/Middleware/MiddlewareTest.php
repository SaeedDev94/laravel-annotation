<?php

namespace Tests\Middleware;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Middleware\Fixture\FooController;

class MiddlewareTest extends TestCase
{
    public ?Router $router = null;

    public function setUp(): void
    {
        parent::setUp();

        try {
            $this->router = new Router(Mockery::mock(Dispatcher::class), Container::getInstance());
            $this->router->get('resource', [FooController::class, 'index']);
            $this->router->post('resource', [FooController::class, 'store']);
            $this->router->put('resource/{id}', [FooController::class, 'update']);
            $this->router->delete('resource/{id}', [FooController::class, 'destroy'])->middleware('classic');
        } catch (Exception) {
        }
    }

    public function testRouterInstance(): void
    {
        $this->assertNotNull($this->router);
    }

    public function testIndexMiddleware(): void
    {
        $route = $this->getRoute('GET');

        $this->assertNotEquals(false, $route);
        $this->assertEquals(['one', 'two', 'three:arg1,arg2'], $route->gatherMiddleware());
    }

    public function testStoreMiddleware(): void
    {
        $route = $this->getRoute('POST');

        $this->assertNotEquals(false, $route);
        $this->assertEquals(['six', 'four', 'five:arg'], $route->gatherMiddleware());
    }

    public function testUpdateMiddleware(): void
    {
        $route = $this->getRoute('PUT');

        $this->assertNotEquals(false, $route);
        $this->assertEquals(['one', 'six'], $route->gatherMiddleware());
    }

    public function testDestroyMiddleware(): void
    {
        $route = $this->getRoute('DELETE');

        $this->assertNotEquals(false, $route);
        $this->assertEquals(['classic', 'one'], $route->gatherMiddleware());
    }

    public function getRoute(string $method): Route | bool
    {
        $routes = $this->router->getRoutes()->get($method);
        return end($routes);
    }
}
