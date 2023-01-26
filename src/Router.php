<?php

namespace MrMadClown\Ennodia;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function trim;

class Router implements RequestHandlerInterface
{
    /** @param array{fallbackPath?: string} $config */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RouteCollection    $routes,
        private readonly MiddlewareGroup    $middleware = new MiddlewareGroup([]),
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
        $uri = $request->getUri();

        if ($uri->getPath() !== '/') {
            return trim($uri->getPath(), '/');
        }

        return $this->config['fallbackPath'] ?? 'index';
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

    private function makeController(string $controller): object
    {
        try {
            return $this->container->get($controller);
        } catch (NotFoundExceptionInterface $exception) {
            throw ControllerNotFoundException::make($controller, $exception);
        }
    }
}
