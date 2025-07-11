<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OTPController;
use App\Http\Controllers\API\Auth\CustomerAuthController;
use App\Http\Controllers\API\Auth\SellerAuthController;
use App\Http\Controllers\API\Auth\UnifiedAuthController;

/*
|--------------------------------------------------------------------------
| Direct API Routes (without v1 prefix)
|--------------------------------------------------------------------------
|
| These routes are designed to be accessed directly without the v1 prefix
| for compatibility with the Flutter app.
|
*/

// Direct OTP routes
Route::post('send-otp', [OTPController::class, 'sendOtp']);
Route::post('verify-otp', [OTPController::class, 'verifyOtp']);
Route::post('check-verification', [OTPController::class, 'checkVerificationStatus']);

// Direct Auth routes
Route::prefix('auth')->group(function () {
    // Unified auth routes
    Route::post('register', [UnifiedAuthController::class, 'register']);
    Route::post('login', [UnifiedAuthController::class, 'login']);
    Route::post('send-otp', [UnifiedAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [UnifiedAuthController::class, 'verifyOtp']);
    Route::post('reset-password', [UnifiedAuthController::class, 'resetPassword']);
    
    // Customer auth routes
    Route::prefix('customer')->group(function () {
        Route::post('register', [CustomerAuthController::class, 'register']);
        Route::post('login', [CustomerAuthController::class, 'login']);
        Route::post('send-otp', [CustomerAuthController::class, 'sendOtp']);
        Route::post('verify-otp', [CustomerAuthController::class, 'verifyOtp']);
        Route::post('reset-password', [CustomerAuthController::class, 'resetPassword']);
        
        // Protected customer routes
        Route::middleware('auth:sanctum')->group(function() {
            Route::get('profile', [\App\Http\Controllers\API\Customer\ProfileController::class, 'show']);
        });
    });
    
    // Seller auth routes
    Route::prefix('seller')->group(function () {
        Route::post('register', [SellerAuthController::class, 'register']);
        Route::post('login', [SellerAuthController::class, 'login']);
        Route::post('send-otp', [SellerAuthController::class, 'sendOtp']);
        Route::post('verify-otp', [SellerAuthController::class, 'verifyOtp']);
        Route::post('reset-password', [SellerAuthController::class, 'resetPassword']);
        
        // Protected seller routes - use auth:sanctum only, not seller middleware
        Route::middleware('auth:sanctum')->group(function() {
            Route::get('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'show']);
            Route::put('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'update']);
            Route::post('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'update']);
        });
    });
});

// Direct routes for updating services - bypass middleware issues
Route::middleware('auth:sanctum')->group(function() {
    // Direct routes using the dedicated controller
    Route::put('/direct-update-home-service/{id}', [\App\Http\Controllers\API\Seller\DirectServiceController::class, 'updateHomeService']);
    Route::post('/direct-update-home-service/{id}', [\App\Http\Controllers\API\Seller\DirectServiceController::class, 'updateHomeService']);
    
    Route::put('/direct-update-studio-service/{id}', [\App\Http\Controllers\API\Seller\DirectServiceController::class, 'updateStudioService']);
    Route::post('/direct-update-studio-service/{id}', [\App\Http\Controllers\API\Seller\DirectServiceController::class, 'updateStudioService']);
    
    // Fallback routes using closure approach
    Route::put('/update-home-service/{id}', function($id, \Illuminate\Http\Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\DirectServiceController::class);
        return $controller->updateHomeService($request, $id);
    });
    
    Route::post('/update-home-service/{id}', function($id, \Illuminate\Http\Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\DirectServiceController::class);
        return $controller->updateHomeService($request, $id);
    });
    
    Route::put('/update-studio-service/{id}', function($id, \Illuminate\Http\Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\DirectServiceController::class);
        return $controller->updateStudioService($request, $id);
    });
    
    Route::post('/update-studio-service/{id}', function($id, \Illuminate\Http\Request $request) {
        $controller = app()->make(\App\Http\Controllers\API\Seller\DirectServiceController::class);
        return $controller->updateStudioService($request, $id);
    });
}); 