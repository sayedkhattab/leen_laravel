<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            // If it's an AJAX request, return a 401 response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthenticated', 'redirect' => route('admin.login')], 401);
            }
            
            // For regular requests, redirect to login with a message
            return redirect()->route('admin.login')
                ->with('error', 'انتهت جلستك. يرجى تسجيل الدخول مرة أخرى.');
        }

        // Extend the session lifetime on each request
        if (Auth::guard('admin')->check()) {
            $request->session()->regenerate();
        }

        return $next($request);
    }
} 