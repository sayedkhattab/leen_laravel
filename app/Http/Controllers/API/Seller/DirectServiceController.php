<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\HomeService;
use App\Models\StudioService;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DirectServiceController extends BaseController
{
    /**
     * تحديث بيانات خدمة منزلية محددة
     */
    public function updateHomeService(Request $request, $id)
    {
        $user = Auth::user();
        
        // تسجيل معلومات المستخدم للتصحيح
        Log::info('User attempting to update home service via direct route:', [
            'user_id' => $user->id ?? 'No user',
            'user_type' => get_class($user) ?? 'Unknown class',
            'token_abilities' => $user ? ($user->currentAccessToken()->abilities ?? 'No abilities') : 'No token',
            'request_data' => $request->all()
        ]);
        
        // التحقق من أن المستخدم مصرح له
        if (!$user) {
            Log::error('No authenticated user found');
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $homeService = HomeService::where('id', $id)->first();
        
        if (!$homeService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }
        
        // التحقق من أن الخدمة تنتمي للمستخدم الحالي
        if ($homeService->seller_id != $user->id) {
            return $this->sendError('غير مصرح لك بتعديل هذه الخدمة', [], 403);
        }

        // Log the current state of the service before updating
        Log::info('Home service before update:', [
            'service_id' => $homeService->id,
            'discount' => $homeService->discount,
            'discount_percentage' => $homeService->discount_percentage,
            'price' => $homeService->price,
            'discounted_price' => $homeService->discounted_price
        ]);

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
            $homeService->discount = $request->discount ? 1 : 0;
            
            if ($homeService->discount && $request->has('discount_percentage')) {
                $homeService->discount_percentage = $request->discount_percentage;
                $homeService->discounted_price = $homeService->price * (1 - $request->discount_percentage / 100);
            } else if ($homeService->discount) {
                // If discount is true but no percentage provided, use existing percentage or default to 0
                if (!$homeService->discount_percentage) {
                    $homeService->discount_percentage = 0;
                }
                $homeService->discounted_price = $homeService->price * (1 - $homeService->discount_percentage / 100);
            } else {
                // If discount is false, set percentage to 0 but keep the original price
                $homeService->discount_percentage = 0;
                $homeService->discounted_price = $homeService->price;
            }
        } else if ($homeService->discount) {
            // If discount field wasn't provided but service has discount enabled, ensure percentage isn't null
            if (!$homeService->discount_percentage) {
                $homeService->discount_percentage = 0;
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
        
        // Log the updated state of the service
        Log::info('Home service after update:', [
            'service_id' => $homeService->id,
            'discount' => $homeService->discount,
            'discount_percentage' => $homeService->discount_percentage,
            'price' => $homeService->price,
            'discounted_price' => $homeService->discounted_price
        ]);
        
        return $this->sendResponse($homeService, 'تم تحديث بيانات الخدمة بنجاح');
    }

    /**
     * تحديث بيانات خدمة استوديو محددة
     */
    public function updateStudioService(Request $request, $id)
    {
        $user = Auth::user();
        
        // تسجيل معلومات المستخدم للتصحيح
        Log::info('User attempting to update studio service via direct route:', [
            'user_id' => $user->id ?? 'No user',
            'user_type' => get_class($user) ?? 'Unknown class',
            'token_abilities' => $user ? ($user->currentAccessToken()->abilities ?? 'No abilities') : 'No token',
            'request_data' => $request->all()
        ]);
        
        // التحقق من أن المستخدم مصرح له
        if (!$user) {
            Log::error('No authenticated user found');
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $studioService = StudioService::where('id', $id)->first();
        
        if (!$studioService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }
        
        // التحقق من أن الخدمة تنتمي للمستخدم الحالي
        if ($studioService->seller_id != $user->id) {
            return $this->sendError('غير مصرح لك بتعديل هذه الخدمة', [], 403);
        }

        // Log the current state of the service before updating
        Log::info('Studio service before update:', [
            'service_id' => $studioService->id,
            'discount' => $studioService->discount,
            'discount_percentage' => $studioService->discount_percentage,
            'price' => $studioService->price,
            'discounted_price' => $studioService->discounted_price
        ]);

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
            $studioService->discount = $request->discount ? 1 : 0;
            
            if ($studioService->discount && $request->has('discount_percentage')) {
                $studioService->discount_percentage = $request->discount_percentage;
                $studioService->discounted_price = $studioService->price * (1 - $request->discount_percentage / 100);
            } else if ($studioService->discount) {
                // If discount is true but no percentage provided, use existing percentage or default to 0
                if (!$studioService->discount_percentage) {
                    $studioService->discount_percentage = 0;
                }
                $studioService->discounted_price = $studioService->price * (1 - $studioService->discount_percentage / 100);
            } else {
                // If discount is false, set percentage to 0 but keep the original price
                $studioService->discount_percentage = 0;
                $studioService->discounted_price = $studioService->price;
            }
        } else if ($studioService->discount) {
            // If discount field wasn't provided but service has discount enabled, ensure percentage isn't null
            if (!$studioService->discount_percentage) {
                $studioService->discount_percentage = 0;
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
        
        // Log the updated state of the service
        Log::info('Studio service after update:', [
            'service_id' => $studioService->id,
            'discount' => $studioService->discount,
            'discount_percentage' => $studioService->discount_percentage,
            'price' => $studioService->price,
            'discounted_price' => $studioService->discounted_price
        ]);
        
        return $this->sendResponse($studioService, 'تم تحديث بيانات الخدمة بنجاح');
    }
} 