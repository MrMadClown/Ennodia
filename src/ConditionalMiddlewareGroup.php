<?php

namespace MrMadClown\Ennodia;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function preg_match;
use function trim;

class ConditionalMiddlewareGroup extends MiddlewareGroup
{
    public function __construct(
        array                          $middlewares,
        public readonly ?string        $pattern = null,
        public readonly ?RequestMethod $method = null,
    )
    {
        parent::__construct($middlewares);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->method === null || $this->method === RequestMethod::tryFrom($request->getMethod())) {
            if ($this->pattern === null || preg_match($this->pattern, trim($request->getUri()->getPath(), '/'))) {
                return parent::process($request, $handler);
            }
        }
        return $handler->handle($request);
    }
}
