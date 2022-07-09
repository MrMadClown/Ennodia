<?php

namespace Ennodia;

use function array_filter;
use function htmlentities;
use function is_numeric;
use function mb_strtoupper;
use function preg_match;
use function sprintf;
use function strcasecmp;

class SingleRoute implements Route
{
    public function __construct(
        public readonly string  $pattern,
        public readonly string  $controller,
        public readonly ?string $method = null
    )
    {
    }

    public function match(string $method, string $urlPath): ?ResolvedRoute
    {
        if ($this->method && strcasecmp($this->method, $method) !== 0) return null;
        if (preg_match($this->pattern, $urlPath, $matches)) {
            return new ResolvedRoute(
                $this->controller,
                array_filter($matches, static fn($key): bool => !is_numeric($key), ARRAY_FILTER_USE_KEY)
            );
        }
        return null;
    }

    public static function any(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller);
    }

    public static function get(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function head(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function post(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function put(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function delete(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function connect(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function options(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function patch(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function purge(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public static function trace(string $pattern, string $controller): Route
    {
        return new static($pattern, $controller, __FUNCTION__);
    }

    public function __toString(): string
    {
        return htmlentities(
            sprintf(
                "%s\t%s\t%s",
                mb_strtoupper($this->method ?? 'ANY'),
                $this->pattern,
                $this->controller
            )
        );
    }
}
