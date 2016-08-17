<?php
class ExactRouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatchWithSingleMethod()
    {
        $r = new \Rapid\Route\ExactRoute("/test", ["GET"]);
        $req = $this->requestWithUri("/test");
        $this->assertEquals($req, $r->match($req));
        $this->assertEquals(false, $r->match($this->requestWithUri("/foo")));
    }

    public function testMatchWithMultipleMethods()
    {
        $r = new \Rapid\Route\ExactRoute("/test", ["GET", "POST"]);
        $req = $this->requestWithUri("/test");
        $postReq = $req->withMethod("POST");
        $this->assertEquals($req, $r->match($req));
        $this->assertEquals($postReq, $r->match($postReq));
    }

    public function testMethodNotAllowed()
    {
        $r = new \Rapid\Route\ExactRoute("/test", ["POST"]);
        $req = $this->requestWithUri("/test");
        $postReq = $req->withMethod("POST");
        $this->assertEquals($postReq, $r->match($postReq));
        $this->setExpectedException(\Rapid\Exception\MethodNotAllowedException::class);
        $this->assertEquals(false, $r->match($req));
    }

    private function requestWithUri($uri): \Psr\Http\Message\ServerRequestInterface
    {
        $req = new \Zend\Diactoros\ServerRequest();
        $req = $req->withUri(new \Zend\Diactoros\Uri($uri));
        return $req;
    }
}