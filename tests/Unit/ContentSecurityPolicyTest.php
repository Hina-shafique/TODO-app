<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Middleware\ContentSecurityPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ContentSecurityPolicyTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_header_is_set_local_environment(): void
    {
        $this->assertFalse(App::isProduction());

        $middleware = new ContentSecurityPolicy();

        $request = Request::create('/', 'GET');
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        $this->assertTrue($response->headers->has('Content-Security-Policy-Report-Only'));
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertStringContainsString("default-src 'self'", $response->headers->get('Content-Security-Policy-Report-Only'));
    }

    public function test_sets_enforced_header_in_production_environment()
    {
        // Force the environment to production
        $this->app->detectEnvironment(fn() => 'production');
        $this->assertTrue(App::isProduction());

        $middleware = new ContentSecurityPolicy();
        $request = Request::create('/', 'GET');

        $response = $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('Content-Security-Policy-Report-Only'));
        $this->assertStringContainsString("default-src 'self'", $response->headers->get('Content-Security-Policy'));
    }
}
