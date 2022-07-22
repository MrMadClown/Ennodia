<?php

namespace Ennodia;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ContainerInterface $container, private readonly string $middleware)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->container
            ->get($this->middleware)
            ->process($request, $handler);
    }
}
