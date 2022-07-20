<?php

namespace Tests;

use Ennodia\ControllerNotFoundException;
use Ennodia\RouteCollection;
use Ennodia\RouteNotFoundException;
use Ennodia\Router;
use Ennodia\SingleRoute;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouterTest extends TestCase
{
    public function testIndexPath(): void
    {
        $router = new Router(
            new class ($this) implements ContainerInterface {
                public function __construct(private readonly TestCase $testCase)
                {
                }

                public function get(string $id)
                {
                    $this->testCase->assertEquals('App\Http\Controllers\IndexController', $id);
                    return new class () {
                        public function __invoke(): Response
                        {
                            return new Response('This is a Response');
                        }
                    };
                }

                public function has(string $id): bool
                {
                    return false;
                }
            },
            new RouteCollection([
                SingleRoute::get(
                    '#^index$#i',
                    'App\Http\Controllers\IndexController'
                )
            ])
        );
        $response = $router(Request::create('https://github.com/', Request::METHOD_GET));
        static::assertEquals('This is a Response', $response->getContent());
    }

    public function testFallbackPath(): void
    {
        $router = new Router(
            new class ($this) implements ContainerInterface {
                public function __construct(private readonly TestCase $testCase)
                {
                }

                public function get(string $id)
                {
                    $this->testCase->assertEquals('App\Http\Controllers\FallbackController', $id);
                    return new class () {
                        public function __invoke(): Response
                        {
                            return new Response('This is a Response');
                        }
                    };
                }

                public function has(string $id): bool
                {
                    return false;
                }
            },
            new RouteCollection([
                SingleRoute::get(
                    '#^fallback#i',
                    'App\Http\Controllers\FallbackController'
                )
            ]),
            ['fallbackPath' => 'fallback']
        );
        $response = $router(Request::create('https://github.com/', Request::METHOD_GET));
        static::assertEquals('This is a Response', $response->getContent());
    }

    public function testRouteNotFound(): void
    {
        $router = new Router(new class implements ContainerInterface {
            public function get(string $id)
            {
            }

            public function has(string $id): bool
            {
                return false;
            }
        }, new RouteCollection([]));

        static::expectException(RouteNotFoundException::class);
        $router(Request::createFromGlobals());
    }

    public function testUrlPathStripping(): void
    {
        $router = new Router(new class implements ContainerInterface {
            public function get(string $id)
            {
                throw new class extends \LogicException implements NotFoundExceptionInterface {
                };
            }

            public function has(string $id): bool
            {
                return false;
            }
        }, new RouteCollection([SingleRoute::get('#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i', 'App\Http\Controllers\IndexController')]));
        static::expectException(ControllerNotFoundException::class);
        static::expectErrorMessage('App\Http\Controllers\IndexController');
        $router(Request::create('https://github.com/MrMadClown/ennodia/', Request::METHOD_GET));
    }

    public function testCallInvokableController(): void
    {
        $router = new Router(
            new class ($this) implements ContainerInterface {
                public function __construct(private readonly TestCase $testCase)
                {
                }

                public function get(string $id)
                {
                    $this->testCase->assertEquals('App\Http\Controllers\IndexController', $id);
                    return new class ($this->testCase) {
                        public function __construct(private readonly TestCase $testCase)
                        {
                        }

                        public function __invoke(string $user, string $repository): Response
                        {
                            $this->testCase->assertEquals('MrMadClown', $user);
                            $this->testCase->assertEquals('ennodia', $repository);
                            return new Response('This is a Response');
                        }
                    };
                }

                public function has(string $id): bool
                {
                    return false;
                }
            },
            new RouteCollection([
                SingleRoute::get(
                    '#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i',
                    'App\Http\Controllers\IndexController'
                )
            ])
        );
        $response = $router(Request::create('https://github.com/MrMadClown/ennodia/', Request::METHOD_GET));
        static::assertEquals('This is a Response', $response->getContent());
    }

    public function testCallControllerMethod(): void
    {
        $router = new Router(
            new class ($this) implements ContainerInterface {
                public function __construct(private readonly TestCase $testCase)
                {
                }

                public function get(string $id)
                {
                    $this->testCase->assertEquals('App\Http\Controllers\IndexController', $id);
                    return new class ($this->testCase) {
                        public function __construct(private readonly TestCase $testCase)
                        {
                        }

                        public function get(string $user, string $repository): Response
                        {
                            $this->testCase->assertEquals('MrMadClown', $user);
                            $this->testCase->assertEquals('ennodia', $repository);
                            return new Response('This is a Response');
                        }
                    };
                }

                public function has(string $id): bool
                {
                    return false;
                }
            },
            new RouteCollection([
                SingleRoute::get('#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i', 'App\Http\Controllers\IndexController')
            ])
        );

        $response = $router(Request::create('https://github.com/MrMadClown/ennodia/', Request::METHOD_GET));
        static::assertEquals('This is a Response', $response->getContent());
    }
}
