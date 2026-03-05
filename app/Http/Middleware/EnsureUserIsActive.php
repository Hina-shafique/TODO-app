<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivityCheck = Session::get('last_activity_check');

            if (!$lastActivityCheck || now()->diffInMinutes($lastActivityCheck) >= 5) {

                $user = Auth::user();

                if (!$user->is_active) {
                    Auth::logout();
                    Session::flush();
                    return redirect()->route('login')
                        ->withErrors(['form.email' => 'Your account is inactive. Please contact support.']);
                }
                Session::put('last_activity_check', now());
            }
        }

        return $next($request);
    }
}
