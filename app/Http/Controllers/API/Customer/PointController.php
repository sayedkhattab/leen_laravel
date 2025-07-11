<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointController extends BaseController
{
    use CustomerControllerTrait;
    
    /**
     * عرض نقاط العميل وتاريخ النقاط
     */
    public function index()
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // جلب مجموع النقاط الحالي
        $totalPoints = $customer->points ?? 0;

        // جلب تاريخ النقاط (الاكتساب والاستخدام)
        $pointsHistory = DB::table('customer_points_history')
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse([
            'total_points' => $totalPoints,
            'history' => $pointsHistory
        ], 'تم استرجاع معلومات النقاط بنجاح');
    }

    /**
     * استخدام النقاط للحصول على خصم
     */
    public function usePoints(Request $request)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'points' => 'required|integer|min:1',
            'booking_type' => 'required|in:home_service,studio_service',
            'booking_id' => 'required|integer|exists:' . ($request->booking_type == 'home_service' ? 'home_service_bookings' : 'studio_service_bookings') . ',id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        // التحقق من امتلاك العميل للنقاط الكافية
        if ($customer->points < $request->points) {
            return $this->sendError('لا تملك نقاط كافية', [], 400);
        }

        // التحقق من أن الحجز ينتمي للعميل
        $bookingTable = $request->booking_type == 'home_service' ? 'home_service_bookings' : 'studio_service_bookings';
        $booking = DB::table($bookingTable)
            ->where('id', $request->booking_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$booking) {
            return $this->sendError('الحجز غير موجود أو لا ينتمي لك', [], 404);
        }

        // التحقق من أن الحجز لم يتم استخدام نقاط عليه من قبل
        $discountExists = DB::table('discount_applications')
            ->where('booking_type', $request->booking_type)
            ->where('booking_id', $request->booking_id)
            ->where('discount_type', 'points')
            ->exists();

        if ($discountExists) {
            return $this->sendError('تم استخدام نقاط على هذا الحجز من قبل', [], 400);
        }

        // حساب قيمة الخصم (كل نقطة = 1 ريال مثلاً)
        $discountAmount = $request->points;

        // تسجيل استخدام النقاط
        DB::beginTransaction();
        try {
            // خصم النقاط من رصيد العميل
            DB::table('customers')
                ->where('id', $customer->id)
                ->decrement('points', $request->points);

            // تسجيل استخدام النقاط في تاريخ النقاط
            DB::table('customer_points_history')->insert([
                'customer_id' => $customer->id,
                'points' => -$request->points,
                'action' => 'used',
                'description' => 'استخدام نقاط للحصول على خصم',
                'reference_type' => $request->booking_type,
                'reference_id' => $request->booking_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // تسجيل تطبيق الخصم
            $discountId = DB::table('discount_applications')->insertGetId([
                'booking_type' => $request->booking_type,
                'booking_id' => $request->booking_id,
                'discount_type' => 'points',
                'discount_amount' => $discountAmount,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return $this->sendResponse([
                'discount_amount' => $discountAmount,
                'remaining_points' => $customer->points - $request->points
            ], 'تم استخدام النقاط بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('حدث خطأ أثناء استخدام النقاط', [], 500);
        }
    }
} 