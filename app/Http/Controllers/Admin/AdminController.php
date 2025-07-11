<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\HomeService;
use App\Models\HomeServiceBooking;
use App\Models\Seller;
use App\Models\StudioService;
use App\Models\StudioServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle admin login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'remember'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'بيانات الاعتماد المقدمة غير صحيحة.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get counts for dashboard
        $customersCount = Customer::count();
        $sellersCount = Seller::count();
        
        // Count all services (home + studio)
        $homeServicesCount = HomeService::count();
        $studioServicesCount = StudioService::count();
        $servicesCount = $homeServicesCount + $studioServicesCount;
        
        // Count all bookings (home + studio)
        $homeBookingsCount = HomeServiceBooking::count();
        $studioBookingsCount = StudioServiceBooking::count();
        $bookingsCount = $homeBookingsCount + $studioBookingsCount;
        
        // Get latest sellers
        $latestSellers = Seller::latest()->take(5)->get();
        
        // Get latest customers
        $latestCustomers = Customer::latest()->take(5)->get();
        
        // Get latest bookings (combining home and studio bookings)
        $latestHomeBookings = HomeServiceBooking::with(['customer', 'service.seller'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($booking) {
                $booking->service_type = 'home';
                return $booking;
            });
            
        $latestStudioBookings = StudioServiceBooking::with(['customer', 'service.seller'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($booking) {
                $booking->service_type = 'studio';
                return $booking;
            });
            
        $latestBookings = $latestHomeBookings->concat($latestStudioBookings)
            ->sortByDesc('created_at')
            ->take(10);
        
        return view('admin.dashboard', compact(
            'customersCount',
            'sellersCount',
            'servicesCount',
            'bookingsCount',
            'homeServicesCount',
            'studioServicesCount',
            'homeBookingsCount',
            'studioBookingsCount',
            'latestSellers',
            'latestCustomers',
            'latestBookings'
        ));
    }

    /**
     * Show all services (both home and studio).
     *
     * @return \Illuminate\View\View
     */
    public function services()
    {
        $homeServices = HomeService::with(['seller', 'subCategory.category'])->get()
            ->map(function ($service) {
                $service->type = 'home';
                return $service;
            });
            
        $studioServices = StudioService::with(['seller', 'subCategory.category'])->get()
            ->map(function ($service) {
                $service->type = 'studio';
                return $service;
            });
            
        $services = $homeServices->concat($studioServices);
        
        return view('admin.services.index', compact('services'));
    }

    /**
     * Display the specified service (home or studio).
     *
     * @param string $type The type of service (home|studio)
     * @param int $id      The ID of the service
     * @return \Illuminate\View\View
     */
    public function serviceShow(string $type, int $id)
    {
        if ($type === 'home') {
            $service = HomeService::with(['seller', 'subCategory.category'])->findOrFail($id);
            $service->type = 'home';
        } elseif ($type === 'studio') {
            $service = StudioService::with(['seller', 'subCategory.category'])->findOrFail($id);
            $service->type = 'studio';
        } else {
            abort(404);
        }

        return view('admin.services.show', compact('service'));
    }

    /**
     * Show all bookings (both home and studio).
     *
     * @return \Illuminate\View\View
     */
    public function bookings()
    {
        $homeBookings = HomeServiceBooking::with(['customer', 'service.seller', 'service.subCategory'])
            ->latest()
            ->get()
            ->map(function ($booking) {
                $booking->service_type = 'home';
                return $booking;
            });
            
        $studioBookings = StudioServiceBooking::with(['customer', 'service.seller', 'service.subCategory'])
            ->latest()
            ->get()
            ->map(function ($booking) {
                $booking->service_type = 'studio';
                return $booking;
            });
            
        $bookings = $homeBookings->concat($studioBookings)
            ->sortByDesc('created_at');
        
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Log the admin out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
} 