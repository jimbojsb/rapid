<?php
namespace Rapid\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorMiddlewareInterface
{
    public function __invoke(\Throwable $e, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}