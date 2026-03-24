<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            $lastActivityCheck = $request->session()->get('last_activity_check');
            $nowTimestamp = now()->timestamp;
            
            if (!$lastActivityCheck || ($nowTimestamp - $lastActivityCheck) >= 300) { 
                $user = Auth::user();

                if (!$user->is_active) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['email' => 'Your account is inactive. Please contact support.']);
                }
                
                $request->session()->put('last_activity_check', $nowTimestamp);
            }
        }

        return $next($request);
    }
}
