<?php

namespace MrMadClown\Ennodia\Tests;

use MrMadClown\Ennodia\RequestMethod;
use MrMadClown\Ennodia\SingleRoute;
use PHPUnit\Framework\TestCase;

class SingleRouteTest extends TestCase
{
    public function methodProvider(): \Generator
    {
        foreach ([
                     RequestMethod::HEAD, RequestMethod::GET, RequestMethod::POST,
                     RequestMethod::PUT, RequestMethod::PATCH, RequestMethod::DELETE,
                     RequestMethod::PURGE, RequestMethod::OPTIONS, RequestMethod::TRACE,
                     RequestMethod::CONNECT,
                 ] as $method) {
            yield $method->value => [mb_strtolower($method->value), $method];
        }

        yield 'ANY' => ['any', null];

    }

    /** @dataProvider methodProvider */
    public function testRouteCreation(string $method, ?RequestMethod $expected): void
    {
        /** @var SingleRoute $route */
        $route = SingleRoute::$method('#^index$#', 'App\Http\Controllers\IndexController');

        static::assertEquals($expected, $route->method);
        static::assertEquals('#^index$#', $route->pattern);
        static::assertEquals('App\Http\Controllers\IndexController', $route->controller);
    }

    public function testSingleRouteMatch(): void
    {
        $route = SingleRoute::get('#^index$#', 'App\Http\Controllers\IndexController');
        static::assertEquals(RequestMethod::GET, $route->method);
        $resolvedRoute = $route->match(RequestMethod::GET, 'index');
        static::assertNotNull($resolvedRoute);
        static::assertEmpty($resolvedRoute->args);
        static::assertEquals('App\Http\Controllers\IndexController', $resolvedRoute->controller);
    }

    public function testSingleRouteMethodMisMatch(): void
    {
        $route = SingleRoute::get('#^index$#', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match(RequestMethod::POST, 'index');
        static::assertNull($resolvedRoute);
    }

    public function testSingleRoutePathMisMatch(): void
    {
        $route = SingleRoute::get('#^index$#', 'App\Http\Controllers\IndexController');
        $resolvedRoute = $route->match(RequestMethod::GET, 'home');
        static::assertNull($resolvedRoute);
    }

    public function testVariableRouteMatch(): void
    {
        $route = SingleRoute::post('#^user/(?P<userId>\d+)$#', 'App\Http\Controllers\UserController');
        static::assertEquals(RequestMethod::POST, $route->method);
        $resolvedRoute = $route->match(RequestMethod::POST, 'user/124');
        static::assertNotNull($resolvedRoute);
        static::assertArrayHasKey('userId', $resolvedRoute->args);
        static::assertEquals(124, $resolvedRoute->args['userId']);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);
    }
}
