<?php

namespace Tests;

use Ennodia\SingleRoute;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class SingleRouteTest extends TestCase
{
    public function methodProvider(): \Generator
    {
        foreach ([
                     Request::METHOD_HEAD, Request::METHOD_GET, Request::METHOD_POST,
                     Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE,
                     Request::METHOD_PURGE, Request::METHOD_OPTIONS, Request::METHOD_TRACE,
                     Request::METHOD_CONNECT,
                 ] as $method) {
            yield $method => [mb_strtolower($method), mb_strtolower($method)];
        }

        yield 'ANY' => ['any', null];

    }

    /** @dataProvider methodProvider */
    public function testRouteCreation(string $method, ?string $expected): void
    {
        /** @var SingleRoute $route */
        $route = SingleRoute::$method('/^index$/', 'App\Http\Controllers\IndexController');

        static::assertEquals($expected, $route->method);
    }

    public function testSingleRouteMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match(Request::METHOD_GET, 'index');
        static::assertNotNull($resolvedRoute);
        static::assertEmpty($resolvedRoute->args);
        static::assertEquals('App\Http\Controllers\IndexController', $resolvedRoute->controller);
    }

    public function testSingleRouteMethodMisMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match(Request::METHOD_POST, 'index');
        static::assertNull($resolvedRoute);
    }

    public function testSingleRoutePathMisMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match(Request::METHOD_GET, 'home');
        static::assertNull($resolvedRoute);
    }

    public function testSimpleRouteMisMatch(): void
    {
        $route = SingleRoute::get('/^index$/', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match(Request::METHOD_POST, 'index');
        static::assertNull($resolvedRoute);
    }

    public function testVariableRouteMatch(): void
    {
        $route = SingleRoute::post('/^user\/(?P<userId>\d+)$/', 'App\Http\Controllers\UserController');
        $resolvedRoute = $route->match(Request::METHOD_POST, 'user/124');
        static::assertNotNull($resolvedRoute);
        static::assertArrayHasKey('userId', $resolvedRoute->args);
        static::assertEquals(124, $resolvedRoute->args['userId']);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);
    }
}
