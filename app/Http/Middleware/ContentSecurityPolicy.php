<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $policy = "default-src 'self'; " .
              "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " . 
              "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; ".
              "font-src 'self' https://fonts.bunny.net; " .
              "img-src 'self' data: https:; " .
              "connect-src 'self' ws: wss:;";

        // In Production: Enforce the policy
        if (app()->isProduction()) {
            $response->headers->set('Content-Security-Policy', $policy);
        }
        // In Local: Just report issues to the Console without blocking
        else {
            $response->headers->set('Content-Security-Policy-Report-Only', $policy);
        }

        return $response;

    }
}
