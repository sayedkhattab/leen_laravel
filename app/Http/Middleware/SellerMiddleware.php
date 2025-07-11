<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user() instanceof \App\Models\Seller) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Seller access required.',
            ], 401);
        }

        // Check if seller is active
        if ($request->user()->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Your account is not active. Please contact support.',
            ], 403);
        }

        return $next($request);
    }
} 