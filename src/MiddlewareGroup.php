<?php

namespace Ennodia;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareGroup implements MiddlewareInterface
{
    public function __construct(private readonly array $middlewares)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->getRequestHandler($handler)->handle($request);
    }

    private function getRequestHandler(RequestHandlerInterface $handler): RequestHandlerInterface
    {
        return array_reduce($this->middlewares, static function (RequestHandlerInterface $ax, MiddlewareInterface $dx): RequestHandlerInterface {
            return new CallableRequestHandler(static fn(ServerRequestInterface $request) => $dx->process($request, $ax));
        }, $handler);
    }
}
