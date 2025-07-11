<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PromotionalContentController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\API\Customers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Development-only route to view recent OTP codes - REMOVE IN PRODUCTION
Route::get('/dev/recent-otps', function () {
    // تحقق من أن البيئة هي بيئة تطوير
    if (app()->environment() !== 'local') {
        abort(404);
    }
    
    $logPath = storage_path('logs/laravel.log');
    
    if (!file_exists($logPath)) {
        return response()->json(['error' => 'Log file not found'], 404);
    }
    
    // قراءة آخر 100 سطر من ملف السجل
    $logContent = shell_exec("powershell -command \"Get-Content -Path '{$logPath}' -Tail 100\"");
    
    // تحليل السجل للبحث عن رموز التحقق
    $otpData = [];
    $lines = explode("\n", $logContent);
    
    $currentOtp = null;
    
    foreach ($lines as $line) {
        if (strpos($line, '======= UNIFIED OTP CODE =======') !== false || 
            strpos($line, '======= OTP CODE =======') !== false) {
            $currentOtp = [];
        } elseif (strpos($line, 'Phone:') !== false && !empty($currentOtp)) {
            preg_match('/Phone: (.+)/', $line, $matches);
            if (isset($matches[1])) {
                $currentOtp['phone'] = $matches[1];
            }
        } elseif (strpos($line, 'OTP Code:') !== false && !empty($currentOtp)) {
            preg_match('/OTP Code: (.+)/', $line, $matches);
            if (isset($matches[1])) {
                $currentOtp['code'] = $matches[1];
            }
        } elseif (strpos($line, 'User Type:') !== false && !empty($currentOtp)) {
            preg_match('/User Type: (.*)/', $line, $matches);
            $currentOtp['user_type'] = isset($matches[1]) ? $matches[1] : '';
        } elseif (strpos($line, 'Action:') !== false && !empty($currentOtp)) {
            preg_match('/Action: (.*)/', $line, $matches);
            $currentOtp['action'] = isset($matches[1]) ? $matches[1] : '';
        } elseif (strpos($line, 'Type/Action:') !== false && !empty($currentOtp)) {
            preg_match('/Type\/Action: (.*)/', $line, $matches);
            $currentOtp['type'] = isset($matches[1]) ? $matches[1] : '';
        } elseif ((strpos($line, '==============================') !== false || 
                  strpos($line, '========================') !== false) && 
                  !empty($currentOtp)) {
            if (!empty($currentOtp['phone']) && !empty($currentOtp['code'])) {
                $otpData[] = $currentOtp;
            }
            $currentOtp = null;
        }
    }
    
    // عرض البيانات في صفحة HTML بسيطة
    return view('dev.otps', ['otps' => $otpData]);
});

// Payment callback routes - مسارات استجابة الدفع
Route::get('/payment/callback', [App\Http\Controllers\API\PaymentController::class, 'handlePaymobCallback'])->name('payment.callback');
Route::post('/payment/callback', [App\Http\Controllers\API\PaymentController::class, 'handlePaymobCallback'])->name('payment.callback.post');
Route::get('/payment/success', function() { return view('payment.success', ['message' => 'تم الدفع بنجاح']); })->name('payment.success');
Route::get('/payment/error', function() { return view('payment.error', ['message' => 'فشلت عملية الدفع']); })->name('payment.error');

// تعديل: إضافة مسارات لمعالجة الكولباك من iFrame
Route::get('/payment/process', [App\Http\Controllers\API\PaymentController::class, 'handlePaymobCallback'])->name('payment.process');
Route::post('/payment/process', [App\Http\Controllers\API\PaymentController::class, 'handlePaymobCallback'])->name('payment.process.post');

