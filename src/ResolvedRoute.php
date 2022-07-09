<?php

namespace Ennodia;

class ResolvedRoute
{
    public function __construct(public readonly string $controller, public readonly array $args)
    {
    }
}
