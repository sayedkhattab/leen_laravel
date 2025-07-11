<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OTPController;
use App\Http\Controllers\API\Customers\PaymentController;
use App\Http\Controllers\API\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\API\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\API\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\API\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\API\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\API\Admin\SubCategoryController as AdminSubCategoryController;
use App\Http\Controllers\API\Auth\AdminAuthController;
use App\Http\Controllers\API\Auth\CustomerAuthController;
use App\Http\Controllers\API\Auth\SellerAuthController;
use App\Http\Controllers\API\Auth\UnifiedAuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\HomeServiceController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\API\StudioServiceController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\UnifiedChatController;
use App\Http\Controllers\API\UnifiedNotificationController;
use App\Http\Controllers\API\UnifiedProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Direct routes for services (without v1 prefix) - Added to solve 404 error
Route::get('/home-services', [App\Http\Controllers\API\HomeServiceController::class, 'index']);
Route::get('/home-services/{homeService}', [App\Http\Controllers\API\HomeServiceController::class, 'show']);
Route::get('/studio-services', [App\Http\Controllers\API\StudioServiceController::class, 'index']);
Route::get('/studio-services/{studioService}', [App\Http\Controllers\API\StudioServiceController::class, 'show']);

// Direct OTP routes (without v1 prefix) - Added to solve 404 error
Route::post('/send-otp', [OTPController::class, 'sendOtp']);
Route::post('/verify-otp', [OTPController::class, 'verifyOtp']);
Route::post('/check-verification', [OTPController::class, 'checkVerificationStatus']);

// Test route to check OTP logging
Route::get('/test-otp/{phone}', function($phone) {
    \Illuminate\Support\Facades\Log::info("Testing OTP logging for phone: {$phone}");
    return response()->json(['message' => 'Test OTP log created. Check Laravel.log file.']);
});

// Test route for phone login
Route::get('/test-phone-login/{phone}/{password}/{user_type}', function($phone, $password, $user_type) {
    $loginField = 'phone';
    $loginValue = $phone;
    
    if (\Illuminate\Support\Facades\Auth::guard($user_type)->attempt([$loginField => $loginValue, 'password' => $password])) {
        $user = \Illuminate\Support\Facades\Auth::guard($user_type)->user();
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'فشل تسجيل الدخول',
            'error' => 'بيانات الاعتماد غير صالحة'
        ], 401);
    }
});

// Direct Auth routes (without v1 prefix) - Added to solve 404 error
Route::prefix('auth')->group(function () {
    // Unified auth routes
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
    });
    
    // Seller auth routes
    Route::prefix('seller')->group(function () {
        Route::post('register', [SellerAuthController::class, 'register']);
        Route::post('login', [SellerAuthController::class, 'login']);
        Route::post('send-otp', [SellerAuthController::class, 'sendOtp']);
        Route::post('verify-otp', [SellerAuthController::class, 'verifyOtp']);
        Route::post('reset-password', [SellerAuthController::class, 'resetPassword']);
    });
    
    // Admin auth routes
    Route::prefix('admin')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
    });
});

