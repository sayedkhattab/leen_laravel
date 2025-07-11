<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\HomeServiceBooking;
use App\Models\StudioServiceBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * عرض بيانات لوحة التحكم للبائع
     */
    public function index()
    {
        $seller = Auth::user()->seller;
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // إحصائيات الخدمات
        $totalHomeServices = $seller->homeServices()->count();
        $totalStudioServices = $seller->studioServices()->count();
        
        // إحصائيات الحجوزات
        $homeServiceBookings = HomeServiceBooking::whereHas('homeService', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        });
        
        $studioServiceBookings = StudioServiceBooking::whereHas('studioService', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        });
        
        $totalBookings = $homeServiceBookings->count() + $studioServiceBookings->count();
        
        // حجوزات اليوم
        $todayHomeBookings = (clone $homeServiceBookings)->whereDate('booking_date', Carbon::today())->count();
        $todayStudioBookings = (clone $studioServiceBookings)->whereDate('booking_date', Carbon::today())->count();
        $todayBookings = $todayHomeBookings + $todayStudioBookings;
        
        // حجوزات هذا الأسبوع
        $weekHomeBookings = (clone $homeServiceBookings)->whereBetween('booking_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $weekStudioBookings = (clone $studioServiceBookings)->whereBetween('booking_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $weekBookings = $weekHomeBookings + $weekStudioBookings;
        
        // حجوزات حسب الحالة
        $bookingsByStatus = [
            'pending' => (clone $homeServiceBookings)->where('status', 'pending')->count() + (clone $studioServiceBookings)->where('status', 'pending')->count(),
            'confirmed' => (clone $homeServiceBookings)->where('status', 'confirmed')->count() + (clone $studioServiceBookings)->where('status', 'confirmed')->count(),
            'completed' => (clone $homeServiceBookings)->where('status', 'completed')->count() + (clone $studioServiceBookings)->where('status', 'completed')->count(),
            'cancelled' => (clone $homeServiceBookings)->where('status', 'cancelled')->count() + (clone $studioServiceBookings)->where('status', 'cancelled')->count(),
            'rescheduled' => (clone $homeServiceBookings)->where('status', 'rescheduled')->count() + (clone $studioServiceBookings)->where('status', 'rescheduled')->count(),
        ];
        
        // إجمالي الإيرادات
        $totalRevenue = 0;
        $completedHomeBookings = (clone $homeServiceBookings)->where('status', 'completed')->get();
        foreach ($completedHomeBookings as $booking) {
            $totalRevenue += $booking->homeService->discounted_price ?? $booking->homeService->price;
        }
        
        $completedStudioBookings = (clone $studioServiceBookings)->where('status', 'completed')->get();
        foreach ($completedStudioBookings as $booking) {
            $totalRevenue += $booking->studioService->discounted_price ?? $booking->studioService->price;
        }
        
        // الحجوزات القادمة
        $upcomingHomeBookings = (clone $homeServiceBookings)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('booking_date', '>=', Carbon::today())
            ->orderBy('booking_date', 'asc')
            ->with(['homeService', 'customer'])
            ->take(5)
            ->get();
            
        $upcomingStudioBookings = (clone $studioServiceBookings)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('booking_date', '>=', Carbon::today())
            ->orderBy('booking_date', 'asc')
            ->with(['studioService', 'customer'])
            ->take(5)
            ->get();
            
        $upcomingBookings = $upcomingHomeBookings->concat($upcomingStudioBookings)
            ->sortBy('booking_date')
            ->take(5)
            ->values();
            
        // الإيرادات الشهرية (آخر 6 أشهر)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->translatedFormat('F'); // اسم الشهر بالعربية
            
            $homeRevenue = DB::table('home_service_bookings')
                ->join('home_services', 'home_service_bookings.home_service_id', '=', 'home_services.id')
                ->where('home_services.seller_id', $seller->id)
                ->where('home_service_bookings.status', 'completed')
                ->whereYear('home_service_bookings.booking_date', $month->year)
                ->whereMonth('home_service_bookings.booking_date', $month->month)
                ->sum(DB::raw('COALESCE(home_services.discounted_price, home_services.price)'));
                
            $studioRevenue = DB::table('studio_service_bookings')
                ->join('studio_services', 'studio_service_bookings.studio_service_id', '=', 'studio_services.id')
                ->where('studio_services.seller_id', $seller->id)
                ->where('studio_service_bookings.status', 'completed')
                ->whereYear('studio_service_bookings.booking_date', $month->year)
                ->whereMonth('studio_service_bookings.booking_date', $month->month)
                ->sum(DB::raw('COALESCE(studio_services.discounted_price, studio_services.price)'));
                
            $monthlyRevenue[] = [
                'month' => $monthName,
                'revenue' => $homeRevenue + $studioRevenue
            ];
        }
        
        return $this->sendResponse([
            'total_services' => [
                'home_services' => $totalHomeServices,
                'studio_services' => $totalStudioServices,
                'total' => $totalHomeServices + $totalStudioServices
            ],
            'bookings' => [
                'total' => $totalBookings,
                'today' => $todayBookings,
                'this_week' => $weekBookings,
                'by_status' => $bookingsByStatus
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'monthly' => $monthlyRevenue
            ],
            'upcoming_bookings' => $upcomingBookings
        ], 'تم استرجاع بيانات لوحة التحكم بنجاح');
    }
} 