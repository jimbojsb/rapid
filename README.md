# Rapid
Rapid is a PSR-7 / middleware dispatcher inpsired by Express.js.
Rapid's only external dependency is the PSR-7 `Psr\HttpMessage` interfaces. 
Rapid requires PHP 7.0 or above.

## Installation
```
$ composer require offers/rapid
```

## Create your application
```php
$application = new Rapid\Application();
// add middlewares here
$application->run();
```

## Define middlewares
Rapid accepts several values to represent a middleware. 
Any place Rapid expects a middleware as an argument, any of the following methods may be used

#### Passing an instance
If passing an instance, your instance should implement `Rapid\MiddlewareInterface`
This method is useful if you need to do custom instantiation of an instance for dependency injection.

```php
$application->use(new ExampleMiddleware());
```

#### Passing a class name
If your middleware doesn't need any custom instantiation, you can pass a class name instead. Your class will not be instantiated unless the system actually needs to use that given middleware.

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
`Rapid\Application` optionall accepts a `callable` that should be used to produce middlewares when a classname is specified. 
This callable can be responsible for wiring and dependency injection as you see fit. 
The factory should be a `callable` that takes one argument (a class name) and returns a properly wired middleware of that class.
An interface, `Radpid\MiddlewareFactoryInterface` is provided for convenience but not required.

```php
$application->setMiddlewareFactory($factory);
```

## Composing and routing middleware
Middleware is evaluated top-to-bottom as defined in your application bootstrapping. 
Middleware may be applied to every request or applied using traditional routing methods that are evaluated against an instance of `\Psr\HttpMessage\ServerRequestInterface`. 
All middlewares will be checked against each request, and multiple matches are permitted and encouraged.

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

## Routing
Rapid includes a built-in router / url matcher.

URLs that start with `/` and contain no special operators as noted below are treated as an exact string match to the request path. In the following exmaple, GetMiddleware will be invoked if the path of the request uri is exactly `/test`
```php
$application->get("/test", GetMiddleware::class);
```

URLs that start with a `/` and contain a `{}` are matched with a system-generated regular expression. Path segments surrounded with `{}` are treated as variables and the values of those segments will be set as attributes on the request object.
In the following example, a request path of `/test/myslug` results in a request attribute of `slug` being set to `myslug`.
```php
$application->get("/test/{slug}", GetMiddleware::class);
```

At the end of any given path prefix, a special marker `{?}` may be added that treats the remainder of any path match as a Rails-style key-value route.
In the following example, a request path of `/test/param1/value1/param2/value2` results in a request attributes of `param1` and `param2` being set to `value1` and `value2` respectively. 
*Note: This route matches on 0 or more options, so it will also match for `/test`* 
```php
$application->get("/test{?}", GetMiddleware::class);
```

Path specifications that are surrounded by backticks are treated as regular expressions and are passed to `preg_match()` as written. If the regeular expression matches, the `$matches` array is set as a request attribute.
```php
$application->get("`/test.*?`", GetMiddleware::class);
```

**Using a custom route matcher**

Rapid supports using a custom `callable` for route matching. This may be useful in scenarios where a database needs to be consulted to see if a route matches or not. 
The callable should take one argument, a `\Psr\HttpMessage\RequestInterface` and return either `false` (no match) or a `\Psr\HttpMessage\RequestInterface` which may be the instance it was passed, or a mutated instance that it has decorated with attributes as necessary. 
An interface, `Rapid\RouteMatcherInterface` is provided for convenience but is not required.
```php
$application->get(function($request) {
    if ($request->getUri()->getPath = "/complex-logic") {
        return $request;    
    }
    return false;    
}, DataBackedMiddleware::class);
```

## Using middleware pipes
The convenience methods on the `Rapid\Application` object are actually proxies to the underlying container object `Rapid\MiddlewarePipe`.
It is possible to use middleware pipes directly for even more flexibility and reusability at bootstrap time.

Create a new middleware pipe with a single middleware and use it:
```php
$pipe = new \Radid\MiddlewarePipe(RunAlwaysMiddleware::class);
$application->use($pipe);
```

Create a new middleware pipe with multiple middlewares
```php
$pipe = new \Radid\MiddlewarePipe([RunAlwaysMiddleware::class, AnotherMiddleware::class]);
$application->use($pipe);
```

Add a middleware to an existing pipe
```php
$pipe = new \Radid\MiddlewarePipe();
$myMiddleware = new MyMiddleware();
$pipe->pipe($myMiddleware);
```

Middleware pipes are also fully routing-aware. The pipe method will also accept a closure. Inside the closure, you may use the same syntax as the convenience methods on the application object.
```php
$pipe = new \Radid\MiddlewarePipe();
$pipe->pipe(function() {
    $this->use(MiddlewareOne::class);
    $this->get('/prefix', PrefixMiddleware::class);
});
```

A more concrete example using pipes for organizational purposes. Middleware pipes can be subsituted wherever a middleware would be used.
```php
$publicPipe = new \Radid\MiddlewarePipe();
$publicPipe->pipe(function() {
   $this->get("/", HomepageMiddleware::class);
   $this->get("/login", LoginMiddleware::class);
});
$application->use($publicPipe);

$privateDirectoryPipe = (new Rapid\MiddlewarePipe())->pipe(function() {
    $this->get("/user", (new MiddlewarePipe)->pipe(function() {
        $this->use(AuthMiddleware::class);
        $this->get("/user/dashboard", DashBoardMiddleware::class);
        $this->get("/user/settings", SettingsMiddleware::class);    
    }));
});
$application->use($privateDirectoryPipe);
```

## Handling errors
Rapid error handlers are also defined as middlewares. 
Error middlewares have a slightly different signature, and are described in their own `Rapid\Middleware\ErrorMiddlewareInterface`. 
The first argument to an error handling middleware is a `Throwable`. 


Rapid catches `Throwable` in it's dispatch cycle, so PHP 7.x `Error` throws are caught as well and can be processed by these middlewares. 
Before invoking the first error middleware, Rapid will set the `Throwable` as an attribute on the `$request` and change the `$response` status code to 404 or 500 if applicable. 
Rapid includes 2 special `Exceptions` for routing issues: `Rapid\Exception\MethodNotAllowedException` and `Rapid\Exception\NoRouteMatchException`.

```php
$application->use(ErrorMiddleware::class);
```