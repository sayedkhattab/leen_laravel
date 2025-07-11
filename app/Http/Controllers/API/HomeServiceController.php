<?php

namespace App\Http\Controllers\API;

use App\Models\HomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeServiceController extends BaseController
{
    /**
     * Display a listing of the home services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = HomeService::with(['seller', 'category', 'subCategory']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by subcategory
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // Filter by gender
        if ($request->has('gender')) {
            $query->where(function ($q) use ($request) {
                $q->where('gender', $request->gender)
                  ->orWhere('gender', 'both');
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by availability
        if ($request->has('available') && $request->available) {
            $query->where('booking_status', 'available');
        }

        // Filter by discount
        if ($request->has('discount') && $request->discount) {
            $query->where('discount', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort by price
        if ($request->has('sort_price')) {
            $query->orderBy('price', $request->sort_price === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $homeServices = $query->paginate(10);
        
        // تسجيل للتصحيح
        \Illuminate\Support\Facades\Log::info('Home Services Images Debug:', [
            'first_service' => $homeServices->first() ? [
                'id' => $homeServices->first()->id,
                'images_raw' => $homeServices->first()->images,
                'images_type' => gettype($homeServices->first()->images),
            ] : 'No services found'
        ]);
        
        // تحويل مسارات الصور
        $homeServices->getCollection()->transform(function ($service) {
            if (is_array($service->images)) {
                $service->images = array_map(function($image) {
                    if (strpos($image, 'http') === 0) {
                        return $image;
                    }
                    return url('images/home_services/' . $image);
                }, $service->images);
            } elseif (is_string($service->images)) {
                // محاولة فك ترميز JSON إذا كان نصًا
                $imagesArray = json_decode($service->images, true);
                if (is_array($imagesArray)) {
                    $service->images = array_map(function($image) {
                        if (strpos($image, 'http') === 0) {
                            return $image;
                        }
                        return url('images/home_services/' . $image);
                    }, $imagesArray);
                }
            }
            return $service;
        });

        return $this->sendResponse($homeServices, 'Home services retrieved successfully.');
    }

    /**
     * Display the specified home service.
     *
     * @param  \App\Models\HomeService  $homeService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(HomeService $homeService): JsonResponse
    {
        $homeService->load(['seller', 'category', 'subCategory']);
        
        // تحويل مسارات الصور
        if (is_array($homeService->images)) {
            $homeService->images = array_map(function($image) {
                if (strpos($image, 'http') === 0) {
                    return $image;
                }
                return url('images/home_services/' . $image);
            }, $homeService->images);
        } elseif (is_string($homeService->images)) {
            // محاولة فك ترميز JSON إذا كان نصًا
            $imagesArray = json_decode($homeService->images, true);
            if (is_array($imagesArray)) {
                $homeService->images = array_map(function($image) {
                    if (strpos($image, 'http') === 0) {
                        return $image;
                    }
                    return url('images/home_services/' . $image);
                }, $imagesArray);
            }
        }

        return $this->sendResponse($homeService, 'Home service retrieved successfully.');
    }
} 