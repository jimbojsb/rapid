<?php
class RouteFactoryTest extends PHPUnit\Framework\TestCase
{
    public function testExactRouteFactory()
    {
        $rf = new \Rapid\Route\RouteFactory();
        $r = $rf("/test", "GET");
        $this->assertInstanceOf(\Rapid\Route\ExactRoute::class, $r);
        $this->assertEquals(["GET"], $r->getMethods());
        $this->assertEquals("/test", $r->getSpec());
    }

    public function testRegexRouteFactory()
    {
        $rf = new \Rapid\Route\RouteFactory();
        $r = $rf("`/test`", "GET");
        $this->assertInstanceOf(\Rapid\Route\RegexRoute::class, $r);
        $this->assertEquals(["GET"], $r->getMethods());
        $this->assertEquals("`/test`", $r->getSpec());
    }

    public function testKeyValueRouteFactory()
    {
        $rf = new \Rapid\Route\RouteFactory();
        $r = $rf("/test{?}", "GET");
        $this->assertInstanceOf(\Rapid\Route\KeyValueRoute::class, $r);
        $this->assertEquals(["GET"], $r->getMethods());
        $this->assertEquals("/test{?}", $r->getSpec());
    }

    public function testVariableRoute()
    {
        $rf = new \Rapid\Route\RouteFactory();
        $r = $rf("/test/{slug}", "GET");
        $this->assertInstanceOf(\Rapid\Route\VariableRoute::class, $r);
        $this->assertEquals(["GET"], $r->getMethods());
        $this->assertEquals("/test/{slug}", $r->getSpec());
    }

    public function testMultipleMethods()
    {
        $rf = new \Rapid\Route\RouteFactory();
        $r = $rf("/test", ["GET", "POST"]);
        $this->assertEquals(["GET", "POST"], $r->getMethods());
    }
}