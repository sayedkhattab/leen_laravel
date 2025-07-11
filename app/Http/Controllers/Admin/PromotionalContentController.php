<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FeaturedProfessional;
use App\Models\FeaturedService;
use App\Models\HomeService;
use App\Models\PromotionalBanner;
use App\Models\Seller;
use App\Models\SpecialOffer;
use App\Models\StudioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PromotionalContentController extends Controller
{
    /**
     * Display a listing of the promotional banners.
     */
    public function bannerIndex()
    {
        $banners = PromotionalBanner::orderBy('display_order')->get();
        return view('admin.promotional.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new promotional banner.
     */
    public function bannerCreate()
    {
        $sellers = Seller::all();
        $homeServices = HomeService::with('seller')->get();
        $studioServices = StudioService::with('seller')->get();
        return view('admin.promotional.banners.create', compact('sellers', 'homeServices', 'studioServices'));
    }

    /**
     * Store a newly created promotional banner in storage.
     */
    public function bannerStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'action_text' => 'nullable|string|max:255',
            'link_type' => 'required|in:url,seller,home_service,studio_service',
            'action_url' => 'nullable|string|max:255|required_if:link_type,url',
            'linked_seller_id' => 'nullable|exists:sellers,id|required_if:link_type,seller',
            'linked_home_service_id' => 'nullable|exists:home_services,id|required_if:link_type,home_service',
            'linked_studio_service_id' => 'nullable|exists:studio_services,id|required_if:link_type,studio_service',
            'is_limited_time' => 'required|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'display_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'target_audience' => 'required|in:all,customers,sellers',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle image upload
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/banners'), $imageName);

        PromotionalBanner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => 'images/banners/' . $imageName,
            'action_text' => $request->action_text,
            'action_url' => $request->action_url,
            'link_type' => $request->link_type,
            'linked_seller_id' => $request->link_type === 'seller' ? $request->linked_seller_id : null,
            'linked_home_service_id' => $request->link_type === 'home_service' ? $request->linked_home_service_id : null,
            'linked_studio_service_id' => $request->link_type === 'studio_service' ? $request->linked_studio_service_id : null,
            'is_limited_time' => (bool)$request->is_limited_time,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'display_order' => $request->display_order ?? 0,
            'is_active' => (bool)$request->is_active,
            'target_audience' => $request->target_audience,
        ]);

        return redirect()->route('admin.promotional.banners.index')
            ->with('success', 'تم إضافة البانر الإعلاني بنجاح');
    }

    /**
     * Show the form for editing the specified promotional banner.
     */
    public function bannerEdit(PromotionalBanner $banner)
    {
        $sellers = Seller::all();
        $homeServices = HomeService::with('seller')->get();
        $studioServices = StudioService::with('seller')->get();
        return view('admin.promotional.banners.edit', compact('banner', 'sellers', 'homeServices', 'studioServices'));
    }

    /**
     * Update the specified promotional banner in storage.
     */
    public function bannerUpdate(Request $request, PromotionalBanner $banner)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'action_text' => 'nullable|string|max:255',
            'link_type' => 'required|in:url,seller,home_service,studio_service',
            'action_url' => 'nullable|string|max:255|required_if:link_type,url',
            'linked_seller_id' => 'nullable|exists:sellers,id|required_if:link_type,seller',
            'linked_home_service_id' => 'nullable|exists:home_services,id|required_if:link_type,home_service',
            'linked_studio_service_id' => 'nullable|exists:studio_services,id|required_if:link_type,studio_service',
            'is_limited_time' => 'required|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'display_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'target_audience' => 'required|in:all,customers,sellers',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'action_text' => $request->action_text,
            'action_url' => $request->action_url,
            'link_type' => $request->link_type,
            'linked_seller_id' => $request->link_type === 'seller' ? $request->linked_seller_id : null,
            'linked_home_service_id' => $request->link_type === 'home_service' ? $request->linked_home_service_id : null,
            'linked_studio_service_id' => $request->link_type === 'studio_service' ? $request->linked_studio_service_id : null,
            'is_limited_time' => (bool)$request->is_limited_time,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'display_order' => $request->display_order ?? 0,
            'is_active' => (bool)$request->is_active,
            'target_audience' => $request->target_audience,
        ];

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image
            if (file_exists(public_path($banner->image_path))) {
                unlink(public_path($banner->image_path));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/banners'), $imageName);
            $updateData['image_path'] = 'images/banners/' . $imageName;
        }

        $banner->update($updateData);

        return redirect()->route('admin.promotional.banners.index')
            ->with('success', 'تم تحديث البانر الإعلاني بنجاح');
    }

    /**
     * Remove the specified promotional banner from storage.
     */
    public function bannerDestroy(PromotionalBanner $banner)
    {
        // Delete banner image
        if (file_exists(public_path($banner->image_path))) {
            unlink(public_path($banner->image_path));
        }

        $banner->delete();

        return redirect()->route('admin.promotional.banners.index')
            ->with('success', 'تم حذف البانر الإعلاني بنجاح');
    }

    /**
     * Display a listing of the featured services.
     */
    public function featuredServiceIndex()
    {
        $featuredServices = FeaturedService::with(['service'])->orderBy('display_order')->get();
        return view('admin.promotional.featured-services.index', compact('featuredServices'));
    }

    /**
     * Show the form for creating a new featured service.
     */
    public function featuredServiceCreate()
    {
        $homeServices = HomeService::with('seller')->get();
        $studioServices = StudioService::with('seller')->get();
        return view('admin.promotional.featured-services.create', compact('homeServices', 'studioServices'));
    }

    /**
     * Store a newly created featured service in storage.
     */
    public function featuredServiceStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|integer',
            'service_type' => 'required|in:home_service,studio_service',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify that the service exists
        if ($request->service_type === 'home_service') {
            $service = HomeService::find($request->service_id);
        } else {
            $service = StudioService::find($request->service_id);
        }

        if (!$service) {
            return redirect()->back()
                ->with('error', 'الخدمة غير موجودة')
                ->withInput();
        }

        FeaturedService::create([
            'service_id' => $request->service_id,
            'service_type' => $request->service_type,
            'display_order' => $request->display_order ?? 0,
            'is_active' => $request->has('is_active'),
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.promotional.featured-services.index')
            ->with('success', 'تم إضافة الخدمة المميزة بنجاح');
    }

    /**
     * Remove the specified featured service from storage.
     */
    public function featuredServiceDestroy(FeaturedService $featuredService)
    {
        $featuredService->delete();

        return redirect()->route('admin.promotional.featured-services.index')
            ->with('success', 'تم حذف الخدمة المميزة بنجاح');
    }

    /**
     * Display a listing of the featured professionals.
     */
    public function featuredProfessionalIndex()
    {
        $featuredProfessionals = FeaturedProfessional::with('seller')->orderBy('display_order')->get();
        return view('admin.promotional.featured-professionals.index', compact('featuredProfessionals'));
    }

    /**
     * Show the form for creating a new featured professional.
     */
    public function featuredProfessionalCreate()
    {
        $sellers = Seller::where('status', 'active')
            ->where('request_status', 'approved')
            ->get();
        return view('admin.promotional.featured-professionals.create', compact('sellers'));
    }

    /**
     * Store a newly created featured professional in storage.
     */
    public function featuredProfessionalStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seller_id' => 'required|exists:sellers,id',
            'featured_title' => 'nullable|string|max:255',
            'featured_description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        FeaturedProfessional::create([
            'seller_id' => $request->seller_id,
            'featured_title' => $request->featured_title,
            'featured_description' => $request->featured_description,
            'display_order' => $request->display_order ?? 0,
            'is_active' => $request->has('is_active'),
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.promotional.featured-professionals.index')
            ->with('success', 'تم إضافة مقدم الخدمة المميز بنجاح');
    }

    /**
     * Remove the specified featured professional from storage.
     */
    public function featuredProfessionalDestroy(FeaturedProfessional $featuredProfessional)
    {
        $featuredProfessional->delete();

        return redirect()->route('admin.promotional.featured-professionals.index')
            ->with('success', 'تم حذف مقدم الخدمة المميز بنجاح');
    }

    /**
     * Display a listing of the special offers.
     */
    public function specialOfferIndex()
    {
        $specialOffers = SpecialOffer::orderBy('display_order')->get();
        return view('admin.promotional.special-offers.index', compact('specialOffers'));
    }

    /**
     * Show the form for creating a new special offer.
     */
    public function specialOfferCreate()
    {
        return view('admin.promotional.special-offers.create');
    }

    /**
     * Store a newly created special offer in storage.
     */
    public function specialOfferStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'action_text' => 'nullable|string|max:255',
            'action_url' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle image upload
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/offers'), $imageName);

        SpecialOffer::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => 'images/offers/' . $imageName,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'action_text' => $request->action_text,
            'action_url' => $request->action_url,
            'is_active' => $request->has('is_active'),
            'display_order' => $request->display_order ?? 0,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.promotional.special-offers.index')
            ->with('success', 'تم إضافة العرض الخاص بنجاح');
    }

    /**
     * Show the form for editing the specified special offer.
     */
    public function specialOfferEdit(SpecialOffer $specialOffer)
    {
        return view('admin.promotional.special-offers.edit', compact('specialOffer'));
    }

    /**
     * Update the specified special offer in storage.
     */
    public function specialOfferUpdate(Request $request, SpecialOffer $specialOffer)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'action_text' => 'nullable|string|max:255',
            'action_url' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'action_text' => $request->action_text,
            'action_url' => $request->action_url,
            'is_active' => $request->has('is_active'),
            'display_order' => $request->display_order ?? 0,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
        ];

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image
            if (file_exists(public_path($specialOffer->image_path))) {
                unlink(public_path($specialOffer->image_path));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/offers'), $imageName);
            $updateData['image_path'] = 'images/offers/' . $imageName;
        }

        $specialOffer->update($updateData);

        return redirect()->route('admin.promotional.special-offers.index')
            ->with('success', 'تم تحديث العرض الخاص بنجاح');
    }

    /**
     * Remove the specified special offer from storage.
     */
    public function specialOfferDestroy(SpecialOffer $specialOffer)
    {
        // Delete offer image
        if (file_exists(public_path($specialOffer->image_path))) {
            unlink(public_path($specialOffer->image_path));
        }

        $specialOffer->delete();

        return redirect()->route('admin.promotional.special-offers.index')
            ->with('success', 'تم حذف العرض الخاص بنجاح');
    }
} 