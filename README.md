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
$router = new Router($routes);
$response = $router($request);
```

A Controller either implements ``__invoke`` or ``get, post, put, patch, delete, etc..``

### Route Params
Define a route with a variable:
```php
 SingleRoute::get('/^user\/(?P<userId>\d+)$/', UserController::class),
```
The variables from the route are passed to the respective function in the controller:
```php
 class UserController {
    public function get(int $userId): Response {
        //...
    }
 }
```

The request object is passed to the controller in the constructor.
