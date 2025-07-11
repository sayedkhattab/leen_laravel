<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePhoneIsVerified
{
    /**
     * التعامل مع الطلب الوارد
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->hasVerifiedPhone()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your phone number is not verified.',
                'verification_required' => true
            ], 403);
        }

        return $next($request);
    }
} 