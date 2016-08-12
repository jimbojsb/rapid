<?php
namespace Rapid\Route;

use Psr\Http\Message\ServerRequestInterface;

interface RouteMatcherInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return bool|ServerRequestInterface;
     */
    public function match(ServerRequestInterface $request);
}