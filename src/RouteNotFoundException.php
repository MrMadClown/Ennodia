<?php

namespace Ennodia;

use function sprintf;

class RouteNotFoundException extends \RuntimeException
{
    public static function make(string $method, string $urlPath): RouteNotFoundException
    {
        return new RouteNotFoundException(sprintf('No Route is defined for [%s] %s', $method, $urlPath));
    }
}
