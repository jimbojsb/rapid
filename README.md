# Rapid
Rapid is a PSR-7 / middleware dispatcher inpsired by Express.js.

## Installation

## Create your application
```
$application = new Rapid\Application();
// add middlewares here
$application->run();
```

## Define middlewares
Rapid accepts several values to represent a middleware. Any place Rapid 
expects a middleware as an argument, any of the following methods may be 
used

#### Passing an instance
If passing an instance, your instance should implement `Rapid\MiddlewareInterface`; 
This method is useful if you need to do custom instantiation of an 
instance for dependency injection.

```php
$application->use(new ExampleMiddleware());
```

#### Passing a class name
If your middleware doesn't need any custom instantiation, you can pass
a class name instead. Your class will not be instantiated unless the
system actually needs to use that given middleware.

```php
$application->use(ExampleMiddleware::class);
```

#### Passing a closure
A closure that with a `Rapid\MiddlewareInterface` compatible signature
may also be used. Type hinting the arguments is optional but recommended.

```php
$application->use(function(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
    
});
```

#### Using a middleware factory
`Rapid\Application` optionall accepts a `callable` that should be used
to produce middlewares when a classname is specified. This callable can 
be responsible for wiring and dependency injection as you see fit. The
factory should be a `callable` that takes one argument (a class name) and
returns a properly wired middleware of that class.

```php
$application->setMiddlewareFactory($factory);
```

## Composing and routing middleware
Middleware is evaluated top-to-bottom as defined in your application 
bootstrapping. Middleware may be applied to every request or applied 
using traditional routing methods that are evaluated against an instance 
of `\Psr\HttpMessage\ServerRequestInterface`; All middlewares will be 
checked against each request, and multiple matches are permitted and 
encouraged.

**Apply a middleware to every request**
```php
$application = new Rapid\Application();
$application->use(new ExampleMiddleware());
```

**Match middlewares based on method and simple path**
```php
$application->get("/test", TestMiddleware::class);
```

**Match middlewares on any method and simple path**
```php
$application->all("/test", TestMiddleware::class);
```

**Match middlewares on any method and simple path**
```php
$application->all("/test", TestMiddleware::class);
```

**Apply a group of middlewares to a given path**
```php
$application->get("/test", [TestMiddleware::class, ExampleMiddleware::class]);
```

**Apply a middleware to more than one method on the same route**
```php
$application->route(["POST", "PUT"], "/test", TestMiddleware::class);
```

**Apply different middlewares to different methods on the same route**
```php
$application->get("/test", GetMiddleware::class);
$application->post("/test", PostMiddleware::class);
```
Altnerate syntax:
```php
$application->route("/test")
            ->get(GetMiddleware::class)
            ->post(PostMiddleware::class);
```

**Using a custom route matcher**
Rapid supports using a custom `callable` for route matching. This may be 
useful in scenarios where a database needs to be consulted to see if a 
route matches or not. The callable should take one argument, a `\Psr\HttpMessage\RequestInterface` 
and return either `false` (no match) or a `\Psr\HttpMessage\RequestInterface` 
which may be the instance it was passed, or a mutated instance that it has 
decorated with attributes as necessary. A interface, `Rapid\RouteMatcherInterface` 
is provided for convenience but is not required.
```php
$application->get(function($request) {
    if ($request->getUri()->getPath = "/complex-logic") {
        return $request;    
    }
    return false;    
}, DataBackedMiddleware::class);
```

#### Handling errors
Rapid error handlers are also defined as middlewares. Error middlewares 
have a slightly different signature, and are described in their own 
`Rapid\Middleware\ErrorMiddlewareInterface`. The first argument to an 
error handling middleware is a `Throwable`. Rapid catches `Throwable` in 
it's dispatch cycle, so PHP 7.x `Error` throws are caught as well and can 
be processed by these middlewares. Before invoking the first error 
middleware, 

Rapid will set the `Throwable` as an attribute on the `$request` 
and change the `$response` status code to 404 or 500 if applicable. Rapid 
includes 2 special `Exceptions` for routing issues: `Rapid\Exception\MethodNotAllowedException` 
and `Rapid\Exception\NoRouteMatchException`.

```php
$application->use(ErrorMiddleware::class);
```