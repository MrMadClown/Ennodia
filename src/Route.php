<?php

namespace Ennodia;

interface Route
{
    public function match(RequestMethod $method, string $urlPath): ?ResolvedRoute;
}
