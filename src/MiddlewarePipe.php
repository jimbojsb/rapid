<?php
namespace Rapid;

use Rapid\Route\AbstractRoute;

class MiddlewarePipe
{
    /** @var AbstractRoute */
    protected $route;
    /** @var MiddlewareInterface|MiddlewarePipe */
    protected $middleware;

    /**
     * MiddlewarePipe constructor.
     * @param MiddlewareInterface|MiddlewareInterface[]|MiddlewarePipe $middleware
     */
    public function __construct($middleware = null)
    {
        if ($middleware != null) {
            $this->middleware = $middleware;
        }
    }

    public function pipe()
    {

    }
}