<?php

namespace MrMadClown\Ennodia\Tests;

use MrMadClown\Ennodia\ControllerMethodNotFoundException;
use MrMadClown\Ennodia\ControllerNotFoundException;
use MrMadClown\Ennodia\MiddlewareGroup;
use MrMadClown\Ennodia\RequestMethod;
use MrMadClown\Ennodia\RouteCollection;
use MrMadClown\Ennodia\RouteNotFoundException;
use MrMadClown\Ennodia\Router;
use MrMadClown\Ennodia\SingleRoute;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RouterTest extends TestCase
{
    public function testIndexPath(): void
    {
        $controller = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['__invoke'])
            ->getMock();
        $controller->expects(static::once())
            ->method('__invoke')
            ->willReturn(new Response(200, [], 'This is a Response'));

        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::once())
            ->method('get')
            ->with('App\Http\Controllers\IndexController')
            ->willReturn($controller);

        $router = new Router(
            $container,
            new RouteCollection([
                SingleRoute::get(
                    '#^index$#i',
                    'App\Http\Controllers\IndexController'
                )
            ]),
            new MiddlewareGroup([])
        );
        $response = $router->handle(new ServerRequest(RequestMethod::GET->value, 'https://github.com/'));
        static::assertEquals('This is a Response', $response->getBody()->getContents());
    }

    public function testFallbackPath(): void
    {
        $controller = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['__invoke'])
            ->getMock();
        $controller->expects(static::once())
            ->method('__invoke')
            ->willReturn(new Response(200, [], 'This is a Response'));

        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::once())
            ->method('get')
            ->with('App\Http\Controllers\FallbackController')
            ->willReturn($controller);

        $router = new Router(
            $container,
            new RouteCollection([
                SingleRoute::get(
                    '#^fallback#i',
                    'App\Http\Controllers\FallbackController'
                )
            ]),
            new MiddlewareGroup([]),
            ['fallbackPath' => 'fallback']
        );
        $response = $router->handle(new ServerRequest(RequestMethod::GET->value, 'https://github.com/'));
        static::assertEquals('This is a Response', $response->getBody()->getContents());
    }

    public function testRouteNotFound(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::never())->method('get');

        $router = new Router($container, new RouteCollection([]), new MiddlewareGroup([]));

        static::expectException(RouteNotFoundException::class);
        $router->handle(ServerRequest::fromGlobals());
    }

    public function testUrlPathStripping(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::once())
            ->method('get')
            ->with('App\Http\Controllers\IndexController')
            ->willThrowException(new class extends \LogicException implements NotFoundExceptionInterface {
            });

        $router = new Router(
            $container,
            new RouteCollection([SingleRoute::get('#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i', 'App\Http\Controllers\IndexController')]),
            new MiddlewareGroup([])
        );
        static::expectException(ControllerNotFoundException::class);
        static::expectExceptionMessage('App\Http\Controllers\IndexController');
        $router->handle(new ServerRequest(RequestMethod::GET->value, 'https://github.com/MrMadClown/ennodia/'));
    }

    public function testCallInvokableController(): void
    {
        $controller = new class ($this) {
            public function __construct(private readonly TestCase $testCase)
            {
            }

            public function __invoke(string $user, string $repository): Response
            {
                $this->testCase->assertEquals('MrMadClown', $user);
                $this->testCase->assertEquals('ennodia', $repository);
                return new Response(200, [], 'This is a Response');
            }
        };

        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::once())
            ->method('get')
            ->with('App\Http\Controllers\IndexController')
            ->willReturn($controller);

        $router = new Router(
            $container,
            new RouteCollection([
                SingleRoute::get(
                    '#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i',
                    'App\Http\Controllers\IndexController'
                )
            ]),
            new MiddlewareGroup([])
        );
        $response = $router->handle(new ServerRequest(RequestMethod::GET->value, 'https://github.com/MrMadClown/ennodia/'));
        static::assertEquals('This is a Response', $response->getBody()->getContents());
    }

    public function testCallControllerMethod(): void
    {
        $controller = new class ($this) {
            public function __construct(private readonly TestCase $testCase)
            {
            }

            public function get(string $user, string $repository): Response
            {
                $this->testCase->assertEquals('MrMadClown', $user);
                $this->testCase->assertEquals('ennodia', $repository);
                return new Response(200, [], 'This is a Response');
            }
        };

        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::once())
            ->method('get')
            ->with('App\Http\Controllers\IndexController')
            ->willReturn($controller);

        $router = new Router(
            $container,
            new RouteCollection([
                SingleRoute::get('#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i', 'App\Http\Controllers\IndexController')
            ]),
            new MiddlewareGroup([])
        );

        $response = $router->handle(new ServerRequest(RequestMethod::GET->value, 'https://github.com/MrMadClown/ennodia/'));
        static::assertEquals('This is a Response', $response->getBody()->getContents());
    }

    public function testControllerMethodNotFound(): void
    {
        $controller = new \stdClass();

        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::once())
            ->method('get')
            ->with('App\Http\Controllers\IndexController')
            ->willReturn($controller);

        $router = new Router(
            $container,
            new RouteCollection([
                SingleRoute::get('#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i', 'App\Http\Controllers\IndexController')
            ]),
            new MiddlewareGroup([])
        );

        static::expectException(ControllerMethodNotFoundException::class);
        $router->handle(new ServerRequest(RequestMethod::GET->value, 'https://github.com/MrMadClown/ennodia/'));
    }
}
