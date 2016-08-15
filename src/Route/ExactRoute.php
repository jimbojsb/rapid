<?php
namespace Rapid\Route;

use Psr\Http\Message\ServerRequestInterface;
use Rapid\Exception\MethodNotAllowedException;

class ExactRoute extends AbstractRoute
{
    public function match(ServerRequestInterface $request)
    {
        if ($request->getUri()->getPath() == $this->spec) {
            if (in_array($request->getMethod(), $this->methods)) {
                return $request;
            } else {
                throw new MethodNotAllowedException();
            }
        }
        return false;
    }
}