<?php

namespace MrMadClown\Ennodia;

use function array_map;
use function sprintf;

class RouteCollection implements Route
{
    /** @param array<Route> $routes */
    public function __construct(public readonly array $routes)
    {
    }

    /** @param array<Route> $routes */
    public static function collect(array $routes): RouteCollection
    {
        return new RouteCollection($routes);
    }

    /** @param array<RequestMethod> $methods */
    public static function make(array $methods, string $pattern, string $controller): RouteCollection
    {
        return new RouteCollection(
            array_map(
                static fn(RequestMethod $method): SingleRoute => new SingleRoute($pattern, $controller, $method),
                $methods
            )
        );
    }

    public static function resource(string $path, string $resourceIdentifierPattern, string $controller): RouteCollection
    {
        return static::collect([
            new SingleRoute(sprintf('#^%s$#', $path), $controller, RequestMethod::GET),
            new SingleRoute(sprintf('#^%s$#', $path), $controller, RequestMethod::POST),

            new SingleRoute(sprintf('#^%s/%s$#', $path, $resourceIdentifierPattern), $controller, RequestMethod::GET),
            new SingleRoute(sprintf('#^%s/%s$#', $path, $resourceIdentifierPattern), $controller, RequestMethod::PUT),
            new SingleRoute(sprintf('#^%s/%s$#', $path, $resourceIdentifierPattern), $controller, RequestMethod::DELETE)
        ]);
    }

    public function match(RequestMethod $method, string $urlPath): ?ResolvedRoute
    {
        foreach ($this->routes as $route) {
            if ($resolvedRoute = $route->match($method, $urlPath)) return $resolvedRoute;
        }
        return null;
    }
}
