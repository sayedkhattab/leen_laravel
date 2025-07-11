<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseController;
use App\Models\Customer;
use App\Models\HomeService;
use App\Models\HomeServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class HomeServiceBookingController extends BaseController
{
    use CustomerControllerTrait;
    
    /**
     * عرض قائمة حجوزات خدمات المنزل الخاصة بالعميل
     */
    public function index(Request $request)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $query = HomeServiceBooking::where('customer_id', $customer->id)
            ->with(['homeService', 'homeService.seller']);

        // تصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('booking_status', $request->status);
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        $query->orderBy('date', 'desc');

        $bookings = $query->paginate(10);
        return $this->sendResponse($bookings, 'تم استرجاع قائمة الحجوزات بنجاح');
    }

    /**
     * تخزين حجز جديد
     */
    public function store(Request $request)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'home_service_id' => 'required|exists:home_services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        // التحقق من توفر الخدمة
        $homeService = HomeService::find($request->home_service_id);
        if (!$homeService || $homeService->booking_status !== 'available') {
            return $this->sendError('الخدمة غير متوفرة للحجز حالياً', [], 400);
        }

        // إنشاء الحجز
        $booking = new HomeServiceBooking();
        $booking->customer_id = $customer->id;
        $booking->home_service_id = $request->home_service_id;
        $booking->seller_id = $homeService->seller_id; // إضافة معرف البائع من الخدمة
        $booking->employee_id = 1; // قيمة افتراضية، سيتم تعيينها لاحقاً من قبل البائع
        $booking->date = $request->booking_date;
        $booking->start_time = $request->booking_time;
        $booking->location = $request->address;
        $booking->booking_status = 'pending';
        $booking->payment_status = 'pending';
        $booking->paid_amount = $homeService->discount ? $homeService->discounted_price : $homeService->price; // تعيين قيمة الدفع من سعر الخدمة
        $booking->request_rejection_reason = $request->notes;
        $booking->save();

        // إرسال إشعار للبائع بالحجز الجديد
        // TODO: إضافة رمز إرسال الإشعارات

        return $this->sendResponse($booking->load(['homeService', 'homeService.seller']), 'تم إنشاء الحجز بنجاح');
    }

    /**
     * عرض تفاصيل حجز محدد
     */
    public function show($id)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $booking = HomeServiceBooking::with(['homeService', 'homeService.seller', 'employee', 'payment'])
            ->where('customer_id', $customer->id)
            ->find($id);

        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        return $this->sendResponse($booking, 'تم استرجاع تفاصيل الحجز بنجاح');
    }

    /**
     * تحديث حجز موجود
     */
    public function update(Request $request, $id)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $booking = HomeServiceBooking::where('customer_id', $customer->id)->find($id);
        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        // لا يمكن تعديل الحجوزات المكتملة أو الملغاة
        if (in_array($booking->booking_status, ['completed', 'cancelled'])) {
            return $this->sendError('لا يمكن تعديل الحجوزات المكتملة أو الملغاة', [], 400);
        }

        $validator = Validator::make($request->all(), [
            'booking_date' => 'sometimes|date|after_or_equal:today',
            'booking_time' => 'sometimes',
            'address' => 'sometimes|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        // تحديث بيانات الحجز
        if ($request->has('booking_date')) $booking->date = $request->booking_date;
        if ($request->has('booking_time')) $booking->start_time = $request->booking_time;
        if ($request->has('address')) $booking->location = $request->address;
        if ($request->has('notes')) $booking->request_rejection_reason = $request->notes;
        
        // تغيير الحالة إلى معاد جدولته
        $booking->booking_status = 'rescheduled';
        $booking->save();

        // إرسال إشعار للبائع بتعديل الحجز
        // TODO: إضافة رمز إرسال الإشعارات

        return $this->sendResponse($booking->load(['homeService', 'homeService.seller']), 'تم تحديث الحجز بنجاح');
    }

    /**
     * إلغاء حجز
     */
    public function cancel(Request $request, $id)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $booking = HomeServiceBooking::where('customer_id', $customer->id)->find($id);
        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        // لا يمكن إلغاء الحجوزات المكتملة
        if ($booking->booking_status === 'completed') {
            return $this->sendError('لا يمكن إلغاء الحجوزات المكتملة', [], 400);
        }

        // تحديث حالة الحجز إلى ملغي
        $booking->booking_status = 'cancelled';
        if ($request->has('cancellation_reason')) {
            $booking->request_rejection_reason = $request->cancellation_reason;
        }
        $booking->save();

        // إرسال إشعار للبائع بإلغاء الحجز
        // TODO: إضافة رمز إرسال الإشعارات

        return $this->sendResponse($booking, 'تم إلغاء الحجز بنجاح');
    }

    /**
     * حذف حجز
     */
    public function destroy($id)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $booking = HomeServiceBooking::where('customer_id', $customer->id)->find($id);
        if (!$booking) {
            return $this->sendError('الحجز غير موجود', [], 404);
        }

        // لا يمكن حذف الحجوزات المؤكدة أو المكتملة
        if (in_array($booking->booking_status, ['confirmed', 'completed'])) {
            return $this->sendError('لا يمكن حذف الحجوزات المؤكدة أو المكتملة', [], 400);
        }

        $booking->delete();
        return $this->sendResponse([], 'تم حذف الحجز بنجاح');
    }
} 