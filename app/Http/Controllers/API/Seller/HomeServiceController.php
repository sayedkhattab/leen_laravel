<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\HomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class HomeServiceController extends BaseController
{
    /**
     * عرض قائمة خدمات المنزل الخاصة بالبائع
     */
    public function index()
    {
        $user = Auth::user();
        Log::info('User attempting to get home services:', [
            'user_id' => $user->id ?? 'No user',
            'user_type' => get_class($user) ?? 'Unknown class',
            'token_abilities' => $user ? ($user->currentAccessToken()->abilities ?? 'No abilities') : 'No token'
        ]);
        
        // التحقق من أن المستخدم هو بائع
        if (!$user || !($user instanceof \App\Models\Seller)) {
            Log::error('User is not a seller:', [
                'user_id' => $user->id ?? 'No user',
                'user_class' => get_class($user) ?? 'Unknown class'
            ]);
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $homeServices = HomeService::where('seller_id', $user->id)
            ->with(['category', 'subCategory'])
            ->get();
            
        return $this->sendResponse($homeServices, 'تم استرجاع قائمة خدمات المنزل بنجاح');
    }

    /**
     * تخزين خدمة منزلية جديدة
     */
    public function store(Request $request)
    {
        // إضافة سجلات تصحيح للتحقق من المستخدم
        $user = Auth::user();
        Log::info('User attempting to add home service:', [
            'user_id' => $user->id,
            'user_type' => get_class($user),
            'has_seller_property' => isset($user->seller),
            'token_abilities' => $request->user()->currentAccessToken()->abilities ?? 'No abilities',
            'request_status' => $user->request_status ?? 'unknown'
        ]);
        
        // التحقق من أن المستخدم هو بائع
        if (!$user || !($user instanceof \App\Models\Seller)) {
            Log::error('User is not a seller:', [
                'user_id' => $user->id ?? 'No user',
                'user_class' => get_class($user) ?? 'Unknown class'
            ]);
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }
        
        // التحقق من حالة اعتماد حساب البائع
        if ($user->request_status !== 'approved') {
            Log::error('Seller account not approved:', [
                'seller_id' => $user->id,
                'request_status' => $user->request_status
            ]);
            return $this->sendError('يجب اعتماد حسابك أولاً قبل إضافة الخدمات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'gender' => 'required|in:male,female,both',
            'booking_status' => 'required|in:available,unavailable',
            'discount' => 'boolean',
            'discount_percentage' => 'nullable|required_if:discount,1|numeric|min:1|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors()->toArray(), 422);
        }

        $homeServiceData = $request->all();
        $homeServiceData['seller_id'] = $user->id;
        
        // حساب السعر بعد الخصم إذا كان هناك خصم
        if ($request->has('discount') && $request->discount && $request->has('discount_percentage')) {
            $homeServiceData['discounted_price'] = $request->price * (1 - $request->discount_percentage / 100);
        }

        // معالجة الصور إذا تم تحميلها
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/home_services'), $imageName);
                $images[] = $imageName;
            }
            $homeServiceData['images'] = json_encode($images);
        }

        $homeService = HomeService::create($homeServiceData);
        return $this->sendResponse($homeService, 'تم إضافة خدمة المنزل بنجاح');
    }

    /**
     * عرض بيانات خدمة منزلية محددة
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم هو بائع
        if (!$user || !($user instanceof \App\Models\Seller)) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $homeService = HomeService::where('id', $id)
            ->where('seller_id', $user->id)
            ->with(['category', 'subCategory'])
            ->first();

        if (!$homeService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        return $this->sendResponse($homeService, 'تم استرجاع بيانات الخدمة بنجاح');
    }

    /**
     * تحديث بيانات خدمة منزلية محددة
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // تسجيل معلومات المستخدم للتصحيح
        Log::info('User attempting to update home service:', [
            'user_id' => $user->id ?? 'No user',
            'user_type' => get_class($user) ?? 'Unknown class',
            'token_abilities' => $user ? ($user->currentAccessToken()->abilities ?? 'No abilities') : 'No token'
        ]);
        
        // التحقق من أن المستخدم هو بائع باستخدام طريقة أكثر موثوقية
        if (!$user) {
            Log::error('No authenticated user found');
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }
        
        // التحقق من نوع المستخدم بطريقة أكثر أماناً
        if (!($user instanceof \App\Models\Seller)) {
            Log::error('User is not a seller:', [
                'user_id' => $user->id,
                'user_class' => get_class($user)
            ]);
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $homeService = HomeService::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$homeService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'duration' => 'sometimes|required|integer|min:1',
            'category_id' => 'sometimes|required|exists:categories,id',
            'sub_category_id' => 'sometimes|required|exists:sub_categories,id',
            'gender' => 'sometimes|required|in:male,female,both',
            'booking_status' => 'sometimes|required|in:available,unavailable',
            'discount' => 'boolean',
            'discount_percentage' => 'nullable|required_if:discount,1|numeric|min:1|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors()->toArray(), 422);
        }

        // تحديث البيانات
        if ($request->has('name')) $homeService->name = $request->name;
        if ($request->has('description')) $homeService->description = $request->description;
        if ($request->has('price')) $homeService->price = $request->price;
        if ($request->has('duration')) $homeService->duration = $request->duration;
        if ($request->has('category_id')) $homeService->category_id = $request->category_id;
        if ($request->has('sub_category_id')) $homeService->sub_category_id = $request->sub_category_id;
        if ($request->has('gender')) $homeService->gender = $request->gender;
        if ($request->has('booking_status')) $homeService->booking_status = $request->booking_status;
        
        // تحديث معلومات الخصم
        if ($request->has('discount')) {
            $homeService->discount = $request->discount;
            
            if ($request->discount && $request->has('discount_percentage')) {
                $homeService->discount_percentage = $request->discount_percentage;
                $homeService->discounted_price = $homeService->price * (1 - $request->discount_percentage / 100);
            } else {
                $homeService->discount_percentage = null;
                $homeService->discounted_price = null;
            }
        }

        // معالجة الصور إذا تم تحميلها
        if ($request->hasFile('images')) {
            // حذف الصور القديمة إذا كانت موجودة
            if ($homeService->images) {
                $oldImages = json_decode($homeService->images);
                foreach ($oldImages as $oldImage) {
                    if (file_exists(public_path('images/home_services/' . $oldImage))) {
                        unlink(public_path('images/home_services/' . $oldImage));
                    }
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/home_services'), $imageName);
                $images[] = $imageName;
            }
            $homeService->images = json_encode($images);
        }

        $homeService->save();
        return $this->sendResponse($homeService, 'تم تحديث بيانات الخدمة بنجاح');
    }

    /**
     * حذف خدمة منزلية محددة
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم هو بائع
        if (!$user || !($user instanceof \App\Models\Seller)) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $homeService = HomeService::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$homeService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        // حذف الصور إذا كانت موجودة
        if ($homeService->images) {
            $images = json_decode($homeService->images);
            foreach ($images as $image) {
                if (file_exists(public_path('images/home_services/' . $image))) {
                    unlink(public_path('images/home_services/' . $image));
                }
            }
        }

        $homeService->delete();
        return $this->sendResponse([], 'تم حذف الخدمة بنجاح');
    }
} 