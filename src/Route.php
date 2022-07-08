<?php

namespace Ennodia;

interface Route extends \Stringable
{
    public function match(string $method, string $urlPath): ?ResolvedRoute;
}
