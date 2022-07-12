<?php

namespace Ennodia;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function call_user_func_array;
use function method_exists;
use function str_ends_with;
use function strtolower;
use function substr;

class Router
{
    /** @param array<string, mixed> $config */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RouteCollection    $routes,
        private readonly array              $config = [],
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

    /** @param array<string, mixed> $urlParams */
    private function callController(string $controller, Request $request, array $urlParams): Response
    {
        $method = strtolower($request->getMethod());
        $controllerInstance = $this->makeController($controller);
        return method_exists($controllerInstance, $method)
            ? call_user_func_array([$controllerInstance, $method], $urlParams)
            : call_user_func_array($controllerInstance, $urlParams);
    }

    private function makeController(string $controller): mixed
    {
        try {
            return $this->container->get($controller);
        } catch (NotFoundExceptionInterface $exception) {
            throw ControllerNotFoundException::make($controller, $exception);
        }
    }
}
