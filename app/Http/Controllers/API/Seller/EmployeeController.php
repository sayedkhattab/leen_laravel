<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends BaseController
{
    /**
     * عرض قائمة الموظفين الخاصين بالبائع
     */
    public function index()
    {
        // قد يكون المستخدم المصادق عليه كائن Seller مباشرة أو كائن User يرتبط بـ Seller
        $authUser = Auth::user();
        $seller = $authUser instanceof \App\Models\Seller ? $authUser : ($authUser->seller ?? null);

        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $employees = Employee::where('seller_id', $seller->id)->get();
        
        // تحويل البيانات لتشمل الروابط الكاملة للصور
        $employees = $employees->map(function ($employee) {
            $employee->photo_url = $employee->photoUrl;
            return $employee;
        });
        
        return $this->sendResponse($employees, 'تم استرجاع قائمة الموظفين بنجاح');
    }

    /**
     * تخزين موظف جديد
     */
    public function store(Request $request)
    {
        // قد يكون المستخدم المصادق عليه كائن Seller مباشرة أو كائن User يرتبط بـ Seller
        $authUser = Auth::user();
        $seller = $authUser instanceof \App\Models\Seller ? $authUser : ($authUser->seller ?? null);

        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'working_days.*' => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'experience_years' => 'nullable|integer|min:0',
            'specialization' => 'nullable|string|max:255',
            'max_bookings_per_day' => 'nullable|integer|min:1',
            'is_available' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $employeeData = $request->only([
            'name', 
            'phone', 
            'position', 
            'email', 
            'experience_years', 
            'specialization',
            'work_start_time',
            'work_end_time',
            'working_days',
            'max_bookings_per_day',
            'is_available',
        ]);
        
        $employeeData['seller_id'] = $seller->id;
        $employeeData['status'] = 'active';

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/employees'), $photoName);
            $employeeData['photo'] = $photoName;
        }

        $employee = Employee::create($employeeData);
        
        // إضافة رابط الصورة للاستجابة
        $employee->photo_url = $employee->photoUrl;
        
        return $this->sendResponse($employee, 'تم إضافة الموظف بنجاح');
    }

    /**
     * عرض بيانات موظف محدد
     */
    public function show($id)
    {
        // قد يكون المستخدم المصادق عليه كائن Seller مباشرة أو كائن User يرتبط بـ Seller
        $authUser = Auth::user();
        $seller = $authUser instanceof \App\Models\Seller ? $authUser : ($authUser->seller ?? null);

        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $employee = Employee::where('id', $id)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$employee) {
            return $this->sendError('الموظف غير موجود', [], 404);
        }

        // إضافة رابط الصورة للاستجابة
        $employee->photo_url = $employee->photoUrl;
        
        // إضافة نص حالة التوفر
        $employee->is_available_text = $employee->isAvailableText;

        return $this->sendResponse($employee, 'تم استرجاع بيانات الموظف بنجاح');
    }

    /**
     * تحديث بيانات موظف محدد
     */
    public function update(Request $request, $id)
    {
        // قد يكون المستخدم المصادق عليه كائن Seller مباشرة أو كائن User يرتبط بـ Seller
        $authUser = Auth::user();
        $seller = $authUser instanceof \App\Models\Seller ? $authUser : ($authUser->seller ?? null);

        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $employee = Employee::where('id', $id)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$employee) {
            return $this->sendError('الموظف غير موجود', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'position' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'working_days.*' => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'experience_years' => 'nullable|integer|min:0',
            'specialization' => 'nullable|string|max:255',
            'max_bookings_per_day' => 'nullable|integer|min:1',
            'is_available' => 'nullable|boolean',
            'status' => 'sometimes|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        // تحديث البيانات
        $updateFields = [
            'name', 'phone', 'position', 'email', 'experience_years', 
            'specialization', 'work_start_time', 'work_end_time', 
            'working_days', 'max_bookings_per_day', 'is_available', 'status'
        ];
        
        foreach ($updateFields as $field) {
            if ($request->has($field)) {
                $employee->$field = $request->$field;
            }
        }

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($employee->photo && file_exists(public_path('images/employees/' . $employee->photo))) {
                unlink(public_path('images/employees/' . $employee->photo));
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/employees'), $photoName);
            $employee->photo = $photoName;
        }

        $employee->save();
        
        // إضافة رابط الصورة للاستجابة
        $employee->photo_url = $employee->photoUrl;
        
        return $this->sendResponse($employee, 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * تحديث حالة توفر الموظف
     */
    public function updateAvailability(Request $request, $id)
    {
        // قد يكون المستخدم المصادق عليه كائن Seller مباشرة أو كائن User يرتبط بـ Seller
        $authUser = Auth::user();
        $seller = $authUser instanceof \App\Models\Seller ? $authUser : ($authUser->seller ?? null);

        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $employee = Employee::where('id', $id)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$employee) {
            return $this->sendError('الموظف غير موجود', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_available' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $employee->is_available = $request->is_available;
        $employee->save();

        return $this->sendResponse($employee, 'تم تحديث حالة توفر الموظف بنجاح');
    }

    /**
     * حذف موظف محدد
     */
    public function destroy($id)
    {
        // قد يكون المستخدم المصادق عليه كائن Seller مباشرة أو كائن User يرتبط بـ Seller
        $authUser = Auth::user();
        $seller = $authUser instanceof \App\Models\Seller ? $authUser : ($authUser->seller ?? null);

        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $employee = Employee::where('id', $id)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$employee) {
            return $this->sendError('الموظف غير موجود', [], 404);
        }

        // حذف الصورة إذا كانت موجودة
        if ($employee->photo && file_exists(public_path('images/employees/' . $employee->photo))) {
            unlink(public_path('images/employees/' . $employee->photo));
        }

        $employee->delete();
        return $this->sendResponse([], 'تم حذف الموظف بنجاح');
    }

    /**
     * الحصول على الموظفين المتاحين في تاريخ ووقت محددين
     */
    public function getAvailableEmployees(Request $request)
    {
        // قد يكون المستخدم المصادق عليه كائن Seller مباشرة أو كائن User يرتبط بـ Seller
        $authUser = Auth::user();
        $seller = $authUser instanceof \App\Models\Seller ? $authUser : ($authUser->seller ?? null);

        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $date = $request->date;
        $time = $request->time;
        
        $employees = Employee::where('seller_id', $seller->id)
            ->where('status', 'active')
            ->where('is_available', true)
            ->get();
        
        $availableEmployees = $employees->filter(function ($employee) use ($date, $time) {
            return $employee->isAvailableAt($date, $time);
        });
        
        // تحويل البيانات لتشمل الروابط الكاملة للصور
        $availableEmployees = $availableEmployees->map(function ($employee) {
            $employee->photo_url = $employee->photoUrl;
            return $employee;
        });
        
        return $this->sendResponse($availableEmployees->values(), 'تم استرجاع قائمة الموظفين المتاحين بنجاح');
    }
} 