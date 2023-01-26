<?php

namespace MrMadClown\Ennodia;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableRequestHandler implements RequestHandlerInterface
{
    /** @param callable $callable */
    public function __construct(private $callable)
    {
        if (!is_callable($this->callable)) throw new \TypeError(sprintf('Argument #1 ($callable) must be of type callable, %s given', gettype($this->callable)));
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callable)($request);
    }
}
