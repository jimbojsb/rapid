<?php
namespace Rapid\Route;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractRoute implements RouteMatcherInterface
{
    /** @var string[] */
    protected $methods = [];
    /** @var string */
    protected $spec;

    public function __construct(string $spec, array $methods = [])
    {
        $this->methods = $methods;
        $this->spec = $spec;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getSpec(): string
    {
        return $this->spec;
    }



    abstract public function match(ServerRequestInterface $request);
}