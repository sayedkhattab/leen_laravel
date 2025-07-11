<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Unified API Routes
|--------------------------------------------------------------------------
|
| These routes are designed for a single app that serves both customers
| and sellers. Authentication will determine the user type and permissions.
|
*/

// Direct routes without v1 prefix (for Flutter app compatibility)
Route::post('/send-otp', [\App\Http\Controllers\API\OTPController::class, 'sendOtp']);
Route::post('/verify-otp', [\App\Http\Controllers\API\OTPController::class, 'verifyOtp']);
Route::post('/check-verification', [\App\Http\Controllers\API\OTPController::class, 'checkVerificationStatus']);

// Debug endpoint
Route::get('/debug-api', function() {
    return response()->json([
        'status' => true,
        'message' => 'API is working',
        'data' => [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment()
        ]
    ]);
});

// Test POST endpoint
Route::post('/test-post', function(Request $request) {
    return response()->json([
        'status' => true,
        'message' => 'POST request received successfully',
        'data' => [
            'received_data' => $request->all(),
            'headers' => $request->headers->all(),
            'timestamp' => now()->toIso8601String()
        ]
    ]);
});

// Direct Auth routes without v1 prefix
Route::prefix('auth')->group(function () {
    Route::post('register', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'login']);
    Route::post('send-otp', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'verifyOtp']);
    Route::post('reset-password', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'resetPassword']);
    
    // Customer auth routes
    Route::prefix('customer')->group(function () {
        Route::post('register', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'register']);
        Route::post('login', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'login']);
        Route::post('send-otp', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'sendOtp']);
        Route::post('verify-otp', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'verifyOtp']);
        Route::post('reset-password', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'resetPassword']);
        
        // Protected customer routes
        Route::middleware('auth:sanctum')->group(function() {
            Route::get('profile', [\App\Http\Controllers\API\Customer\ProfileController::class, 'show']);
        });
    });
    
    // Seller auth routes
    Route::prefix('seller')->group(function () {
        Route::post('register', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'register']);
        Route::post('login', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'login']);
        Route::post('send-otp', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'sendOtp']);
        Route::post('verify-otp', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'verifyOtp']);
        Route::post('reset-password', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'resetPassword']);
        
        // Protected seller routes - use auth:sanctum only, not seller middleware
        Route::middleware('auth:sanctum')->group(function() {
            Route::get('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'show']);
            Route::put('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'update']);
            Route::post('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'update']);
            Route::get('check-approval', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'checkApprovalStatus']);
        });
    });
});

// Add direct route for checking seller approval status
Route::middleware('auth:sanctum')->get('/check-provider-approval', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'checkApprovalStatus']);

// Direct routes for seller services
Route::middleware('auth:sanctum')->group(function() {
    // Home services
    Route::post('/seller/home-services', [\App\Http\Controllers\API\Seller\HomeServiceController::class, 'store']);
    Route::get('/seller/home-services', [\App\Http\Controllers\API\Seller\HomeServiceController::class, 'index']);
    Route::get('/seller/home-services/{homeService}', [\App\Http\Controllers\API\Seller\HomeServiceController::class, 'show']);
    Route::put('/seller/home-services/{homeService}', [\App\Http\Controllers\API\Seller\HomeServiceController::class, 'update']);
    Route::delete('/seller/home-services/{homeService}', [\App\Http\Controllers\API\Seller\HomeServiceController::class, 'destroy']);
    
    // Studio services
    Route::post('/seller/studio-services', [\App\Http\Controllers\API\Seller\StudioServiceController::class, 'store']);
    Route::get('/seller/studio-services', [\App\Http\Controllers\API\Seller\StudioServiceController::class, 'index']);
    Route::get('/seller/studio-services/{studioService}', [\App\Http\Controllers\API\Seller\StudioServiceController::class, 'show']);
    Route::put('/seller/studio-services/{studioService}', [\App\Http\Controllers\API\Seller\StudioServiceController::class, 'update']);
    Route::delete('/seller/studio-services/{studioService}', [\App\Http\Controllers\API\Seller\StudioServiceController::class, 'destroy']);
});

