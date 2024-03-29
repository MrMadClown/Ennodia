<?php

namespace MrMadClown\Ennodia;

use Psr\Container\NotFoundExceptionInterface;

class ControllerNotFoundException extends \LogicException
{
    public static function make(string $controller, NotFoundExceptionInterface $previous): ControllerNotFoundException
    {
        return new ControllerNotFoundException(sprintf('Class %s not found', $controller), $previous->getCode(), $previous);
    }
}
