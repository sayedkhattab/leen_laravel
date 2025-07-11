<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerController extends BaseController
{
    /**
     * Display a listing of the latest approved & active sellers.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 10);

        $sellers = Seller::where('status', 'active')
            ->where('request_status', 'approved')
            ->latest() // order by created_at desc
            ->take($limit)
            ->get([
                'id',
                'first_name',
                'last_name',
                'seller_logo as profile_image_path',
            ])->map(function ($seller) {
                $seller->full_name = $seller->first_name . ' ' . $seller->last_name;
                $seller->profile_image_url = $seller->profile_image_path ? url($seller->profile_image_path) : null;
                return $seller;
            });

        return $this->sendResponse($sellers, 'Latest sellers retrieved successfully');
    }

    /**
     * Display the specified seller details for customers.
     *
     * @param  \App\Models\Seller  $seller
     * @return JsonResponse
     */
    public function show(Seller $seller): JsonResponse
    {
        // Check if seller is active and approved
        if ($seller->status !== 'active' || $seller->request_status !== 'approved') {
            return $this->sendError('Seller not available', [], 404);
        }

        // Load seller with all related data
        $seller->load([
            'homeServices' => function ($query) {
                $query->with(['category', 'subCategory'])
                      ->where('booking_status', 'available');
            },
            'studioServices' => function ($query) {
                $query->with(['category', 'subCategory'])
                      ->where('booking_status', 'available');
            },
            'employees'
        ]);

        // Calculate seller statistics
        $totalServices = $seller->homeServices->count() + $seller->studioServices->count();
        $totalBookings = $seller->homeServiceBookings()->count() + $seller->studioServiceBookings()->count();
        
        // Calculate average rating (if you have rating system)
        // $averageRating = $seller->ratings()->avg('rating') ?? 0;

        // Prepare response data
        $sellerData = [
            'id' => $seller->id,
            'full_name' => $seller->first_name . ' ' . $seller->last_name,
            'first_name' => $seller->first_name,
            'last_name' => $seller->last_name,
            'email' => $seller->email,
            'phone' => $seller->phone,
            'phone_verified' => !is_null($seller->phone_verified_at),
            'location' => $seller->location,
            'service_type' => $seller->service_type,
            'seller_logo' => $seller->seller_logo ? url($seller->seller_logo) : null,
            'seller_banner' => $seller->seller_banner ? url($seller->seller_banner) : null,
            'license' => $seller->license ? url($seller->license) : null,
            'commercial_register' => $seller->commercial_register,
            'last_latitude' => $seller->last_latitude,
            'last_longitude' => $seller->last_longitude,
            'location_tracking_enabled' => $seller->location_tracking_enabled,
            'created_at' => $seller->created_at,
            'updated_at' => $seller->updated_at,
            
            // Statistics
            'total_services' => $totalServices,
            'total_bookings' => $totalBookings,
            'total_employees' => $seller->employees->count(),
            // 'average_rating' => round($averageRating, 1),
            
            // Services data
            'home_services' => $seller->homeServices->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'service_details' => $service->service_details,
                    'price' => $service->price,
                    'discounted_price' => $service->discounted_price,
                    'discount' => $service->discount,
                    'discount_percentage' => $service->discount_percentage,
                    'duration' => $service->duration,
                    'gender' => $service->gender,
                    'booking_status' => $service->booking_status,
                    'points' => $service->points,
                    'images' => $service->images ? array_map(function($image) {
                        return url('storage/images/home_services/' . $image);
                    }, json_decode($service->images, true)) : [],
                    'category' => $service->category,
                    'sub_category' => $service->subCategory,
                    'employees' => $service->employees,
                    'created_at' => $service->created_at,
                ];
            }),
            
            'studio_services' => $seller->studioServices->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'service_details' => $service->service_details,
                    'price' => $service->price,
                    'discounted_price' => $service->discounted_price,
                    'discount' => $service->discount,
                    'discount_percentage' => $service->discount_percentage,
                    'duration' => $service->duration,
                    'gender' => $service->gender,
                    'booking_status' => $service->booking_status,
                    'location' => $service->location,
                    'points' => $service->points,
                    'images' => $service->images ? array_map(function($image) {
                        return url('storage/images/studio_services/' . $image);
                    }, json_decode($service->images, true)) : [],
                    'category' => $service->category,
                    'sub_category' => $service->subCategory,
                    'employees' => $service->employees,
                    'created_at' => $service->created_at,
                ];
            }),
            
            'employees' => $seller->employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'phone' => $employee->phone,
                    'email' => $employee->email,
                    'experience_years' => $employee->experience_years,
                    'specialization' => $employee->specialization,
                    'image' => $employee->image ? url($employee->image) : null,
                ];
            }),
        ];

        return $this->sendResponse($sellerData, 'Seller details retrieved successfully');
    }

    public function homeServices(Seller $seller): JsonResponse
    {
        // Ensure seller is active & approved
        if ($seller->status !== 'active' || $seller->request_status !== 'approved') {
            return $this->sendError('Seller not available', [], 404);
        }

        $services = $seller->homeServices()
            ->with(['category', 'subCategory'])
            ->where('booking_status', 'available')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'service_details' => $service->service_details,
                    'price' => $service->price,
                    'discounted_price' => $service->discounted_price,
                    'discount' => $service->discount,
                    'discount_percentage' => $service->discount_percentage,
                    'duration' => $service->duration,
                    'gender' => $service->gender,
                    'booking_status' => $service->booking_status,
                    'points' => $service->points,
                    'images' => $service->images ? array_map(function($image) {
                        return url('storage/images/home_services/' . $image);
                    }, json_decode($service->images, true)) : [],
                    'category' => $service->category,
                    'sub_category' => $service->subCategory,
                    'employees' => $service->employees,
                    'created_at' => $service->created_at,
                ];
            });

        return $this->sendResponse($services, 'Seller home services retrieved successfully');
    }

    public function studioServices(Seller $seller): JsonResponse
    {
        if ($seller->status !== 'active' || $seller->request_status !== 'approved') {
            return $this->sendError('Seller not available', [], 404);
        }

        $services = $seller->studioServices()
            ->with(['category', 'subCategory'])
            ->where('booking_status', 'available')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'service_details' => $service->service_details,
                    'price' => $service->price,
                    'discounted_price' => $service->discounted_price,
                    'discount' => $service->discount,
                    'discount_percentage' => $service->discount_percentage,
                    'duration' => $service->duration,
                    'gender' => $service->gender,
                    'booking_status' => $service->booking_status,
                    'location' => $service->location,
                    'points' => $service->points,
                    'images' => $service->images ? array_map(function($image) {
                        return url('storage/images/studio_services/' . $image);
                    }, json_decode($service->images, true)) : [],
                    'category' => $service->category,
                    'sub_category' => $service->subCategory,
                    'employees' => $service->employees,
                    'created_at' => $service->created_at,
                ];
            });

        return $this->sendResponse($services, 'Seller studio services retrieved successfully');
    }

    public function statistics(Seller $seller): JsonResponse
    {
        if ($seller->status !== 'active' || $seller->request_status !== 'approved') {
            return $this->sendError('Seller not available', [], 404);
        }

        $totalServices = $seller->homeServices()->count() + $seller->studioServices()->count();
        $totalBookings = $seller->homeServiceBookings()->count() + $seller->studioServiceBookings()->count();
        $stats = [
            'total_services' => $totalServices,
            'total_bookings' => $totalBookings,
            'total_employees' => $seller->employees()->count(),
            // Future: include ratings
        ];
        return $this->sendResponse($stats, 'Seller statistics retrieved successfully');
    }

    public function employees(Seller $seller): JsonResponse
    {
        if ($seller->status !== 'active' || $seller->request_status !== 'approved') {
            return $this->sendError('Seller not available', [], 404);
        }

        $employees = $seller->employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->position,
                'phone' => $employee->phone,
                'email' => $employee->email,
                'experience_years' => $employee->experience_years,
                'specialization' => $employee->specialization,
                'photo_url' => $employee->photoUrl,
                'is_available' => $employee->is_available,
                'is_available_text' => $employee->isAvailableText,
                'work_start_time' => $employee->work_start_time,
                'work_end_time' => $employee->work_end_time,
                'working_days' => $employee->working_days,
                'rating' => $employee->rating,
                'status' => $employee->status,
            ];
        });

        return $this->sendResponse($employees, 'Seller employees retrieved successfully');
    }
} 