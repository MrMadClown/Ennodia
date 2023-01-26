<?php

namespace MrMadClown\Ennodia\Tests;

use MrMadClown\Ennodia\RequestMethod;
use MrMadClown\Ennodia\RouteCollection;
use MrMadClown\Ennodia\SingleRoute;
use PHPUnit\Framework\TestCase;

class RouteCollectionTest extends TestCase
{
    public function testResourceCollection(): void
    {
        $collection = RouteCollection::resource(
            'user',
            '(?P<userId>\d+)',
            'App\Http\Controllers\UserController'
        );
        static::assertCount(5, $collection->routes);

        static::assertNotNull($resolvedRoute = $collection->match(RequestMethod::GET, 'user'));
        static::assertEmpty($resolvedRoute->args);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);

        static::assertNotNull($resolvedRoute = $collection->match(RequestMethod::POST, 'user'));
        static::assertEmpty($resolvedRoute->args);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);

        static::assertNotNull($resolvedRoute = $collection->match(RequestMethod::GET, 'user/123'));
        static::assertArrayHasKey('userId', $resolvedRoute->args);
        static::assertEquals(123, $resolvedRoute->args['userId']);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);

        static::assertNotNull($resolvedRoute = $collection->match(RequestMethod::PUT, 'user/123'));
        static::assertArrayHasKey('userId', $resolvedRoute->args);
        static::assertEquals(123, $resolvedRoute->args['userId']);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);

        static::assertNotNull($resolvedRoute = $collection->match(RequestMethod::DELETE, 'user/123'));
        static::assertArrayHasKey('userId', $resolvedRoute->args);
        static::assertEquals(123, $resolvedRoute->args['userId']);
        static::assertEquals('App\Http\Controllers\UserController', $resolvedRoute->controller);
    }

    public function testCollection(): void
    {
        $collection = RouteCollection::collect([
            SingleRoute::get('#^index$#', 'App\Http\Controllers\GetController'),
            SingleRoute::post('#^index$#', 'App\Http\Controllers\PostController')
        ]);
        static::assertCount(2, $collection->routes);

        $resolvedGetRoute = $collection->match(RequestMethod::GET, 'index');
        static::assertNotNull($resolvedGetRoute);
        static::assertEquals('App\Http\Controllers\GetController', $resolvedGetRoute->controller);
        $resolvedPostRoute = $collection->match(RequestMethod::POST, 'index');
        static::assertNotNull($resolvedPostRoute);
        static::assertEquals('App\Http\Controllers\PostController', $resolvedPostRoute->controller);
    }

    public function testMake(): void
    {
        $collection = RouteCollection::make([RequestMethod::GET, RequestMethod::HEAD], '#^index$#', 'App\Http\Controllers\GetController');
        static::assertCount(2, $collection->routes);

        static::assertNotNull($resolvedGetRoute = $collection->match(RequestMethod::GET, 'index'));
        static::assertEquals('App\Http\Controllers\GetController', $resolvedGetRoute->controller);
        static::assertNotNull($resolvedPostRoute = $collection->match(RequestMethod::HEAD, 'index'));
        static::assertEquals('App\Http\Controllers\GetController', $resolvedPostRoute->controller);
    }
}
