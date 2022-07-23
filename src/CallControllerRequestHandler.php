<?php

namespace Ennodia;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function call_user_func_array;
use function method_exists;

class CallControllerRequestHandler implements RequestHandlerInterface
{
    /** @param array<string, mixed> $urlParams */
    public function __construct(
        private readonly object        $controllerInstance,
        private readonly RequestMethod $method,
        private readonly array         $urlParams
    )
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return method_exists($this->controllerInstance, $this->method->value)
            ? call_user_func_array([$this->controllerInstance, $this->method->value], $this->urlParams)
            : call_user_func_array($this->controllerInstance, $this->urlParams);
    }
}
