<?php

namespace MrMadClown\Ennodia\Tests;

use MrMadClown\Ennodia\LazyMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyMiddlewareTest extends TestCase
{
    public function testProcess()
    {
        $middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $middleware->expects(static::once())
            ->method('process')
            ->willReturn(new Response(200, [], ''));
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->expects(static::once())
            ->method('get')
            ->with('App\Http\Middleware\AuthMiddleware')
            ->willReturn($middleware);

        $lazy = new LazyMiddleware($container, 'App\Http\Middleware\AuthMiddleware');

        $lazy->process(new ServerRequest('GET', '', [], null), new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, [], '');
            }
        });
    }
}
