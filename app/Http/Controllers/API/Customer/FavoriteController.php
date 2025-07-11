<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseController;
use App\Models\Customer;
use App\Models\HomeService;
use App\Models\StudioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteController extends BaseController
{
    /**
     * عرض قائمة المفضلة للعميل
     */
    public function index(Request $request)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // جلب الخدمات المنزلية المفضلة
        $favoriteHomeServices = $customer->favoriteHomeServices()
            ->with(['seller', 'category', 'subCategory'])
            ->get();

        // جلب خدمات الاستوديو المفضلة
        $favoriteStudioServices = $customer->favoriteStudioServices()
            ->with(['seller', 'category', 'subCategory'])
            ->get();

        return $this->sendResponse([
            'home_services' => $favoriteHomeServices,
            'studio_services' => $favoriteStudioServices
        ], 'تم استرجاع قائمة المفضلة بنجاح');
    }

    /**
     * إضافة خدمة منزلية للمفضلة
     */
    public function addHomeService($homeServiceId)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $homeService = HomeService::find($homeServiceId);
        if (!$homeService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        // التحقق من أن الخدمة غير موجودة بالفعل في المفضلة
        $exists = $customer->favoriteHomeServices()->where('home_service_id', $homeServiceId)->exists();
        if ($exists) {
            return $this->sendError('الخدمة موجودة بالفعل في المفضلة', [], 400);
        }

        // إضافة الخدمة للمفضلة
        $customer->favoriteHomeServices()->attach($homeServiceId);

        return $this->sendResponse([], 'تم إضافة الخدمة للمفضلة بنجاح');
    }

    /**
     * إضافة خدمة استوديو للمفضلة
     */
    public function addStudioService($studioServiceId)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $studioService = StudioService::find($studioServiceId);
        if (!$studioService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        // التحقق من أن الخدمة غير موجودة بالفعل في المفضلة
        $exists = $customer->favoriteStudioServices()->where('studio_service_id', $studioServiceId)->exists();
        if ($exists) {
            return $this->sendError('الخدمة موجودة بالفعل في المفضلة', [], 400);
        }

        // إضافة الخدمة للمفضلة
        $customer->favoriteStudioServices()->attach($studioServiceId);

        return $this->sendResponse([], 'تم إضافة الخدمة للمفضلة بنجاح');
    }

    /**
     * إزالة خدمة منزلية من المفضلة
     */
    public function removeHomeService($homeServiceId)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // التحقق من أن الخدمة موجودة في المفضلة
        $exists = $customer->favoriteHomeServices()->where('home_service_id', $homeServiceId)->exists();
        if (!$exists) {
            return $this->sendError('الخدمة غير موجودة في المفضلة', [], 404);
        }

        // إزالة الخدمة من المفضلة
        $customer->favoriteHomeServices()->detach($homeServiceId);

        return $this->sendResponse([], 'تم إزالة الخدمة من المفضلة بنجاح');
    }

    /**
     * إزالة خدمة استوديو من المفضلة
     */
    public function removeStudioService($studioServiceId)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // التحقق من أن الخدمة موجودة في المفضلة
        $exists = $customer->favoriteStudioServices()->where('studio_service_id', $studioServiceId)->exists();
        if (!$exists) {
            return $this->sendError('الخدمة غير موجودة في المفضلة', [], 404);
        }

        // إزالة الخدمة من المفضلة
        $customer->favoriteStudioServices()->detach($studioServiceId);

        return $this->sendResponse([], 'تم إزالة الخدمة من المفضلة بنجاح');
    }
} 