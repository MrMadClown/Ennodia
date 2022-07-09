<?php

namespace Ennodia;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function call_user_func_array;
use function method_exists;
use function str_ends_with;
use function strtolower;
use function substr;

class Router
{
    /**
     * @param RouteCollection $routes
     * @param array<mixed> $config
     */
    public function __construct(
        private readonly RouteCollection $routes,
        private readonly array  $config = [],
    )
    {
    }

    public function __invoke(Request $request): Response
    {
        $urlPath = $this->getUrlPathFromRequest($request);
        $route = $this->routes->match($request->getMethod(), $urlPath);

        if (!$route) {
            throw RouteNotFoundException::make($request->getMethod(), $urlPath);
        } else {
            return $this->callController($route->controller, $request, $route->args);
        }
    }

    protected function getUrlPathFromRequest(Request $request): string
    {
        $urlPath = $this->config['fallbackPath'] ?? 'index';

        if ($request->getPathInfo() !== '/') {
            $urlPath = substr($request->getPathInfo(), 1);
            if (str_ends_with($urlPath, '/')) {
                $urlPath = substr($urlPath, 0, -1);
            }
        }

        return $urlPath;
    }

    private function callController(string $controller, Request $request, array $urlParams): Response
    {
        $method = strtolower($request->getMethod());
        return method_exists($controller, $method)
            ? call_user_func_array([new $controller($request), $method], $urlParams)
            : call_user_func_array(new $controller($request), $urlParams);
    }
}
