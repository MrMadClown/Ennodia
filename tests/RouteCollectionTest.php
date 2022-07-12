<?php

namespace Tests;

use Ennodia\RouteCollection;
use Ennodia\SingleRoute;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouteCollectionTest extends TestCase
{
    public function testResourceCollection(): void
    {
        $collection = RouteCollection::resource(
            '#^index$#',
            'App\Http\Controllers\IndexController'
        );
        static::assertCount(4, $collection->routes);
        static::assertNotNull($resolvedRoute = $collection->match(Request::METHOD_GET, 'index'));
        static::assertNotNull($collection->match(Request::METHOD_POST, 'index'));
        static::assertNotNull($collection->match(Request::METHOD_PUT, 'index'));
        static::assertNotNull($collection->match(Request::METHOD_DELETE, 'index'));
        static::assertNull($collection->match(Request::METHOD_PATCH, 'index'));

        static::assertEmpty($resolvedRoute->args);
        static::assertEquals('App\Http\Controllers\IndexController', $resolvedRoute->controller);
    }

    public function testCollection(): void
    {
        $collection = RouteCollection::collect([
            SingleRoute::get('#^index$#', 'App\Http\Controllers\GetController'),
            SingleRoute::post('#^index$#', 'App\Http\Controllers\PostController')
        ]);
        static::assertCount(2, $collection->routes);

        static::assertNotNull($resolvedGetRoute = $collection->match(Request::METHOD_GET, 'index'));
        static::assertEquals('App\Http\Controllers\GetController', $resolvedGetRoute->controller);
        static::assertNotNull($resolvedPostRoute = $collection->match(Request::METHOD_POST, 'index'));
        static::assertEquals('App\Http\Controllers\PostController', $resolvedPostRoute->controller);
    }
}
