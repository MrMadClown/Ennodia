# Ennodia - a PHP Router
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)
[![Latest Stable Version](https://poser.pugx.org/mrmadclown/ennodia/v/stable.svg)](https://packagist.org/packages/mrmadclown/ennodia)
[![Total Downloads](https://poser.pugx.org/mrmadclown/ennodia/downloads)](https://packagist.org/packages/mrmadclown/ennodia)

This is a pretty straight forward Router, a Route consists of a regular expression, a qualified Controller path and optionally a HTTP Method.

### Installation
```bash
composer require mrmadclown/ennodia
```

The Router gets constructed by passing an Implementation of `Psr\Container\ContainerInterface::class` and a `Ennodia\RouteCollection::class`.

### Usage

```php
use Ennodia\RouteCollection;
use Ennodia\SingleRoute;
use Ennodia\Router;
use Ennodia\MiddlewareGroup;
use Ennodia\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

use App\Http\Controllers\IndexController;
use App\Container; // implements Psr\Container\ContainerInterface;

$routes = RouteCollection::collect([
    SingleRoute::get('#^index$#', IndexController::class),
]);
$request = Request::createFromGlobals();
$router = new Router(new Container(), $routes, new MiddlewareGroup([]));
$response = $router->handle($request);
```

A Controller either implements ``__invoke`` or ``get, post, put, patch, delete, etc..``

### Route Params
Define a route with a variable:
```php
 SingleRoute::get('#^user/(?P<userId>\d+)$#', UserController::class),
 SingleRoute::get('#^(?P<user>[a-z]+)/(?P<repository>[a-z]+)$#i', UserRepositoryController::class),
```
The variables from the route are passed to the respective function in the controller:
```php
class UserController {
    public function get(int $userId): Response {
        //...
    }
}

class UserRepositoryController {
    public function __invoke(string $user, string $repository): Response {
        //...
    }
 }
```
