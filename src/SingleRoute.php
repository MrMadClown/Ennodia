<?php

namespace MrMadClown\Ennodia;

use function array_filter;
use function is_numeric;
use function preg_match;
use function strtoupper;

class SingleRoute implements Route
{
    public function __construct(
        public readonly string         $pattern,
        public readonly string         $controller,
        public readonly ?RequestMethod $method = null
    )
    {
    }

    public function match(RequestMethod $method, string $urlPath): ?ResolvedRoute
    {
        if ($this->method !== null && $this->method->value !== $method->value) return null;
        if (preg_match($this->pattern, $urlPath, $matches)) {
            return new ResolvedRoute(
                $this->controller,
                array_filter($matches, static fn($key): bool => !is_numeric($key), ARRAY_FILTER_USE_KEY)
            );
        }
        return null;
    }

    public static function any(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller);
    }

    public static function get(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function head(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function post(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function put(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function delete(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function connect(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function options(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function patch(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function purge(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }

    public static function trace(string $pattern, string $controller): SingleRoute
    {
        return new SingleRoute($pattern, $controller, RequestMethod::from(strtoupper(__FUNCTION__)));
    }
}
