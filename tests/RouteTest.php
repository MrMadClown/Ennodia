<?php

namespace Tests;

use Ennodia\SingleRoute;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testSingleRouteMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match('GET', 'index');
        static::assertNotNull($resolvedRoute);
        static::assertEmpty($resolvedRoute->args);
        static::assertEquals('App\Http\Controllers\IndexController', $resolvedRoute->controller);
    }

    public function testSingleRouteMethodMisMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match('POST', 'index');
        static::assertNull($resolvedRoute);
    }

    public function testSingleRoutePathMisMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match('GET', 'home');
        static::assertNull($resolvedRoute);
    }

    public function testSimpleRouteMisMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match('POST', 'index');
        static::assertNull($resolvedRoute);
    }

    public function testVariableRouteMatch(): void
    {
        $route = SingleRoute::post('/^user\/(?P<userId>\d+)$/', 'App\Http\Controllers\UserController');
        $resolvedRoute = $route->match('POST', 'user/124');
        static::assertNotNull($resolvedRoute);
        static::assertArrayHasKey('userId', $resolvedRoute->args);
        static::assertEquals(124, $resolvedRoute->args['userId']);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);
    }
}
