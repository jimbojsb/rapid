<?php
namespace Rapid\Route;

class RouteFactory
{
    /**
     * @param string $routeSpec
     * @param string|array $method
     * @return AbstractRoute
     */
    public function __invoke(string $routeSpec, $method): AbstractRoute
    {
        if (is_string($method)) {
            $method = [$method];
        }

        $class = ExactRoute::class;

        if (strpos($routeSpec, "{") !== false && strpos($routeSpec, "}") !== false) {
            $class = VariableRoute::class;
        }

        if (substr($routeSpec, 0, 1) == "`" && substr($routeSpec, -1, 1) == "`") {
            $class = RegexRoute::class;
        }

        if (substr($routeSpec, -3, 3) == "{?}") {
            $class = KeyValueRoute::class;
        }

        return new $class($routeSpec, $method);
    }
}