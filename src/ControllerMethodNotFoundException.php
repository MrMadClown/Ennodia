<?php

namespace MrMadClown\Ennodia;

class ControllerMethodNotFoundException extends \LogicException
{
    public static function make(string $method, string $controller): ControllerMethodNotFoundException
    {
        return new ControllerMethodNotFoundException(sprintf('Method %s on class %s not found', $method, $controller));
    }
}
