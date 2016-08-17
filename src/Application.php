<?php
namespace Rapid;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class Application
{
    /** @var MiddlewarePipe */
    protected $middlewarePipe;
    /** @var  ServerRequestInterface */
    protected $request;
    /** @var  ResponseInterface */
    protected $response;
    /** @var  Dispatcher */
    protected $dispatcher;

    public function __construct()
    {
        $this->dispatcher = new Dispatcher(new MiddlewarePipe, ServerRequestFactory::fromGlobals(), new Response);
    }

    public function init()
    {
    }

    public function run()
    {

    }
}
