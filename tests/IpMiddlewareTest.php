<?php

namespace Orkhanahmadov\LaravelIpMiddleware\Tests;

use Illuminate\Http\Request;
use Orkhanahmadov\LaravelIpMiddleware\IpMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IpMiddlewareTest extends TestCase
{
    /**
     * @var IpMiddleware
     */
    private $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = $this->app->make(IpMiddleware::class);
    }

    public function testBlocksIfIpIsNotWhitelisted()
    {
        $this->expectException(HttpException::class);
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '1.1.1.0']);

        $this->middleware->handle($request, function () {
        }, '1.1.1.1,2.2.2.2');
    }

    public function testAllowsWithCloudflareIpAddress()
    {
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_CF_CONNECTING_IP' => '2.1.1.1']);

        $result = $this->middleware->handle($request, function () {
            return true;
        }, '2.1.1.1');

        $this->assertTrue($result);
    }

    public function testAllowsIfIpIsWhitelisted()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '1.1.1.1']);

        $result = $this->middleware->handle($request, function () {
            return true;
        }, '1.1.1.1');

        $this->assertTrue($result);
    }

    public function testAllowsIfEnvironmentIsIgnored()
    {
        app()['config']->set('ip-middleware.ignore_environments', ['testing']);
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '1.1.1.0']);

        $result = $this->middleware->handle($request, function () {
            return true;
        }, '1.1.1.1');

        $this->assertTrue($result);
    }
}