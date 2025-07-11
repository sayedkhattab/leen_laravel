<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Category;
use App\Models\FeaturedProfessional;
use App\Models\FeaturedService;
use App\Models\PromotionalBanner;
use App\Models\Seller;
use App\Models\SpecialOffer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    /**
     * Get all content for the home screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userType = $request->user() ? $this->getUserType($request->user()) : 'guest';
        
        // Get promotional banners
        $banners = PromotionalBanner::active()
            ->forAudience($userType)
            ->orderBy('display_order')
            ->get()
            ->map(function ($banner) {
                // Add dynamic link based on link type
                $banner->dynamic_link = $this->generateDynamicLink($banner);
                return $banner;
            });
            
        // Get categories
        $categories = Category::where('is_active', true)
            ->orderBy('display_order')
            ->take(10)
            ->get();
            
        // Get featured services (top rated)
        $featuredServices = FeaturedService::with(['service.seller', 'service.category'])
            ->active()
            ->orderBy('display_order')
            ->take(10)
            ->get()
            ->map(function ($featuredService) {
                $service = $featuredService->service;
                
                // Add service type to the response
                $service->service_type = $featuredService->service_type;
                
                return $service;
            });
            
        // Get special offers
        $specialOffers = SpecialOffer::active()
            ->orderBy('display_order')
            ->take(5)
            ->get();
            
        // Get featured professionals
        $featuredProfessionals = FeaturedProfessional::with('seller')
            ->active()
            ->orderBy('display_order')
            ->take(10)
            ->get()
            ->map(function ($featuredPro) {
                $seller = $featuredPro->seller;
                
                // Add featured title and description to the seller
                $seller->featured_title = $featuredPro->featured_title;
                $seller->featured_description = $featuredPro->featured_description;
                
                return $seller;
            });
            
        // Get nearby services based on user location if available
        $nearbyServices = [];
        if ($request->has('latitude') && $request->has('longitude')) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            // Find sellers near the user's location
            $nearbySellers = Seller::where('status', 'active')
                ->where('request_status', 'approved')
                ->whereNotNull('last_latitude')
                ->whereNotNull('last_longitude')
                ->selectRaw("*, 
                    (6371 * acos(cos(radians(?)) * cos(radians(last_latitude)) * 
                    cos(radians(last_longitude) - radians(?)) + 
                    sin(radians(?)) * sin(radians(last_latitude)))) AS distance", 
                    [$latitude, $longitude, $latitude])
                ->having('distance', '<', 10) // Within 10 km
                ->orderBy('distance')
                ->take(5)
                ->get();
                
            // Get one service from each nearby seller
            foreach ($nearbySellers as $seller) {
                $service = $seller->studioServices()
                    ->with(['category', 'subCategory'])
                    ->first();
                    
                if ($service) {
                    $service->distance = round($seller->distance, 1);
                    $service->service_type = 'studio_service';
                    $nearbyServices[] = $service;
                } else {
                    $service = $seller->homeServices()
                        ->with(['category', 'subCategory'])
                        ->first();
                        
                    if ($service) {
                        $service->distance = round($seller->distance, 1);
                        $service->service_type = 'home_service';
                        $nearbyServices[] = $service;
                    }
                }
            }
        }
        
        return $this->sendResponse([
            'banners' => $banners,
            'categories' => $categories,
            'featured_services' => $featuredServices,
            'special_offers' => $specialOffers,
            'featured_professionals' => $featuredProfessionals,
            'nearby_services' => $nearbyServices,
        ], 'Home screen data retrieved successfully.');
    }
    
    /**
     * Generate dynamic link for banner based on link type.
     *
     * @param  \App\Models\PromotionalBanner  $banner
     * @return array
     */
    protected function generateDynamicLink(PromotionalBanner $banner)
    {
        $result = [
            'type' => $banner->link_type,
            'url' => null,
            'id' => null,
            'data' => null
        ];
        
        switch ($banner->link_type) {
            case 'url':
                $result['url'] = $banner->action_url;
                break;
                
            case 'seller':
                if ($banner->linked_seller_id && $banner->linkedSeller) {
                    $result['id'] = $banner->linked_seller_id;
                    $result['data'] = [
                        'name' => $banner->linkedSeller->name,
                        'profile_image' => $banner->linkedSeller->profile_image_path,
                    ];
                }
                break;
                
            case 'home_service':
                if ($banner->linked_home_service_id && $banner->linkedHomeService) {
                    $result['id'] = $banner->linked_home_service_id;
                    $result['data'] = [
                        'name' => $banner->linkedHomeService->name,
                        'seller_id' => $banner->linkedHomeService->seller_id,
                        'seller_name' => $banner->linkedHomeService->seller->name ?? null,
                    ];
                }
                break;
                
            case 'studio_service':
                if ($banner->linked_studio_service_id && $banner->linkedStudioService) {
                    $result['id'] = $banner->linked_studio_service_id;
                    $result['data'] = [
                        'name' => $banner->linkedStudioService->name,
                        'seller_id' => $banner->linkedStudioService->seller_id,
                        'seller_name' => $banner->linkedStudioService->seller->name ?? null,
                    ];
                }
                break;
        }
        
        return $result;
    }
    
    /**
     * Determine user type from user model.
     *
     * @param  mixed  $user
     * @return string
     */
    protected function getUserType($user)
    {
        if ($user instanceof \App\Models\Customer) {
            return 'customer';
        } elseif ($user instanceof \App\Models\Seller) {
            return 'seller';
        } elseif ($user instanceof \App\Models\Admin) {
            return 'admin';
        } else {
            return 'guest';
        }
    }
} 