// مسار لإصلاح حالة المدفوعات (للمشرفين فقط)
Route::get('/admin/fix-payment/{id}', function($id) {
    // التحقق من أن المستخدم مشرف (في بيئة الإنتاج، يجب إضافة تحقق أكثر صرامة)
    if (app()->environment() !== 'production') {
        $controller = app()->make(App\Http\Controllers\API\PaymentController::class);
        return $controller->checkAndUpdatePaymentStatus($id);
    }
    abort(403);
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (accessible without authentication)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', function () {
            return view('admin.login');
        })->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.submit');
    });
    
    // Protected Routes (require admin authentication)
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Categories
        Route::resource('categories', CategoryController::class);
        
        // Subcategories
        Route::resource('subcategories', SubCategoryController::class);
        
        // Customers
        Route::resource('customers', CustomerController::class);
        
        // Sellers
        Route::resource('sellers', SellerController::class);
        Route::post('/sellers/{id}/approve', [SellerController::class, 'approve'])->name('sellers.approve');
        Route::post('/sellers/{id}/reject', [SellerController::class, 'reject'])->name('sellers.reject');
        
        // Services
        Route::get('/services', [AdminController::class, 'services'])->name('services.index');
        // Service Details
        Route::get('/services/{type}/{id}', [AdminController::class, 'serviceShow'])->name('services.show');
        
        // Bookings
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
        
        // Promotional Content
        Route::prefix('promotional')->name('promotional.')->group(function () {
            // Banners
            Route::get('/banners', [PromotionalContentController::class, 'bannerIndex'])->name('banners.index');
            Route::get('/banners/create', [PromotionalContentController::class, 'bannerCreate'])->name('banners.create');
            Route::post('/banners', [PromotionalContentController::class, 'bannerStore'])->name('banners.store');
            Route::get('/banners/{banner}/edit', [PromotionalContentController::class, 'bannerEdit'])->name('banners.edit');
            Route::put('/banners/{banner}', [PromotionalContentController::class, 'bannerUpdate'])->name('banners.update');
            Route::delete('/banners/{banner}', [PromotionalContentController::class, 'bannerDestroy'])->name('banners.destroy');
            
            // Featured Services
            Route::get('/featured-services', [PromotionalContentController::class, 'featuredServiceIndex'])->name('featured-services.index');
            Route::get('/featured-services/create', [PromotionalContentController::class, 'featuredServiceCreate'])->name('featured-services.create');
            Route::post('/featured-services', [PromotionalContentController::class, 'featuredServiceStore'])->name('featured-services.store');
            Route::delete('/featured-services/{featuredService}', [PromotionalContentController::class, 'featuredServiceDestroy'])->name('featured-services.destroy');
            
            // Featured Professionals
            Route::get('/featured-professionals', [PromotionalContentController::class, 'featuredProfessionalIndex'])->name('featured-professionals.index');
            Route::get('/featured-professionals/create', [PromotionalContentController::class, 'featuredProfessionalCreate'])->name('featured-professionals.create');
            Route::post('/featured-professionals', [PromotionalContentController::class, 'featuredProfessionalStore'])->name('featured-professionals.store');
            Route::delete('/featured-professionals/{featuredProfessional}', [PromotionalContentController::class, 'featuredProfessionalDestroy'])->name('featured-professionals.destroy');
            
            // Special Offers
            Route::get('/special-offers', [PromotionalContentController::class, 'specialOfferIndex'])->name('special-offers.index');
            Route::get('/special-offers/create', [PromotionalContentController::class, 'specialOfferCreate'])->name('special-offers.create');
            Route::post('/special-offers', [PromotionalContentController::class, 'specialOfferStore'])->name('special-offers.store');
            Route::get('/special-offers/{specialOffer}/edit', [PromotionalContentController::class, 'specialOfferEdit'])->name('special-offers.edit');
            Route::put('/special-offers/{specialOffer}', [PromotionalContentController::class, 'specialOfferUpdate'])->name('special-offers.update');
            Route::delete('/special-offers/{specialOffer}', [PromotionalContentController::class, 'specialOfferDestroy'])->name('special-offers.destroy');
        });
        
        // Admin API search routes for AJAX calls
        Route::prefix('api')->group(function () {
            Route::get('/search/sellers', [App\Http\Controllers\API\Admin\DashboardController::class, 'searchSellers'])->name('api.search.sellers');
            Route::get('/search/home-services', [App\Http\Controllers\API\Admin\DashboardController::class, 'searchHomeServices'])->name('api.search.home-services');
            Route::get('/search/studio-services', [App\Http\Controllers\API\Admin\DashboardController::class, 'searchStudioServices'])->name('api.search.studio-services');
        });
    });
});

// Fallback route to handle expired sessions and redirect to login
Route::fallback(function () {
    if (request()->is('admin/*') || request()->is('admin')) {
        return redirect()->route('admin.login')->with('error', 'انتهت جلستك. يرجى تسجيل الدخول مرة أخرى.');
    }
    
    return response()->view('welcome');
});
