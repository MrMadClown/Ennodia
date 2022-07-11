# Ennodia - a PHP Router

### Usage

```php
use Ennodia\RouteCollection;
use Ennodia\SingleRoute;
use Ennodia\Router;
use Ennodia\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

use App\Http\Controllers\IndexController;

$routes = RouteCollection::collect([
    SingleRoute::get('/^index$/', IndexController::class),
]);
$request = Request::createFromGlobals();
$router = (new RouterFactory())->make($routes);
$response = $router($request);
```

A Controller either implements ``__invoke`` or ``get, post, put, patch, delete, etc..``

### Route Params
Define a route with a variable:
```php
 SingleRoute::get('/^user\/(?P<userId>\d+)$/', UserController::class),
 SingleRoute::get('/^(?P<user>[a-z]+)\/(?P<repository>[a-z]+)$/i', UserController::class),
```
The variables from the route are passed to the respective function in the controller:
```php
 class UserController {
    public function get(int $userId): Response {
        //...
    }
    // or __invoke if the Controller does not implement the HTTP Method directly
    public function __invoke(int $userId): Response {
        //...
    }
 }
```

The request object is passed to the controller in the constructor.
