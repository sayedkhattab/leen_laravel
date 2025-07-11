<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerApprovalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // التحقق من أن المستخدم هو بائع وأن حالة طلبه معتمدة
        if ($user && $user->getTable() === 'sellers' && $user->request_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Account Pending Approval',
                'data' => [
                    'error' => 'Your account is pending admin approval. You can update your profile but cannot add services yet.'
                ]
            ], 403);
        }

        return $next($request);
    }
} 