// Public routes
Route::prefix('v1')->group(function () {
    // OTP routes
    Route::controller(OTPController::class)->group(function () {
        Route::post('/send-otp', 'sendOtp');
        Route::post('/verify-otp', 'verifyOtp');
        Route::post('/check-verification', 'checkVerificationStatus');
    });

    // Auth routes
    Route::prefix('auth')->group(function () {
        // Admin auth routes
        Route::prefix('admin')->group(function () {
            Route::post('login', [\App\Http\Controllers\API\Auth\AdminAuthController::class, 'login']);
        });

        // Seller auth routes
        Route::prefix('seller')->group(function () {
            Route::post('register', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'register']);
            Route::post('login', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'login']);
            Route::post('send-otp', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'sendOtp']);
            Route::post('verify-otp', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'verifyOtp']);
            Route::post('reset-password', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'resetPassword']);
        });

        // Customer auth routes
        Route::prefix('customer')->group(function () {
            Route::post('register', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'register']);
            Route::post('login', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'login']);
            Route::post('send-otp', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'sendOtp']);
            Route::post('verify-otp', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'verifyOtp']);
            Route::post('reset-password', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'resetPassword']);
        });
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
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Admin routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::post('logout', [\App\Http\Controllers\API\Auth\AdminAuthController::class, 'logout']);
        Route::get('profile', [\App\Http\Controllers\API\Admin\ProfileController::class, 'show']);
        Route::put('profile', [\App\Http\Controllers\API\Admin\ProfileController::class, 'update']);
        
        // Category management
        Route::apiResource('categories', \App\Http\Controllers\API\Admin\CategoryController::class);
        
        // Subcategory management
        Route::apiResource('subcategories', \App\Http\Controllers\API\Admin\SubCategoryController::class);
        
        // User management
        Route::apiResource('sellers', \App\Http\Controllers\API\Admin\SellerController::class);
        Route::apiResource('customers', \App\Http\Controllers\API\Admin\CustomerController::class);
        
        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\API\Admin\DashboardController::class, 'index']);

        // Admin search routes
        Route::get('search/sellers', [\App\Http\Controllers\API\Admin\DashboardController::class, 'searchSellers']);
        Route::get('search/home-services', [\App\Http\Controllers\API\Admin\DashboardController::class, 'searchHomeServices']);
        Route::get('search/studio-services', [\App\Http\Controllers\API\Admin\DashboardController::class, 'searchStudioServices']);
    });
    
    // Seller routes
    Route::prefix('seller')->middleware('seller')->group(function () {
        Route::post('logout', [\App\Http\Controllers\API\Auth\SellerAuthController::class, 'logout']);
        Route::get('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'show']);
        Route::put('profile', [\App\Http\Controllers\API\Seller\ProfileController::class, 'update']);
        
        // Employee management
        Route::apiResource('employees', \App\Http\Controllers\API\Seller\EmployeeController::class);
        Route::put('employees/{id}/availability', [\App\Http\Controllers\API\Seller\EmployeeController::class, 'updateAvailability']);
        Route::get('available-employees', [\App\Http\Controllers\API\Seller\EmployeeController::class, 'getAvailableEmployees']);
        
        // Service management (تتطلب الموافقة)
        Route::middleware('seller.approved')->group(function () {
            Route::apiResource('home-services', \App\Http\Controllers\API\Seller\HomeServiceController::class);
            Route::apiResource('studio-services', \App\Http\Controllers\API\Seller\StudioServiceController::class);
        });
        
        // Booking management (تتطلب الموافقة)
        Route::middleware('seller.approved')->group(function () {
            Route::apiResource('home-service-bookings', \App\Http\Controllers\API\Seller\HomeServiceBookingController::class);
            Route::apiResource('studio-service-bookings', \App\Http\Controllers\API\Seller\StudioServiceBookingController::class);
            Route::put('home-service-bookings/{booking}/status', [\App\Http\Controllers\API\Seller\HomeServiceBookingController::class, 'updateStatus']);
            Route::put('studio-service-bookings/{booking}/status', [\App\Http\Controllers\API\Seller\StudioServiceBookingController::class, 'updateStatus']);
        });
        
        // Chat
        Route::get('chat-rooms', [\App\Http\Controllers\API\Seller\ChatRoomController::class, 'index']);
        Route::get('chat-rooms/{chatRoom}', [\App\Http\Controllers\API\Seller\ChatRoomController::class, 'show']);
        Route::post('chat-rooms/{chatRoom}/messages', [\App\Http\Controllers\API\Seller\ChatRoomController::class, 'sendMessage']);
        
        // Notifications
        Route::get('notifications', [\App\Http\Controllers\API\Seller\NotificationController::class, 'index']);
        Route::put('notifications/{notification}/read', [\App\Http\Controllers\API\Seller\NotificationController::class, 'markAsRead']);
        
        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\API\Seller\DashboardController::class, 'index']);
    });
    
    // Customer routes
    Route::prefix('customer')->middleware(\App\Http\Middleware\CustomerMiddleware::class)->group(function () {
        Route::post('logout', [\App\Http\Controllers\API\Auth\CustomerAuthController::class, 'logout']);
        Route::get('profile', [\App\Http\Controllers\API\Customer\ProfileController::class, 'show']);
        Route::put('profile', [\App\Http\Controllers\API\Customer\ProfileController::class, 'update']);
        Route::put('change-password', [\App\Http\Controllers\API\Customer\ProfileController::class, 'changePassword']);
        Route::delete('account', [\App\Http\Controllers\API\Customer\ProfileController::class, 'deleteAccount']);
        
        // Payment routes
        Route::post('payments/create', [PaymentController::class, 'makePayment']);
        Route::get('payments/{id}', [PaymentController::class, 'getPaymentById']);
        
        // Booking management
        Route::apiResource('home-service-bookings', \App\Http\Controllers\API\Customer\HomeServiceBookingController::class);
        Route::apiResource('studio-service-bookings', \App\Http\Controllers\API\Customer\StudioServiceBookingController::class);
        Route::put('home-service-bookings/{booking}/cancel', [\App\Http\Controllers\API\Customer\HomeServiceBookingController::class, 'cancel']);
        Route::put('studio-service-bookings/{booking}/cancel', [\App\Http\Controllers\API\Customer\StudioServiceBookingController::class, 'cancel']);
        
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
        
        // Chat
        Route::get('chat-rooms', [\App\Http\Controllers\API\Customer\ChatRoomController::class, 'index']);
        Route::get('chat-rooms/{chatRoom}', [\App\Http\Controllers\API\Customer\ChatRoomController::class, 'show']);
        Route::post('chat-rooms/{chatRoom}/messages', [\App\Http\Controllers\API\Customer\ChatRoomController::class, 'sendMessage']);
        Route::post('chat-rooms/sellers/{seller}', [\App\Http\Controllers\API\Customer\ChatRoomController::class, 'createWithSeller']);
        
        // Notifications
        Route::get('notifications', [\App\Http\Controllers\API\Customer\NotificationController::class, 'index']);
        Route::put('notifications/{notification}/read', [\App\Http\Controllers\API\Customer\NotificationController::class, 'markAsRead']);
        
        // Points
        Route::get('points', [\App\Http\Controllers\API\Customer\PointController::class, 'index']);
    });
});

