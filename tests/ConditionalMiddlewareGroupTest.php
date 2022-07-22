<?php

namespace Tests;

use Ennodia\ConditionalMiddlewareGroup;
use Ennodia\MiddlewareGroup;
use Ennodia\RequestMethod;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ConditionalMiddlewareGroupTest extends TestCase
{
    public function testProcessMatch()
    {
        $middleWare = new ConditionalMiddlewareGroup([new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return new Response(401, [], 'Not Allowed');
            }
        }], '#^protected/#', RequestMethod::GET);
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $requestHandler->expects(static::never())->method('handle');
        $middleWare->process(new ServerRequest(RequestMethod::GET->value, 'https://example.com/protected/super-secret/'), $requestHandler);
    }

    public function testProcessPathMisMatch()
    {
        $middleWare = new ConditionalMiddlewareGroup([new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return new Response(401, [], 'Not Allowed');
            }
        }], '#^protected/#', RequestMethod::GET);
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $requestHandler->expects(static::once())->method('handle')->willReturn(new Response(200, [], ''));
        $middleWare->process(new ServerRequest(RequestMethod::GET->value, 'https://example.com/public/not-super-secret/'), $requestHandler);
    }

    public function testProcessMethodMisMatch()
    {
        $middleWare = new ConditionalMiddlewareGroup([new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return new Response(401, [], 'Not Allowed');
            }
        }], '#^protected/#', RequestMethod::GET);
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $requestHandler->expects(static::once())->method('handle')->willReturn(new Response(200, [], ''));
        $middleWare->process(new ServerRequest(RequestMethod::POST->value, 'https://example.com/protected/super-secret/'), $requestHandler);
    }
}
