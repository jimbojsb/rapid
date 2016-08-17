<?php
namespace Rapid;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Dispatcher
{
    /** @var MiddlewarePipe */
    protected $middlewarePipe;
    /** @var  ServerRequestInterface */
    protected $request;
    /** @var  ResponseInterface */
    protected $response;

    /**
     * Dispatcher constructor.
     * @param MiddlewarePipe $middlewarePipe
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(MiddlewarePipe $middlewarePipe, ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->middlewarePipe = $middlewarePipe;
        $this->request = $request;
        $this->response = $response;
    }

    public function dispatch()
    {

    }
}