// Unified Auth Routes
Route::post('/login', [UnifiedAuthController::class, 'login']);
Route::post('/register', [UnifiedAuthController::class, 'register']);
Route::post('/logout', [UnifiedAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [UnifiedAuthController::class, 'user'])->middleware('auth:sanctum');

// Home Screen Content
Route::get('/home', [HomeController::class, 'index']);

// Location Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-location', [LocationController::class, 'updateLocation']);
});

// Customer Routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\CustomerMiddleware::class])->prefix('customer')->group(function () {
    Route::get('/profile', [CustomerProfileController::class, 'show']);
    Route::post('/profile', [CustomerProfileController::class, 'update']);
    
    // Payment Routes
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
    Route::post('/payment/callback', [PaymentController::class, 'handleCallback']);
});

// Seller Routes
Route::middleware(['auth:sanctum', 'seller'])->prefix('seller')->group(function () {
    Route::get('/profile', [SellerProfileController::class, 'show']);
    Route::post('/profile', [SellerProfileController::class, 'update']);
    
    // Home Services
    Route::apiResource('/home-services', HomeServiceController::class);
    
    // Studio Services
    Route::apiResource('/studio-services', StudioServiceController::class);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/profile', [AdminProfileController::class, 'show']);
    Route::post('/profile', [AdminProfileController::class, 'update']);
    
    // Category Management
    Route::apiResource('/categories', AdminCategoryController::class);
    
    // Subcategory Management
    Route::apiResource('/subcategories', AdminSubCategoryController::class);
    
    // Customer Management
    Route::apiResource('/customers', AdminCustomerController::class);
    
    // Seller Management
    Route::apiResource('/sellers', AdminSellerController::class);
});

// Unified Profile Routes (for all user types)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile/update', [UnifiedProfileController::class, 'update']);
    
    // Chat Routes
    Route::get('/chats', [UnifiedChatController::class, 'index']);
    Route::get('/chats/{chatRoom}', [UnifiedChatController::class, 'show']);
    Route::post('/chats/{chatRoom}/messages', [UnifiedChatController::class, 'sendMessage']);
    
    // Notification Routes
    Route::get('/notifications', [UnifiedNotificationController::class, 'index']);
    Route::post('/notifications/read/{notification}', [UnifiedNotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [UnifiedNotificationController::class, 'markAllAsRead']);
});

// Payment Routes
Route::post('/payment/callback', [\App\Http\Controllers\API\PaymentController::class, 'handlePaymobCallback'])
    ->name('payment.callback.api')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/payment/callback', [\App\Http\Controllers\API\PaymentController::class, 'handlePaymobCallback'])
    ->name('payment.callback.api.get')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Customer Payment Routes
Route::middleware(['auth:sanctum', 'customer'])->prefix('customer')->group(function () {
    // Home Service Payments
    Route::post('/payments/home-service', [\App\Http\Controllers\API\Customers\PaymentController::class, 'createHomeServicePayment']);
    
    // Studio Service Payments
    Route::post('/payments/studio-service', [\App\Http\Controllers\API\Customers\PaymentController::class, 'createStudioServicePayment']);
    
    // Get Payment Status
    Route::get('/payments/{id}', [\App\Http\Controllers\API\Customers\PaymentController::class, 'getPaymentStatus']);
});

// Seller endpoints for customers
Route::get('/sellers/latest', [\App\Http\Controllers\API\SellerController::class, 'latest']);
Route::get('/sellers/{seller}', [\App\Http\Controllers\API\SellerController::class, 'show']);
Route::get('/sellers/{seller}/home-services', [\App\Http\Controllers\API\SellerController::class, 'homeServices']);
Route::get('/sellers/{seller}/studio-services', [\App\Http\Controllers\API\SellerController::class, 'studioServices']);
Route::get('/sellers/{seller}/statistics', [\App\Http\Controllers\API\SellerController::class, 'statistics']);
Route::get('/sellers/{seller}/employees', [\App\Http\Controllers\API\SellerController::class, 'employees']); 