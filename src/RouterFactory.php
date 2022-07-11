<?php

namespace Ennodia;

class RouterFactory
{
    private readonly ?\Closure $controllerFactory;

    public function __construct(?\Closure $controllerFactory = null)
    {
        $this->controllerFactory = $controllerFactory ?? static function (string $controller, array $arguments): mixed {
                return new $controller(...$arguments);
            };
    }

    /**
     * @param array<string, mixed> $config
     */
    public function make(RouteCollection $routes, array $config = []): Router
    {
        return new Router(
            $this->controllerFactory,
            $routes,
            $config
        );
    }
}
