<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\Seller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Check if user has the required role
        if ($role === 'customer' && $user instanceof Customer) {
            return $next($request);
        }
        
        if ($role === 'seller' && $user instanceof Seller) {
            return $next($request);
        }
        
        if ($role === 'admin' && get_class($user) === 'App\Models\Admin') {
            return $next($request);
        }
        
        return response()->json(['message' => 'Unauthorized. You do not have the required role.'], 403);
    }
} 