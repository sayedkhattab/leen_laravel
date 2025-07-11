<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\HomeServiceBooking;
use App\Models\Payment;
use App\Models\StudioServiceBooking;
use App\Services\Payment\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends BaseController
{
    protected $paymobService;
    
    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }
    
    /**
     * الحصول على كائن العميل الحالي
     */
    protected function getCurrentCustomer()
    {
        $user = Auth::user();
        
        // إذا كان المستخدم هو عميل مباشرة
        if ($user instanceof Customer) {
            return $user;
        }
        
        // إذا كان المستخدم له علاقة عميل
        if ($user && $user->customer) {
            return $user->customer;
        }
        
        return null;
    }
    
    /**
     * إنشاء طلب دفع جديد (يدعم كلا من الخدمات المنزلية وخدمات الاستوديو)
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
            'booking_type' => 'required|in:home,studio',
            'is_partial' => 'boolean',
            'deposit_percentage' => 'nullable|numeric|min:1|max:99',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // تحديد نوع الحجز وجلب البيانات المناسبة
        $booking = null;
        if ($request->booking_type === 'home') {
            $booking = HomeServiceBooking::where('id', $request->booking_id)
                ->where('customer_id', $customer->id)
                ->first();
        } else { // studio
            $booking = StudioServiceBooking::where('id', $request->booking_id)
                ->where('customer_id', $customer->id)
                ->first();
        }

        if (!$booking) {
            return $this->sendError('الحجز غير موجود أو لا ينتمي لك', [], 404);
        }

        // التحقق من أن الحجز لم يتم دفعه بالفعل
        if ($booking->payment_status === 'paid') {
            return $this->sendError('تم دفع هذا الحجز بالفعل', [], 400);
        }

        // إنشاء سجل دفع جديد مع دعم الدفع الجزئي
        $paymentData = [
            'amount' => $booking->paid_amount,
            'user_id' => $customer->id,
            'payment_method' => 'paymob',
            'status' => 'Pending',
            'is_partial' => $request->is_partial ?? false,
            'payment_data' => json_encode([
                'created_at' => now()->toIso8601String(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ];
        
        // إضافة نسبة العربون إذا كان الدفع جزئي
        if ($request->is_partial && $request->deposit_percentage) {
            $paymentData['deposit_percentage'] = $request->deposit_percentage;
            
            // حساب المبلغ المدفوع (العربون)
            $depositAmount = $booking->paid_amount * ($request->deposit_percentage / 100);
            $paymentData['paid_amount'] = $depositAmount;
        }

        $payment = Payment::create($paymentData);
        
        // تحديث معرف الدفع في الحجز
        $booking->payment_id = $payment->id;
        $booking->save();

        // إنشاء رابط الدفع
        $paymentLink = $this->paymobService->generatePaymentLink($payment, $customer);
        
        if (!$paymentLink) {
            return $this->sendError('فشل في إنشاء رابط الدفع', [], 500);
        }

        return $this->sendResponse([
            'payment_id' => $payment->id,
            'payment_link' => $paymentLink,
            'amount' => $payment->amount,
            'paid_amount' => $payment->paid_amount ?? null,
            'is_partial' => $payment->is_partial,
            'deposit_percentage' => $payment->deposit_percentage ?? null,
            'currency' => 'SAR',
        ], 'تم إنشاء رابط الدفع بنجاح');
    }
    
    /**
     * إنشاء طلب دفع جديد لحجز خدمة منزلية
     */
    public function createHomeServicePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:home_service_bookings,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // التحقق من أن الحجز ينتمي للعميل الحالي
        $booking = HomeServiceBooking::where('id', $request->booking_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$booking) {
            return $this->sendError('الحجز غير موجود أو لا ينتمي لك', [], 404);
        }

        // التحقق من أن الحجز لم يتم دفعه بالفعل
        if ($booking->payment_status === 'paid') {
            return $this->sendError('تم دفع هذا الحجز بالفعل', [], 400);
        }

        // تعديل: إنشاء سجل دفع جديد دائماً لتجنب مشكلة duplicate
        $payment = $this->createPayment($booking->paid_amount, $customer->id);
        $booking->payment_id = $payment->id;
        $booking->save();

        // إنشاء رابط الدفع
        $paymentLink = $this->paymobService->generatePaymentLink($payment, $customer);
        
        if (!$paymentLink) {
            return $this->sendError('فشل في إنشاء رابط الدفع', [], 500);
        }

        return $this->sendResponse([
            'payment_id' => $payment->id,
            'payment_link' => $paymentLink,
            'amount' => $payment->amount,
            'currency' => 'SAR',
        ], 'تم إنشاء رابط الدفع بنجاح');
    }
    
    /**
     * إنشاء طلب دفع جديد لحجز خدمة استوديو
     */
    public function createStudioServicePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:studio_service_bookings,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        // التحقق من أن الحجز ينتمي للعميل الحالي
        $booking = StudioServiceBooking::where('id', $request->booking_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$booking) {
            return $this->sendError('الحجز غير موجود أو لا ينتمي لك', [], 404);
        }

        // التحقق من أن الحجز لم يتم دفعه بالفعل
        if ($booking->payment_status === 'paid') {
            return $this->sendError('تم دفع هذا الحجز بالفعل', [], 400);
        }

        // تعديل: إنشاء سجل دفع جديد دائماً لتجنب مشكلة duplicate
        $payment = $this->createPayment($booking->paid_amount, $customer->id);
        $booking->payment_id = $payment->id;
        $booking->save();

        // إنشاء رابط الدفع
        $paymentLink = $this->paymobService->generatePaymentLink($payment, $customer);
        
        if (!$paymentLink) {
            return $this->sendError('فشل في إنشاء رابط الدفع', [], 500);
        }

        return $this->sendResponse([
            'payment_id' => $payment->id,
            'payment_link' => $paymentLink,
            'amount' => $payment->amount,
            'currency' => 'SAR',
        ], 'تم إنشاء رابط الدفع بنجاح');
    }
    
    /**
     * معالجة استجابة باي موب (Callback)
     */
    public function handlePaymobCallback(Request $request)
    {
        $data = $request->all();
        Log::info('PayMob Callback Received', ['data' => $data, 'method' => $request->method(), 'url' => $request->fullUrl(), 'headers' => $request->header()]);
        
        // إذا كان الطلب فارغًا، قد يكون ناتجًا عن إعادة توجيه المستخدم
        if (empty($data)) {
            Log::warning('PayMob Empty Callback Data - Might be redirect', ['url' => $request->fullUrl()]);
            
            // إذا كان هناك معلمة success في الاستعلام
            if ($request->has('success')) {
                $success = filter_var($request->query('success'), FILTER_VALIDATE_BOOLEAN);
                if ($success) {
                    return view('payment.success', ['message' => 'تم الدفع بنجاح']);
                } else {
                    return view('payment.error', ['message' => 'فشلت عملية الدفع']);
                }
            }
            
            // إذا كان هناك معلمة id في الاستعلام (معرف الدفع)
            if ($request->has('id')) {
                $paymentId = $request->query('id');
                $payment = \App\Models\Payment::find($paymentId);
                
                if ($payment && $payment->status === 'Paid') {
                    return view('payment.success', ['message' => 'تم الدفع بنجاح']);
                } else {
                    return view('payment.error', ['message' => 'فشلت عملية الدفع أو لا تزال معلقة']);
                }
            }
            
            // إذا لم نتمكن من تحديد الحالة، نعرض صفحة نجاح عامة
            return view('payment.success', ['message' => 'تم استلام طلبك، يرجى العودة إلى التطبيق للتحقق من حالة الدفع']);
        }
        
        // معالجة الاستجابة من باي موب
        $result = $this->paymobService->processCallback($data);
        
        if ($result['success']) {
            Log::info('PayMob Callback Processed Successfully', [
                'payment_id' => $result['payment']->id ?? null,
                'status' => $result['payment']->status ?? null
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $result['message']
                ], 200);
            } else {
                return view('payment.success', ['message' => 'تم الدفع بنجاح']);
            }
        } else {
            Log::warning('PayMob Callback Processing Failed', [
                'message' => $result['message'],
                'payment_id' => $result['payment']->id ?? null
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 200); // نعيد 200 حتى لا يعيد باي موب إرسال الاستجابة
            } else {
                return view('payment.error', ['message' => 'فشلت عملية الدفع']);
            }
        }
    }
    
    /**
     * الحصول على حالة الدفع
     */
    public function getPaymentStatus($id)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }
        
        $payment = Payment::where('id', $id)
            ->where('user_id', $customer->id)
            ->first();
        
        if (!$payment) {
            return $this->sendError('الدفعة غير موجودة', [], 404);
        }
        
        // البحث عن الحجوزات المرتبطة
        $homeServiceBookings = HomeServiceBooking::where('payment_id', $payment->id)->get();
        $studioServiceBookings = StudioServiceBooking::where('payment_id', $payment->id)->get();
        
        return $this->sendResponse([
            'payment' => $payment,
            'home_service_bookings' => $homeServiceBookings,
            'studio_service_bookings' => $studioServiceBookings,
        ], 'تم استرجاع حالة الدفع بنجاح');
    }
    
    /**
     * تحديث حالة الدفع يدويًا (للاستخدام في حالات الطوارئ أو لإصلاح المدفوعات العالقة)
     */
    public function checkAndUpdatePaymentStatus($id)
    {
        $payment = Payment::find($id);
        
        if (!$payment) {
            return $this->sendError('الدفعة غير موجودة', [], 404);
        }
        
        // التحقق من وجود معاملات ناجحة
        $successfulTransaction = \App\Models\PaymentTransaction::where('payment_id', $payment->id)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($successfulTransaction) {
            // تحديث حالة الدفع
            if ($payment->is_partial) {
                $payment->status = 'Partially_Paid';
                $payment->paid_amount = $successfulTransaction->amount;
            } else {
                $payment->status = 'Paid';
                $payment->paid_amount = $payment->amount;
            }
            
            $payment->save();
            
            // تحديث حالة الحجوزات المرتبطة
            $this->updateRelatedBookingsStatus($payment);
            
            return $this->sendResponse([
                'payment' => $payment,
                'transaction' => $successfulTransaction,
            ], 'تم تحديث حالة الدفع بنجاح');
        }
        
        return $this->sendError('لم يتم العثور على معاملات ناجحة لهذا الدفع', [], 404);
    }
    
    /**
     * تحديث حالة الحجوزات المرتبطة بالدفع
     */
    private function updateRelatedBookingsStatus($payment)
    {
        // تحديث حجوزات الخدمات المنزلية
        $homeServiceBookings = HomeServiceBooking::where('payment_id', $payment->id)->get();
        foreach ($homeServiceBookings as $booking) {
            if ($payment->is_partial) {
                $booking->payment_status = 'partially_paid';
            } else {
                $booking->payment_status = 'paid';
            }
            
            $booking->booking_status = 'confirmed';
            $booking->paid_amount = $payment->paid_amount;
            $booking->save();
            
            \Illuminate\Support\Facades\Log::info('Home Service Booking Payment Status Updated', [
                'booking_id' => $booking->id,
                'payment_status' => $booking->payment_status,
                'booking_status' => $booking->booking_status,
                'paid_amount' => $booking->paid_amount
            ]);
        }
        
        // تحديث حجوزات خدمات الاستوديو
        $studioServiceBookings = StudioServiceBooking::where('payment_id', $payment->id)->get();
        foreach ($studioServiceBookings as $booking) {
            if ($payment->is_partial) {
                $booking->payment_status = 'partially_paid';
            } else {
                $booking->payment_status = 'paid';
            }
            
            $booking->booking_status = 'confirmed';
            $booking->paid_amount = $payment->paid_amount;
            $booking->save();
            
            \Illuminate\Support\Facades\Log::info('Studio Service Booking Payment Status Updated', [
                'booking_id' => $booking->id,
                'payment_status' => $booking->payment_status,
                'booking_status' => $booking->booking_status,
                'paid_amount' => $booking->paid_amount
            ]);
        }
    }
    
    /**
     * إنشاء سجل دفع جديد
     */
    private function createPayment($amount, $userId)
    {
        return Payment::create([
            'amount' => $amount,
            'status' => 'Pending',
            'user_id' => $userId,
            'payment_method' => 'paymob',
            'payment_data' => json_encode([
                'created_at' => now()->toIso8601String(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);
    }
} 