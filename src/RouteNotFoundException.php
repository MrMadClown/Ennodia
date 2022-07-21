<?php

namespace Ennodia;

use function sprintf;

class RouteNotFoundException extends \RuntimeException
{
    public static function make(RequestMethod $method, string $urlPath): RouteNotFoundException
    {
        return new RouteNotFoundException(sprintf('No Route is defined for [%s] %s', $method->value, $urlPath));
    }
}
