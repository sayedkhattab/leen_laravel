<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseController
{
    /**
     * عرض الملف الشخصي للعميل المصادق
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $customer = Auth::guard('customer')->user();
        
        // التأكد من وجود البيانات الأساسية
        $customerData = [
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'location' => $customer->location,
            'image' => $customer->image ? url('storage/' . $customer->image) : null,
            'status' => $customer->status,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at,
        ];
        
        // إضافة إحصائيات إضافية
        $customerData['bookings_count'] = $customer->homeServiceBookings()->count() + $customer->studioServiceBookings()->count();
        $customerData['points'] = $customer->points ?? 0;
        
        return $this->sendResponse($customerData, 'Profile retrieved successfully.');
    }

    /**
     * تحديث الملف الشخصي للعميل المصادق
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:customers,email,' . $customer->id,
            'location' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }
        
        // تحديث البيانات الأساسية
        if ($request->has('first_name')) {
            $customer->first_name = $request->first_name;
        }
        
        if ($request->has('last_name')) {
            $customer->last_name = $request->last_name;
        }
        
        if ($request->has('email')) {
            $customer->email = $request->email;
        }
        
        if ($request->has('location')) {
            $customer->location = $request->location;
        }
        
        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($customer->image && Storage::disk('public')->exists($customer->image)) {
                Storage::disk('public')->delete($customer->image);
            }
            
            // تحميل الصورة الجديدة
            $imagePath = $request->file('image')->store('customers', 'public');
            $customer->image = $imagePath;
        }
        
        $customer->save();
        
        // إعداد البيانات للرد
        $customerData = [
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'location' => $customer->location,
            'image' => $customer->image ? url('storage/' . $customer->image) : null,
            'status' => $customer->status,
            'created_at' => $customer->created_at,
            'updated_at' => $customer->updated_at,
        ];
        
        return $this->sendResponse($customerData, 'Profile updated successfully.');
    }

    /**
     * تغيير كلمة مرور العميل المصادق
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }
        
        $customer = Auth::guard('customer')->user();
        
        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $customer->password)) {
            return $this->sendError('Validation Error.', ['current_password' => ['Current password is incorrect']], 422);
        }
        
        // تحديث كلمة المرور
        $customer->password = Hash::make($request->password);
        $customer->save();
        
        return $this->sendResponse(['message' => 'Password changed successfully'], 'Password changed successfully.');
    }

    /**
     * حذف حساب العميل المصادق
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'reason' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }
        
        $customer = Auth::guard('customer')->user();
        
        // التحقق من كلمة المرور
        if (!Hash::check($request->password, $customer->password)) {
            return $this->sendError('Validation Error.', ['password' => ['Password is incorrect']], 422);
        }
        
        // حفظ سبب حذف الحساب إذا تم توفيره
        if ($request->has('reason')) {
            // يمكن إضافة جدول لتخزين أسباب حذف الحسابات
            // AccountDeletionReason::create([
            //     'user_id' => $customer->id,
            //     'user_type' => 'customer',
            //     'reason' => $request->reason,
            // ]);
        }
        
        // إبطال جميع التوكنات
        $customer->tokens()->delete();
        
        // حذف الحساب
        $customer->delete();
        
        return $this->sendResponse([], 'Account deleted successfully.');
    }
} 