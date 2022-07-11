<?php

namespace Ennodia;

interface Route
{
    public function match(string $method, string $urlPath): ?ResolvedRoute;
}
