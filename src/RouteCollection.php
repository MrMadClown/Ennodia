<?php

namespace Ennodia;

use function array_map;
use function implode;
use function sprintf;

class RouteCollection implements Route
{
    /** @param array<Route> $routes */
    public function __construct(public array $routes)
    {
    }

    public static function collect(array $routes): RouteCollection
    {
        return new RouteCollection($routes);
    }

    public static function make(array $methods, string $pattern, string $controller): RouteCollection
    {
        return new RouteCollection(
            array_map(
                static fn(string $method): SingleRoute => new SingleRoute($pattern, $controller, $method),
                $methods
            )
        );
    }

    public static function resource(string $pattern, string $controller): RouteCollection
    {
        return static::make(['GET', 'PATCH', 'PUT', 'POST', 'DELETE'], $pattern, $controller);
    }

    public function match(string $method, string $urlPath): ?ResolvedRoute
    {
        foreach ($this->routes as $route) {
            if ($resolvedRoute = $route->match($method, $urlPath)) return $resolvedRoute;
        }
        return null;
    }

    public function __toString(): string
    {
        return sprintf(
            '<ul>%s</ul>',
            implode("<br>", array_map(static fn(Route $r): string => sprintf('<li>%s</li>', $r), $this->routes))
        );
    }
}
