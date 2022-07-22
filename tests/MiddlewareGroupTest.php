<?php

namespace Tests;

use Ennodia\CallableRequestHandler;
use Ennodia\MiddlewareGroup;
use Ennodia\RequestMethod;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareGroupTest extends TestCase
{
    public function testProcess()
    {
        $middleWare = new MiddlewareGroup([new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return new Response(401, [], 'Not Allowed');
            }
        }]);
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $requestHandler->expects(static::never())->method('handle');
        $middleWare->process(new ServerRequest(RequestMethod::GET->value, 'https://github.com/MrMadClown/ennodia/'), $requestHandler);
    }

    public function testCallableRequestHandler()
    {
        $controller = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['__invoke'])
            ->getMock();
        $controller->expects(static::once())
            ->method('__invoke')
            ->with(static::isInstanceOf(ServerRequest::class))
            ->willReturn(new Response(200, [], 'This is a Response'));

        $callable = new CallableRequestHandler($controller);
        $callable->handle(new ServerRequest(RequestMethod::GET->value, 'https://github.com/MrMadClown/ennodia/'));
    }

    public function testCallableRequestHandlerConstructor()
    {
        static::expectException(\TypeError::class);
        static::expectExceptionMessage('Argument #1 ($callable) must be of type callable, object given');
        new CallableRequestHandler(new ServerRequest(RequestMethod::GET->value, 'https://github.com/MrMadClown/ennodia/'));
    }
}
