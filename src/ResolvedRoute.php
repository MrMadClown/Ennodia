<?php

namespace Ennodia;

class ResolvedRoute
{
    public function __construct(public string $controller, public array $args)
    {
    }
}