// Special direct routes for updating services (without seller middleware)
Route::middleware('auth:sanctum')->group(function() {
    // Update home service
    Route::put('/update-home-service/{id}', function($id, Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\HomeServiceController::class);
        return $controller->update($request, $id);
    });
    
    // Update studio service
    Route::put('/update-studio-service/{id}', function($id, Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\StudioServiceController::class);
        return $controller->update($request, $id);
    });
    
    // Alternative routes with POST method (in case PUT is causing issues)
    Route::post('/update-home-service/{id}', function($id, Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\HomeServiceController::class);
        return $controller->update($request, $id);
    });
    
    Route::post('/update-studio-service/{id}', function($id, Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\StudioServiceController::class);
        return $controller->update($request, $id);
    });
});

// Public routes
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'register']);
        Route::post('login', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'login']);
        Route::post('send-otp', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'sendOtp']);
        Route::post('verify-otp', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'verifyOtp']);
        Route::post('reset-password', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'resetPassword']);
    });

    // Public category routes
    Route::get('categories', [\App\Http\Controllers\API\CategoryController::class, 'index']);
    Route::get('categories/{category}', [\App\Http\Controllers\API\CategoryController::class, 'show']);
    Route::get('categories/{category}/subcategories', [\App\Http\Controllers\API\CategoryController::class, 'subcategories']);
    
    // Public subcategory routes
    Route::get('subcategories', [\App\Http\Controllers\API\SubCategoryController::class, 'index']);
    Route::get('subcategories/{subcategory}', [\App\Http\Controllers\API\SubCategoryController::class, 'show']);
    
    // Public service routes
    Route::get('home-services', [\App\Http\Controllers\API\HomeServiceController::class, 'index']);
    Route::get('home-services/{homeService}', [\App\Http\Controllers\API\HomeServiceController::class, 'show']);
    Route::get('studio-services', [\App\Http\Controllers\API\StudioServiceController::class, 'index']);
    Route::get('studio-services/{studioService}', [\App\Http\Controllers\API\StudioServiceController::class, 'show']);
    
    // Public location routes
    Route::get('nearby-users', [\App\Http\Controllers\API\LocationController::class, 'getNearbyUsers']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Common routes for all authenticated users
    Route::post('logout', [\App\Http\Controllers\API\Auth\UnifiedAuthController::class, 'logout']);
    Route::get('profile', [\App\Http\Controllers\API\UnifiedProfileController::class, 'show']);
    Route::put('profile', [\App\Http\Controllers\API\UnifiedProfileController::class, 'update']);
    
    // Location routes
    Route::post('update-location', [\App\Http\Controllers\API\LocationController::class, 'updateLocation']);
    
    // Chat routes (common functionality)
    Route::get('chat-rooms', [\App\Http\Controllers\API\UnifiedChatController::class, 'index']);
    Route::get('chat-rooms/{chatRoom}', [\App\Http\Controllers\API\UnifiedChatController::class, 'show']);
    Route::post('chat-rooms/{chatRoom}/messages', [\App\Http\Controllers\API\UnifiedChatController::class, 'sendMessage']);
    
    // Notifications (common functionality)
    Route::get('notifications', [\App\Http\Controllers\API\UnifiedNotificationController::class, 'index']);
    Route::put('notifications/{notification}/read', [\App\Http\Controllers\API\UnifiedNotificationController::class, 'markAsRead']);
    
    // Seller specific routes
    Route::prefix('seller')->group(function () {
        // Profile routes
        Route::get('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'show']);
        Route::put('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'update']);
        Route::post('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'update']);
        Route::get('check-approval', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'checkApprovalStatus']);
        
        // Employee management
        Route::apiResource('employees', \App\Http\Controllers\API\Seller\EmployeeController::class);
        
        // Service management
        Route::apiResource('home-services', \App\Http\Controllers\API\Seller\HomeServiceController::class);
        Route::apiResource('studio-services', \App\Http\Controllers\API\Seller\StudioServiceController::class);
        
        // Booking management for sellers
        Route::apiResource('home-service-bookings', \App\Http\Controllers\API\Seller\HomeServiceBookingController::class);
        Route::apiResource('studio-service-bookings', \App\Http\Controllers\API\Seller\StudioServiceBookingController::class);
        Route::put('home-service-bookings/{booking}/status', [\App\Http\Controllers\API\Seller\HomeServiceBookingController::class, 'updateStatus']);
        Route::put('studio-service-bookings/{booking}/status', [\App\Http\Controllers\API\Seller\StudioServiceBookingController::class, 'updateStatus']);
        
        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\API\Seller\DashboardController::class, 'index']);
    });
    
    // Customer specific routes
    Route::middleware(\App\Http\Middleware\CustomerMiddleware::class)->group(function () {
        // Booking management for customers
        Route::apiResource('customer/home-service-bookings', \App\Http\Controllers\API\Customer\HomeServiceBookingController::class);
        Route::apiResource('customer/studio-service-bookings', \App\Http\Controllers\API\Customer\StudioServiceBookingController::class);
        Route::put('customer/home-service-bookings/{booking}/cancel', [\App\Http\Controllers\API\Customer\HomeServiceBookingController::class, 'cancel']);
        Route::put('customer/studio-service-bookings/{booking}/cancel', [\App\Http\Controllers\API\Customer\StudioServiceBookingController::class, 'cancel']);
        
        // Payments
        Route::post('customer/payments', [\App\Http\Controllers\API\PaymentController::class, 'processPayment']);
        Route::post('customer/payments/home-service', [\App\Http\Controllers\API\PaymentController::class, 'createHomeServicePayment']);
        Route::post('customer/payments/studio-service', [\App\Http\Controllers\API\PaymentController::class, 'createStudioServicePayment']);
        Route::get('customer/payments/{id}', [\App\Http\Controllers\API\PaymentController::class, 'getPaymentStatus']);
        Route::post('customer/payments/{id}/check', [\App\Http\Controllers\API\PaymentController::class, 'checkAndUpdatePaymentStatus']);
        
        // Favorites
        Route::post('favorites/home-services/{homeService}', [\App\Http\Controllers\API\Customer\FavoriteController::class, 'addHomeService']);
        Route::post('favorites/studio-services/{studioService}', [\App\Http\Controllers\API\Customer\FavoriteController::class, 'addStudioService']);
        Route::delete('favorites/home-services/{homeService}', [\App\Http\Controllers\API\Customer\FavoriteController::class, 'removeHomeService']);
        Route::delete('favorites/studio-services/{studioService}', [\App\Http\Controllers\API\Customer\FavoriteController::class, 'removeStudioService']);
        Route::get('favorites', [\App\Http\Controllers\API\Customer\FavoriteController::class, 'index']);
        
        // Ratings
        Route::post('ratings/sellers/{seller}', [\App\Http\Controllers\API\Customer\RatingController::class, 'rateSeller']);
        Route::post('ratings/home-services/{homeService}', [\App\Http\Controllers\API\Customer\RatingController::class, 'rateHomeService']);
        Route::post('ratings/studio-services/{studioService}', [\App\Http\Controllers\API\Customer\RatingController::class, 'rateStudioService']);
        
        // Chat with sellers
        Route::post('chat-rooms/sellers/{seller}', [\App\Http\Controllers\API\Customer\ChatRoomController::class, 'createWithSeller']);
        
        // Points
        Route::get('points', [\App\Http\Controllers\API\Customer\PointController::class, 'index']);
    });
    
    // Admin specific routes
    Route::middleware('admin')->group(function () {
        // Category management
        Route::apiResource('admin/categories', \App\Http\Controllers\API\Admin\CategoryController::class);
        
        // Subcategory management
        Route::apiResource('admin/subcategories', \App\Http\Controllers\API\Admin\SubCategoryController::class);
        
        // User management
        Route::apiResource('admin/sellers', \App\Http\Controllers\API\Admin\SellerController::class);
        Route::apiResource('admin/customers', \App\Http\Controllers\API\Admin\CustomerController::class);
        
        // Dashboard
        Route::get('admin/dashboard', [\App\Http\Controllers\API\Admin\DashboardController::class, 'index']);
    });
}); 