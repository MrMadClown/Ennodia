<?php
/**
 * Luca Perna - Webdeveloper
 * Team Dementia
 * luc@rissc.com
 *
 * Date: 09.07.22
 */

namespace Tests;

use Ennodia\RouteCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouteCollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = RouteCollection::make(
            [Request::METHOD_HEAD, Request::METHOD_GET, Request::METHOD_POST],
            '/^index$/', 'App\Http\Controllers\IndexController'
        );
        static::assertCount(3, $collection->routes);

        $resolvedRoute = $collection->match(Request::METHOD_GET, 'index');

        static::assertNotNull($resolvedRoute);
        static::assertEmpty($resolvedRoute->args);
        static::assertEquals('App\Http\Controllers\IndexController', $resolvedRoute->controller);

        $notResolvedRoute = $collection->match(Request::METHOD_POST, 'index');

        static::assertNotNull($notResolvedRoute);
    }
}
