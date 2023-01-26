<?php

namespace MrMadClown\Ennodia;

interface Route
{
    public function match(RequestMethod $method, string $urlPath): ?ResolvedRoute;
}
