<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\StudioServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StudioServiceBookingController extends BaseController
{
    /**
     * عرض قائمة حجوزات خدمات الاستوديو الخاصة بالبائع
     */
    public function index(Request $request)
    {
        $seller = Auth::user();
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $query = StudioServiceBooking::whereHas('studioService', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->with(['studioService', 'customer', 'employee']);

        // تصفية حسب حالة الحجز
        if ($request->has('status')) {
            $query->where('booking_status', $request->status);
        }

        // تصفية حسب التاريخ
        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        $query->orderBy('date', 'desc');

        $bookings = $query->paginate(10);
        return $this->sendResponse($bookings, 'تم استرجاع قائمة الحجوزات بنجاح');
    }

    /**
     * عرض تفاصيل حجز محدد
     */
    public function show($id)
    {
        $seller = Auth::user();
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $booking = StudioServiceBooking::with(['studioService', 'customer', 'employee', 'payment'])
            ->whereHas('studioService', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->find($id);

        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        return $this->sendResponse($booking, 'تم استرجاع تفاصيل الحجز بنجاح');
    }

    /**
     * تحديث حالة الحجز
     */
    public function updateStatus(Request $request, $id)
    {
        $seller = Auth::user();
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,completed,cancelled,rescheduled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $booking = StudioServiceBooking::whereHas('studioService', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->find($id);

        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        // تحديث حالة الحجز
        $booking->booking_status = $request->status;
        if ($request->has('notes')) {
            $booking->request_rejection_reason = $request->notes;
        }
        $booking->save();

        // إرسال إشعار للعميل بتغيير حالة الحجز
        // TODO: إضافة رمز إرسال الإشعارات

        return $this->sendResponse($booking, 'تم تحديث حالة الحجز بنجاح');
    }

    /**
     * تخزين حجز جديد (قد لا تكون مطلوبة للبائع ولكن مضافة للاكتمال)
     */
    public function store(Request $request)
    {
        $seller = Auth::user();
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'studio_service_id' => 'required|exists:studio_services,id',
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'nullable|exists:employees,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'status' => 'required|in:pending,confirmed,completed,cancelled,rescheduled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        // التحقق من أن الخدمة تنتمي للبائع
        $studioServiceExists = $seller->studioServices()
            ->where('id', $request->studio_service_id)
            ->exists();

        if (!$studioServiceExists) {
            return $this->sendError('الخدمة غير موجودة أو لا تنتمي لك', [], 404);
        }

        $booking = StudioServiceBooking::create($request->all());
        return $this->sendResponse($booking, 'تم إنشاء الحجز بنجاح');
    }

    /**
     * تحديث حجز موجود
     */
    public function update(Request $request, $id)
    {
        $seller = Auth::user();
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $booking = StudioServiceBooking::whereHas('studioService', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->find($id);

        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'nullable|exists:employees,id',
            'booking_date' => 'sometimes|date|after_or_equal:today',
            'booking_time' => 'sometimes',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled,rescheduled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        // تحديث بيانات الحجز
        if ($request->has('employee_id')) $booking->employee_id = $request->employee_id;
        if ($request->has('booking_date')) $booking->date = $request->booking_date;
        if ($request->has('booking_time')) $booking->start_time = $request->booking_time;
        if ($request->has('status')) $booking->booking_status = $request->status;
        if ($request->has('notes')) $booking->request_rejection_reason = $request->notes;

        $booking->save();
        return $this->sendResponse($booking, 'تم تحديث الحجز بنجاح');
    }

    /**
     * حذف حجز (قد لا تكون مطلوبة للبائع ولكن مضافة للاكتمال)
     */
    public function destroy($id)
    {
        $seller = Auth::user();
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $booking = StudioServiceBooking::whereHas('studioService', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->find($id);

        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        // لا يمكن حذف الحجوزات المكتملة أو المؤكدة
        if (in_array($booking->booking_status, ['confirmed', 'completed'])) {
            return $this->sendError('لا يمكن حذف الحجوزات المؤكدة أو المكتملة', [], 400);
        }

        $booking->delete();
        return $this->sendResponse([], 'تم حذف الحجز بنجاح');
    }
} 