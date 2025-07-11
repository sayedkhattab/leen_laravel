<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\StudioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StudioServiceController extends BaseController
{
    /**
     * عرض قائمة خدمات الاستوديو الخاصة بالبائع
     */
    public function index()
    {
        $user = Auth::user();
        Log::info('User attempting to get studio services:', [
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

        $studioServices = StudioService::where('seller_id', $user->id)
            ->with(['category', 'subCategory'])
            ->get();
            
        return $this->sendResponse($studioServices, 'تم استرجاع قائمة خدمات الاستوديو بنجاح');
    }

    /**
     * تخزين خدمة استوديو جديدة
     */
    public function store(Request $request)
    {
        // إضافة سجلات تصحيح للتحقق من المستخدم
        $user = Auth::user();
        Log::info('User attempting to add studio service:', [
            'user_id' => $user->id,
            'user_type' => get_class($user),
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
            'location' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors()->toArray(), 422);
        }

        $studioServiceData = $request->all();
        $studioServiceData['seller_id'] = $user->id;
        
        // حساب السعر بعد الخصم إذا كان هناك خصم
        if ($request->has('discount') && $request->discount && $request->has('discount_percentage')) {
            $studioServiceData['discounted_price'] = $request->price * (1 - $request->discount_percentage / 100);
        }

        // معالجة الصور إذا تم تحميلها
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/studio_services'), $imageName);
                $images[] = $imageName;
            }
            $studioServiceData['images'] = json_encode($images);
        }

        $studioService = StudioService::create($studioServiceData);
        return $this->sendResponse($studioService, 'تم إضافة خدمة الاستوديو بنجاح');
    }

    /**
     * عرض بيانات خدمة استوديو محددة
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user || !($user instanceof \App\Models\Seller)) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $studioService = StudioService::where('id', $id)
            ->where('seller_id', $user->id)
            ->with(['category', 'subCategory'])
            ->first();

        if (!$studioService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        return $this->sendResponse($studioService, 'تم استرجاع بيانات الخدمة بنجاح');
    }

    /**
     * تحديث بيانات خدمة استوديو محددة
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // تسجيل معلومات المستخدم للتصحيح
        Log::info('User attempting to update studio service:', [
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

        $studioService = StudioService::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$studioService) {
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
            'location' => 'sometimes|required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors()->toArray(), 422);
        }

        // تحديث البيانات
        if ($request->has('name')) $studioService->name = $request->name;
        if ($request->has('description')) $studioService->description = $request->description;
        if ($request->has('price')) $studioService->price = $request->price;
        if ($request->has('duration')) $studioService->duration = $request->duration;
        if ($request->has('category_id')) $studioService->category_id = $request->category_id;
        if ($request->has('sub_category_id')) $studioService->sub_category_id = $request->sub_category_id;
        if ($request->has('gender')) $studioService->gender = $request->gender;
        if ($request->has('booking_status')) $studioService->booking_status = $request->booking_status;
        if ($request->has('location')) $studioService->location = $request->location;
        
        // تحديث معلومات الخصم
        if ($request->has('discount')) {
            $studioService->discount = $request->discount;
            
            if ($request->discount && $request->has('discount_percentage')) {
                $studioService->discount_percentage = $request->discount_percentage;
                $studioService->discounted_price = $studioService->price * (1 - $request->discount_percentage / 100);
            } else {
                $studioService->discount_percentage = null;
                $studioService->discounted_price = null;
            }
        }

        // معالجة الصور إذا تم تحميلها
        if ($request->hasFile('images')) {
            // حذف الصور القديمة إذا كانت موجودة
            if ($studioService->images) {
                $oldImages = json_decode($studioService->images);
                foreach ($oldImages as $oldImage) {
                    if (file_exists(public_path('images/studio_services/' . $oldImage))) {
                        unlink(public_path('images/studio_services/' . $oldImage));
                    }
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/studio_services'), $imageName);
                $images[] = $imageName;
            }
            $studioService->images = json_encode($images);
        }

        $studioService->save();
        return $this->sendResponse($studioService, 'تم تحديث بيانات الخدمة بنجاح');
    }

    /**
     * حذف خدمة استوديو محددة
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !($user instanceof \App\Models\Seller)) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $studioService = StudioService::where('id', $id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$studioService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        // حذف الصور إذا كانت موجودة
        if ($studioService->images) {
            $images = json_decode($studioService->images);
            foreach ($images as $image) {
                if (file_exists(public_path('images/studio_services/' . $image))) {
                    unlink(public_path('images/studio_services/' . $image));
                }
            }
        }

        $studioService->delete();
        return $this->sendResponse([], 'تم حذف الخدمة بنجاح');
    }
} 