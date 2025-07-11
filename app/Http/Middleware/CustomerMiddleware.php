<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Debug logging
        Log::info('CustomerMiddleware: Processing request', [
            'user_id' => $user ? $user->id : null,
            'user_type' => $user ? get_class($user) : null,
            'has_customer' => $user && $user->customer ? true : false,
            'token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null,
            'route' => $request->path(),
        ]);
        
        if (!$user) {
            Log::warning('CustomerMiddleware: No authenticated user found');
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Authentication required.',
            ], 401);
        }
        
        // Check if user is a Customer model directly
        if ($user instanceof Customer) {
            // Debug customer data
            Log::info('CustomerMiddleware: User is directly a Customer', [
                'customer_id' => $user->id,
                'customer_status' => $user->status,
            ]);
            
            // Check if customer is active
            if ($user->status !== 'active') {
                Log::warning('CustomerMiddleware: Customer account not active', [
                    'customer_id' => $user->id,
                    'status' => $user->status,
                ]);
                
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active. Please contact support.',
                ], 403);
            }
            
            return $next($request);
        }
        
        // Check if user has a customer relationship
        if ($user instanceof User && $user->customer) {
            // Debug customer data
            Log::info('CustomerMiddleware: Customer found via relationship', [
                'customer_id' => $user->customer->id,
                'customer_status' => $user->customer->status,
            ]);
            
            // Check if customer is active
            if ($user->customer->status !== 'active') {
                Log::warning('CustomerMiddleware: Customer account not active', [
                    'customer_id' => $user->customer->id,
                    'status' => $user->customer->status,
                ]);
                
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active. Please contact support.',
                ], 403);
            }
            
            return $next($request);
        }
        
        Log::warning('CustomerMiddleware: User is not a customer', [
            'user_id' => $user->id,
            'user_type' => get_class($user),
        ]);
        
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized. Customer access required.',
        ], 403);
    }
} 