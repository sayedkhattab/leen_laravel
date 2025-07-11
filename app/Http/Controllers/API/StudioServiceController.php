<?php

namespace App\Http\Controllers\API;

use App\Models\StudioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudioServiceController extends BaseController
{
    /**
     * Display a listing of the studio services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = StudioService::with(['seller', 'category', 'subCategory']);

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

        $studioServices = $query->paginate(10);
        
        // تسجيل للتصحيح
        \Illuminate\Support\Facades\Log::info('Studio Services Images Debug:', [
            'first_service' => $studioServices->first() ? [
                'id' => $studioServices->first()->id,
                'images_raw' => $studioServices->first()->images,
                'images_type' => gettype($studioServices->first()->images),
            ] : 'No services found'
        ]);
        
        // تحويل مسارات الصور
        $studioServices->getCollection()->transform(function ($service) {
            \Illuminate\Support\Facades\Log::info('Studio Service Image Transformation:', [
                'service_id' => $service->id,
                'original_images' => $service->images
            ]);
            
            if (is_array($service->images)) {
                $service->images = array_map(function($image) {
                    if (strpos($image, 'http') === 0) {
                        return $image;
                    }
                    return url('images/studio_services/' . $image);
                }, $service->images);
            } elseif (is_string($service->images)) {
                // محاولة فك ترميز JSON إذا كان نصًا
                $imagesArray = json_decode($service->images, true);
                if (is_array($imagesArray)) {
                    $service->images = array_map(function($image) {
                        if (strpos($image, 'http') === 0) {
                            return $image;
                        }
                        return url('images/studio_services/' . $image);
                    }, $imagesArray);
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Studio Service Image After Transformation:', [
                'service_id' => $service->id,
                'transformed_images' => $service->images
            ]);
            
            return $service;
        });

        return $this->sendResponse($studioServices, 'Studio services retrieved successfully.');
    }

    /**
     * Display the specified studio service.
     *
     * @param  \App\Models\StudioService  $studioService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(StudioService $studioService): JsonResponse
    {
        $studioService->load(['seller', 'category', 'subCategory']);
        
        // تحويل مسارات الصور
        if (is_array($studioService->images)) {
            $studioService->images = array_map(function($image) {
                if (strpos($image, 'http') === 0) {
                    return $image;
                }
                return url('images/studio_services/' . $image);
            }, $studioService->images);
        } elseif (is_string($studioService->images)) {
            // محاولة فك ترميز JSON إذا كان نصًا
            $imagesArray = json_decode($studioService->images, true);
            if (is_array($imagesArray)) {
                $studioService->images = array_map(function($image) {
                    if (strpos($image, 'http') === 0) {
                        return $image;
                    }
                    return url('images/studio_services/' . $image);
                }, $imagesArray);
            }
        }

        return $this->sendResponse($studioService, 'Studio service retrieved successfully.');
    }
} 