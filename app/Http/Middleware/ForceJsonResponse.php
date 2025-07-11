<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        $response = $next($request);
        
        // If the response is not already a JSON response, convert it
        if (!$response instanceof \Illuminate\Http\JsonResponse) {
            // If it's an error response, convert it to a JSON error response
            if ($response->getStatusCode() >= 400) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error',
                    'errors' => ['error' => 'An error occurred while processing your request.']
                ], $response->getStatusCode());
            }
        }
        
        return $response;
    }
} 