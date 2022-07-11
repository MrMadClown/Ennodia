<?php
/**
 * Luca Perna - Webdeveloper
 * Team Dementia
 * luc@rissc.com
 *
 * Date: 10.07.22
 */

namespace Tests;

use Ennodia\RouteCollection;
use Ennodia\RouteNotFoundException;
use Ennodia\RouterFactory;
use Ennodia\SingleRoute;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouterTest extends TestCase
{
    public function testRouteNotFound(): void
    {
        $router = (new RouterFactory())->make(new RouteCollection([]));

        static::expectException(RouteNotFoundException::class);
        $router(Request::createFromGlobals());
    }

    public function testUrlPathStripping(): void
    {
        $router = (new RouterFactory())->make(new RouteCollection([
            SingleRoute::get('/^(?P<user>[a-z]+)\/(?P<repository>[a-z]+)$/i', 'App\Http\Controllers\IndexController')
        ]));
        static::expectErrorMessage('App\Http\Controllers\IndexController');
        $router(Request::create('https://github.com/MrMadClown/ennodia/', Request::METHOD_GET));
    }

    public function testCallInvokableController(): void
    {
        $router = (new RouterFactory(function (string $controller) {
            static::assertEquals('App\Http\Controllers\IndexController', $controller);

            return new class ($this) {
                public function __construct(private TestCase $testCase)
                {
                }

                public function __invoke(string $user, string $repository): Response
                {
                    $this->testCase->assertEquals('MrMadClown', $user);
                    $this->testCase->assertEquals('ennodia', $repository);
                    return new Response('This is a Response');
                }
            };
        }))->make(new RouteCollection([
            SingleRoute::get('/^(?P<user>[a-z]+)\/(?P<repository>[a-z]+)$/i', 'App\Http\Controllers\IndexController')
        ]));

        $response = $router(Request::create('https://github.com/MrMadClown/ennodia/', Request::METHOD_GET));
        static::assertEquals('This is a Response', $response->getContent());
    }

    public function testCallControllerMethod(): void
    {
        $router = (new RouterFactory(function (string $controller, array $args) {
            static::assertEquals('App\Http\Controllers\IndexController', $controller);

            $assertEquals = fn($a, $b) => $this->assertEquals($a, $b);

            return new class ($assertEquals) {
                public function __construct(private readonly \Closure $assertEquals)
                {
                }

                public function get(string $user, string $repository): Response
                {
                    ($this->assertEquals)('MrMadClown', $user);
                    ($this->assertEquals)('ennodia', $repository);
                    return new Response('This is a Response');
                }
            };
        }))->make(new RouteCollection([
            SingleRoute::get('/^(?P<user>[a-z]+)\/(?P<repository>[a-z]+)$/i', 'App\Http\Controllers\IndexController')
        ]));

        $response = $router(Request::create('https://github.com/MrMadClown/ennodia/', Request::METHOD_GET));
        static::assertEquals('This is a Response', $response->getContent());
    }
}
