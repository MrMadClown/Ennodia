<?php

namespace Ennodia;

class ResolvedRoute
{
    /** @param array<string, mixed> $args */
    public function __construct(public readonly string $controller, public readonly array $args)
    {
    }
}
