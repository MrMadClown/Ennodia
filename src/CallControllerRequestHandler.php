<?php

namespace MrMadClown\Ennodia;

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
        if (method_exists($this->controllerInstance, $this->method->value)) {
            return call_user_func_array($this->controllerInstance->{$this->method->value}(...), $this->urlParams);
        } else if (method_exists($this->controllerInstance, '__invoke')) {
            return call_user_func_array(($this->controllerInstance)(...), $this->urlParams);
        }

        throw ControllerMethodNotFoundException::make($this->method->value,$this->controllerInstance::class);
    }
}
