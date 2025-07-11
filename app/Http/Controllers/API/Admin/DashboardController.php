<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\HomeService;
use App\Models\HomeServiceBooking;
use App\Models\Seller;
use App\Models\StudioService;
use App\Models\StudioServiceBooking;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * Get dashboard statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = [
            'counts' => [
                'sellers' => [
                    'total' => Seller::count(),
                    'active' => Seller::where('status', 'active')->count(),
                    'pending' => Seller::where('request_status', 'pending')->count(),
                ],
                'customers' => [
                    'total' => Customer::count(),
                    'active' => Customer::where('status', 'active')->count(),
                ],
                'categories' => Category::count(),
                'subcategories' => SubCategory::count(),
                'services' => [
                    'home' => HomeService::count(),
                    'studio' => StudioService::count(),
                    'total' => HomeService::count() + StudioService::count(),
                ],
                'bookings' => [
                    'home' => [
                        'total' => HomeServiceBooking::count(),
                        'pending' => HomeServiceBooking::where('booking_status', 'pending')->count(),
                        'confirmed' => HomeServiceBooking::where('booking_status', 'confirmed')->count(),
                        'completed' => HomeServiceBooking::where('booking_status', 'completed')->count(),
                        'cancelled' => HomeServiceBooking::where('booking_status', 'cancelled')->count(),
                        'rejected' => HomeServiceBooking::where('booking_status', 'rejected')->count(),
                    ],
                    'studio' => [
                        'total' => StudioServiceBooking::count(),
                        'pending' => StudioServiceBooking::where('booking_status', 'pending')->count(),
                        'confirmed' => StudioServiceBooking::where('booking_status', 'confirmed')->count(),
                        'completed' => StudioServiceBooking::where('booking_status', 'completed')->count(),
                        'cancelled' => StudioServiceBooking::where('booking_status', 'cancelled')->count(),
                        'rejected' => StudioServiceBooking::where('booking_status', 'rejected')->count(),
                    ],
                ],
            ],
            'recent' => [
                'sellers' => Seller::latest()->take(5)->get(),
                'customers' => Customer::latest()->take(5)->get(),
                'home_bookings' => HomeServiceBooking::with(['customer', 'seller', 'homeService'])->latest()->take(5)->get(),
                'studio_bookings' => StudioServiceBooking::with(['customer', 'seller', 'studioService'])->latest()->take(5)->get(),
            ],
            'revenue' => [
                'home_services' => HomeServiceBooking::where('payment_status', 'paid')->sum('paid_amount'),
                'studio_services' => StudioServiceBooking::where('payment_status', 'paid')->sum('paid_amount'),
                'total' => HomeServiceBooking::where('payment_status', 'paid')->sum('paid_amount') + 
                          StudioServiceBooking::where('payment_status', 'paid')->sum('paid_amount'),
                'monthly' => $this->getMonthlyRevenue(),
            ],
        ];

        return $this->sendResponse($data, 'Dashboard statistics retrieved successfully.');
    }

    /**
     * Get monthly revenue for the last 6 months.
     *
     * @return array
     */
    private function getMonthlyRevenue(): array
    {
        $homeServiceRevenue = HomeServiceBooking::where('payment_status', 'paid')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(paid_amount) as revenue'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        $studioServiceRevenue = StudioServiceBooking::where('payment_status', 'paid')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(paid_amount) as revenue'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        // Combine and format the data
        $months = [];
        $labels = [];
        $homeData = [];
        $studioData = [];

        // Process home service revenue
        foreach ($homeServiceRevenue as $item) {
            $monthKey = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            $months[$monthKey]['home'] = $item->revenue;
            if (!isset($months[$monthKey]['studio'])) {
                $months[$monthKey]['studio'] = 0;
            }
        }

        // Process studio service revenue
        foreach ($studioServiceRevenue as $item) {
            $monthKey = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            $months[$monthKey]['studio'] = $item->revenue;
            if (!isset($months[$monthKey]['home'])) {
                $months[$monthKey]['home'] = 0;
            }
        }

        // Sort by month
        ksort($months);

        // Format the data for the chart
        foreach ($months as $month => $data) {
            $labels[] = $month;
            $homeData[] = $data['home'];
            $studioData[] = $data['studio'];
        }

        return [
            'labels' => $labels,
            'home_services' => $homeData,
            'studio_services' => $studioData,
        ];
    }

    /**
     * Search for sellers based on a query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchSellers(Request $request)
    {
        $query = $request->input('query');
        
        $sellers = \App\Models\Seller::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->take(10)
            ->get(['id', 'name', 'phone', 'profile_image_path']);
            
        return response()->json($sellers);
    }

    /**
     * Search for home services based on a query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchHomeServices(Request $request)
    {
        $query = $request->input('query');
        
        $services = \App\Models\HomeService::with('seller:id,name')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhereHas('seller', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->take(10)
            ->get(['id', 'name', 'price', 'seller_id']);
            
        return response()->json($services);
    }

    /**
     * Search for studio services based on a query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchStudioServices(Request $request)
    {
        $query = $request->input('query');
        
        $services = \App\Models\StudioService::with('seller:id,name')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhereHas('seller', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->take(10)
            ->get(['id', 'name', 'price', 'seller_id']);
            
        return response()->json($services);
    }
} 