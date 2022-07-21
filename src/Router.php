<?php

namespace Ennodia;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function str_ends_with;
use function substr;

class Router implements RequestHandlerInterface
{
    /** @param array<string, mixed> $config */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RouteCollection    $routes,
        private readonly Middleware         $middleware = new Middleware([]),
        private readonly array              $config = [],
    )
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestMethod = RequestMethod::from($request->getMethod());
        $urlPath = $this->getUrlPathFromRequest($request);
        $route = $this->routes->match($requestMethod, $urlPath);

        if (!$route) {
            throw RouteNotFoundException::make($requestMethod, $urlPath);
        } else {
            return $this->callController($route->controller, $request, $route->args);
        }
    }

    private function getUrlPathFromRequest(ServerRequestInterface $request): string
    {
        $urlPath = $this->config['fallbackPath'] ?? 'index';
        $uri = $request->getUri();
        if ($uri->getPath() !== '/') {
            $urlPath = substr($uri->getPath(), 1);
            if (str_ends_with($urlPath, '/')) {
                $urlPath = substr($urlPath, 0, -1);
            }
        }

        return $urlPath;
    }

    /** @param array<string, mixed> $urlParams */
    private function callController(string $controller, ServerRequestInterface $request, array $urlParams): ResponseInterface
    {
        return $this->middleware->process(
            $request,
            new CallControllerRequestHandler(
                $this->makeController($controller),
                RequestMethod::from($request->getMethod()),
                $urlParams
            )
        );
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
