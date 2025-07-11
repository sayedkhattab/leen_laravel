<?php

namespace App\Http\Controllers\API\Customers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\StudioServiceBooking;
use App\Models\HomeServiceBooking;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    protected $paymobService;
    
    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }
    
    /**
     * الحصول على معلومات الدفع بواسطة المعرف
     */
    public function getPaymentById($id)
    {
        $payment = Payment::find($id);
        
        if (!$payment) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Payment not found'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success', 
            'data' => $payment
        ], 200);
    }
    
    /**
     * إنشاء طلب دفع جديد
     */
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'description' => 'sometimes|string',
            'is_partial' => 'sometimes|boolean',
            'deposit_percentage' => 'required_if:is_partial,true|numeric|min:1|max:99',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // إنشاء سجل دفع جديد
        $payment = Payment::create([
            'amount' => $request->amount,
            'status' => 'Pending',
            'user_id' => $request->user()->id,
            'is_partial' => $request->is_partial ?? false,
            'deposit_percentage' => $request->is_partial ? $request->deposit_percentage : null,
            'payment_data' => [
                'description' => $request->description ?? 'Payment for services',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);
        
        // إنشاء رابط الدفع
        $paymentLink = $this->paymobService->generatePaymentLink($payment, $request->user());
        
        if (!$paymentLink) {
            $payment->update(['status' => 'Failed']);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate payment link'
            ], 500);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Payment link generated successfully',
            'data' => [
                'payment_id' => $payment->id,
                'payment_link' => $paymentLink,
                'amount' => $payment->amount,
                'is_partial' => $payment->is_partial,
                'deposit_percentage' => $payment->deposit_percentage,
                'payment_amount' => $payment->is_partial ? 
                    $payment->amount * ($payment->deposit_percentage / 100) : 
                    $payment->amount,
                'currency' => config('services.paymob.currency', 'SAR'),
            ]
        ]);
    }
    
    /**
     * معالجة استجابة باي موب (Callback)
     */
    public function handlePaymobCallback(Request $request)
    {
        $data = $request->all();
        Log::info('PayMob Customer Callback Received', ['data' => $data, 'method' => $request->method(), 'url' => $request->fullUrl()]);
        
        // التحقق من صحة الاستجابة
        if (!$this->paymobService->validateCallback($data)) {
            Log::error('PayMob Invalid Callback HMAC', ['data' => $data]);
            return view('payment.error', [
                'message' => 'Invalid payment verification',
            ]);
        }
        
        // استخدام حمولة obj إن وجدت
        $payload = isset($data['obj']) && is_array($data['obj']) ? $data['obj'] : $data;
        
        // استخراج معرف الطلب
        $orderId = null;
        if (isset($payload['order']['id'])) {
            $orderId = $payload['order']['id'];
        } elseif (isset($payload['order'])) {
            $orderId = $payload['order'];
        }
        
        if (!$orderId) {
            Log::error('PayMob Callback - Missing Order ID', ['data' => $data]);
            return view('payment.error', [
                'message' => 'Missing order information',
            ]);
        }
        
        // البحث عن مرجع الطلب
        $orderReference = \App\Models\OrderReference::where('reference_id', $orderId)->first();
        if (!$orderReference) {
            Log::error('PayMob Callback - Order Reference Not Found', ['order_id' => $orderId]);
            return view('payment.error', [
                'message' => 'Order reference not found',
            ]);
        }
        
        // الحصول على الدفعة
        $payment = \App\Models\Payment::find($orderReference->payment_id);
        if (!$payment) {
            Log::error('PayMob Callback - Payment Not Found', ['order_reference_id' => $orderReference->id]);
            return view('payment.error', [
                'message' => 'Payment not found',
            ]);
        }
        
        // تسجيل المعاملة في جدول المعاملات
        $paidAmount = ($payload['amount_cents'] ?? 0) / 100; // تحويل من سنتات إلى وحدة العملة
        $transactionData = [
            'payment_id' => $payment->id,
            'transaction_id' => $payload['id'] ?? null,
            'type' => $payload['source_data']['type'] ?? 'card',
            'amount' => $paidAmount,
            'status' => $payload['success'] === true || $payload['success'] === 'true' ? 'success' : 'failed',
            'transaction_data' => json_encode($payload),
        ];
        
        // تسجيل المعاملة
        $transaction = \App\Models\PaymentTransaction::create($transactionData);
        Log::info('PayMob Transaction Recorded', ['transaction_id' => $transaction->id]);
        
        // تحديث بيانات الدفع
        $paymentData = $payment->payment_data ?? [];
        $paymentData['transaction_data'] = $data;
        $payment->payment_data = $paymentData;
        $payment->transaction_id = $payload['id'] ?? null;
        
        if ($payload['success'] === true || $payload['success'] === 'true') {
            // الدفع ناجح
            
            // تحديث المبلغ المدفوع
            $payment->paid_amount = $paidAmount;
            
            // تحديث حالة الدفع
            if ($payment->is_partial) {
                $payment->status = 'Partially_Paid';
            } else {
                $payment->status = 'Paid';
            }
            
            $payment->save();
            
            Log::info('PayMob Payment Updated', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'is_partial' => $payment->is_partial,
                'paid_amount' => $payment->paid_amount,
                'status' => $payment->status
            ]);
            
            // تحديث حالة الحجوزات المرتبطة
            $homeServiceBookings = \App\Models\HomeServiceBooking::where('payment_id', $payment->id)->get();
            foreach ($homeServiceBookings as $booking) {
                if ($payment->is_partial) {
                    $booking->payment_status = 'partially_paid';
                } else {
                    $booking->payment_status = 'paid';
                }
                
                $booking->booking_status = 'confirmed';
                $booking->paid_amount = $payment->paid_amount;
                $booking->save();
                
                Log::info('Home Service Booking Payment Status Updated', [
                    'booking_id' => $booking->id,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status,
                    'paid_amount' => $booking->paid_amount,
                    'is_partial' => $payment->is_partial
                ]);
            }
            
            $studioServiceBookings = \App\Models\StudioServiceBooking::where('payment_id', $payment->id)->get();
            foreach ($studioServiceBookings as $booking) {
                if ($payment->is_partial) {
                    $booking->payment_status = 'partially_paid';
                } else {
                    $booking->payment_status = 'paid';
                }
                
                $booking->booking_status = 'confirmed';
                $booking->paid_amount = $payment->paid_amount;
                $booking->save();
                
                Log::info('Studio Service Booking Payment Status Updated', [
                    'booking_id' => $booking->id,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status,
                    'paid_amount' => $booking->paid_amount,
                    'is_partial' => $payment->is_partial
                ]);
            }
            
            return view('payment.success', [
                'payment' => $payment,
                'is_partial' => $payment->is_partial,
            ]);
        } else {
            // الدفع فشل
            $payment->status = 'Failed';
            $payment->save();
            
            Log::info('PayMob Payment Marked as Failed', [
                'payment_id' => $payment->id
            ]);
            
            // إنشاء رابط دفع جديد للمحاولة مرة أخرى
            $user = $payment->user;
            $paymentLink = $this->paymobService->generatePaymentLink($payment, $user);
            
            return view('payment.error', [
                'message' => 'Payment failed',
                'payment_id' => $payment->id,
                'payment_link' => $paymentLink,
            ]);
        }
    }

    public function createStudioServicePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:studio_service_bookings,id',
            'is_partial' => 'sometimes|boolean',
            'deposit_percentage' => 'required_if:is_partial,true|numeric|min:1|max:99',
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

        // تحديد ما إذا كان الدفع جزئي أم كامل
        $isPartial = $request->is_partial ?? false;
        $depositPercentage = $isPartial ? $request->deposit_percentage : null;

        // إنشاء سجل دفع جديد دائماً لتجنب مشكلة duplicate
        $payment = Payment::create([
            'amount' => $booking->paid_amount,
            'status' => 'Pending',
            'user_id' => $customer->id,
            'is_partial' => $isPartial,
            'deposit_percentage' => $depositPercentage,
            'payment_method' => 'paymob',
            'payment_data' => [
                'description' => $isPartial ? 
                    'دفع عربون لحجز خدمة استوديو #' . $booking->id : 
                    'دفع حجز خدمة استوديو #' . $booking->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        // ربط الدفع بالحجز
        $booking->payment_id = $payment->id;
        $booking->save();

        // إنشاء رابط الدفع
        $paymentLink = $this->paymobService->generatePaymentLink($payment, $customer);
        
        if (!$paymentLink) {
            return $this->sendError('فشل في إنشاء رابط الدفع', [], 500);
        }

        // حساب المبلغ المطلوب دفعه (إما كامل المبلغ أو العربون)
        $paymentAmount = $isPartial ? 
            $payment->amount * ($depositPercentage / 100) : 
            $payment->amount;

        // إضافة تعليمات إضافية في بيئة التطوير
        $devInstructions = '';
        if (app()->environment('local', 'development')) {
            $devInstructions = "\n\nملاحظة للتطوير: بعد إكمال الدفع أو إغلاق صفحة الدفع، قم باستدعاء API التحقق من حالة الدفع (/api/v1/customer/payments/{$payment->id}) للموافقة التلقائية على الدفع في بيئة التطوير.";
        }

        return $this->sendResponse([
            'payment_id' => $payment->id,
            'payment_link' => $paymentLink,
            'amount' => $payment->amount,
            'is_partial' => $isPartial,
            'deposit_percentage' => $depositPercentage,
            'payment_amount' => $paymentAmount,
            'currency' => config('services.paymob.currency', 'SAR'),
            'instructions' => [
                'ar' => ($isPartial ? 'سيتم دفع عربون بقيمة ' . number_format($paymentAmount, 2) . ' ريال. ' : '') .
                      'يجب فتح هذا الرابط في متصفح WebView داخل التطبيق. استخدم WebView مع تفعيل JavaScript وتأكد من السماح بالـ cookies. بعد إتمام الدفع، سيتم إعادة توجيهك إلى صفحة النجاح أو الفشل.' . $devInstructions,
                'en' => ($isPartial ? 'You will pay a deposit of SAR ' . number_format($paymentAmount, 2) . '. ' : '') .
                      'This link must be opened in a WebView browser within the app. Use WebView with JavaScript enabled and make sure cookies are allowed. After completing the payment, you will be redirected to a success or failure page.'
            ],
            'iframe_mode' => true,
            'auto_approval_enabled' => app()->environment('local', 'development'),
            'auto_approval_endpoint' => app()->environment('local', 'development') ? "/api/v1/customer/payments/{$payment->id}" : null
        ], 'تم إنشاء رابط الدفع بنجاح');
    }

    /**
     * إنشاء رابط دفع مباشر (بدون iframe)
     */
    private function createDirectPaymentLink($payment, $customer)
    {
        // الحصول على رمز المصادقة
        $authToken = $this->getAuthToken();
        if (!$authToken) {
            return null;
        }

        // إنشاء طلب جديد
        $orderId = $this->createOrder($authToken, $payment, $customer);
        if (!$orderId) {
            return null;
        }

        // إنشاء مفتاح دفع
        $paymentKey = $this->createPaymentKey($authToken, $orderId, $payment, $customer);
        if (!$paymentKey) {
            return null;
        }

        // إنشاء رابط الدفع المباشر
        $baseUrl = config('services.paymob.base_url');
        $directPaymentUrl = str_replace('/api', '', $baseUrl) . "/acceptance/form/" . $paymentKey;
        
        Log::info('Direct Payment Link Created', [
            'payment_id' => $payment->id,
            'payment_link' => $directPaymentUrl
        ]);
        
        return $directPaymentUrl;
    }

    /**
     * الحصول على رمز المصادقة من باي موب
     */
    private function getAuthToken()
    {
        $apiKey = config('services.paymob.api_key');
        $baseUrl = config('services.paymob.base_url');
        
        try {
            $response = Http::post("{$baseUrl}/auth/tokens", [
                'api_key' => $apiKey,
            ]);
            
            if (!$response->successful()) {
                Log::error('PayMob Auth Error', ['response' => $response->json()]);
                return null;
            }
            
            return $response->json('token');
        } catch (\Exception $e) {
            Log::error('PayMob Auth Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * إنشاء طلب في نظام باي موب
     */
    private function createOrder($authToken, $payment, $customer)
    {
        $baseUrl = config('services.paymob.base_url');
        $merchantId = config('services.paymob.merchant_id');
        $currency = config('services.paymob.currency', 'SAR');
        
        try {
            $amountCents = $payment->amount * 100; // تحويل إلى سنتات
            
            // التأكد من أن المبلغ أكبر من أو يساوي 10 سنتات
            if ($amountCents < 10) {
                Log::error('PayMob Order Creation Error: Amount too small', [
                    'amount' => $payment->amount,
                    'amount_cents' => $amountCents
                ]);
                return null;
            }
            
            $response = Http::withToken($authToken)
                ->post("{$baseUrl}/ecommerce/orders", [
                    'merchant_id' => $merchantId,
                    'amount_cents' => $amountCents,
                    'currency' => $currency,
                    'delivery_needed' => false,
                    'merchant_order_id' => $payment->id . '_' . time(), // إضافة طابع زمني لمنع التكرار
                    'items' => [
                        [
                            'name' => 'Service Booking #' . $payment->id,
                            'amount_cents' => $amountCents,
                            'description' => 'Payment for service booking',
                            'quantity' => 1
                        ]
                    ],
                    'shipping_data' => [
                        'first_name' => $customer->first_name,
                        'last_name' => $customer->last_name ?? '',
                        'email' => $customer->email ?? 'customer@example.com',
                        'phone_number' => $customer->phone,
                        'street' => 'NA',
                        'building' => 'NA',
                        'floor' => 'NA',
                        'city' => 'NA',
                        'country' => 'SA',
                    ],
                ]);
            
            if (!$response->successful()) {
                Log::error('PayMob Order Creation Error', ['response' => $response->json()]);
                return null;
            }
            
            // حفظ معرف الطلب في جدول الدفع
            $orderId = $response->json('id');
            if ($orderId) {
                $payment->update(['reference_id' => $orderId]);
            }
            
            return $orderId;
        } catch (\Exception $e) {
            Log::error('PayMob Order Creation Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * إنشاء مفتاح دفع
     */
    private function createPaymentKey($authToken, $orderId, $payment, $customer)
    {
        $baseUrl = config('services.paymob.base_url');
        $integrationId = config('services.paymob.integration_id');
        $currency = config('services.paymob.currency', 'SAR');
        
        try {
            $amountCents = $payment->amount * 100; // تحويل إلى سنتات
            
            $response = Http::withToken($authToken)
                ->post("{$baseUrl}/acceptance/payment_keys", [
                    'amount_cents' => $amountCents,
                    'expiration' => 3600, // صلاحية ساعة واحدة
                    'order_id' => $orderId,
                    'billing_data' => [
                        'apartment' => 'NA',
                        'email' => $customer->email ?? 'customer@example.com',
                        'floor' => 'NA',
                        'first_name' => $customer->first_name,
                        'last_name' => $customer->last_name ?? '',
                        'street' => 'NA',
                        'building' => 'NA',
                        'phone_number' => $customer->phone,
                        'shipping_method' => 'NA',
                        'postal_code' => 'NA',
                        'city' => 'NA',
                        'country' => 'SA',
                        'state' => 'NA',
                    ],
                    'currency' => $currency,
                    'integration_id' => $integrationId,
                    'lock_order_when_paid' => true
                ]);
            
            if (!$response->successful()) {
                Log::error('PayMob Payment Key Error', ['response' => $response->json()]);
                return null;
            }
            
            return $response->json('token');
        } catch (\Exception $e) {
            Log::error('PayMob Payment Key Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function createHomeServicePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:home_service_bookings,id',
            'is_partial' => 'sometimes|boolean',
            'deposit_percentage' => 'required_if:is_partial,true|numeric|min:1|max:99',
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

        // تحديد ما إذا كان الدفع جزئي أم كامل
        $isPartial = $request->is_partial ?? false;
        $depositPercentage = $isPartial ? $request->deposit_percentage : null;

        // إنشاء سجل دفع جديد دائماً لتجنب مشكلة duplicate
        $payment = Payment::create([
            'amount' => $booking->paid_amount,
            'status' => 'Pending',
            'user_id' => $customer->id,
            'is_partial' => $isPartial,
            'deposit_percentage' => $depositPercentage,
            'payment_method' => 'paymob',
            'payment_data' => [
                'description' => $isPartial ? 
                    'دفع عربون لحجز خدمة منزلية #' . $booking->id : 
                    'دفع حجز خدمة منزلية #' . $booking->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        // ربط الدفع بالحجز
        $booking->payment_id = $payment->id;
        $booking->save();

        // إنشاء رابط الدفع
        $paymentLink = $this->paymobService->generatePaymentLink($payment, $customer);
        
        if (!$paymentLink) {
            return $this->sendError('فشل في إنشاء رابط الدفع', [], 500);
        }

        // حساب المبلغ المطلوب دفعه (إما كامل المبلغ أو العربون)
        $paymentAmount = $isPartial ? 
            $payment->amount * ($depositPercentage / 100) : 
            $payment->amount;

        // إضافة تعليمات إضافية في بيئة التطوير
        $devInstructions = '';
        if (app()->environment('local', 'development')) {
            $devInstructions = "\n\nملاحظة للتطوير: بعد إكمال الدفع أو إغلاق صفحة الدفع، قم باستدعاء API التحقق من حالة الدفع (/api/v1/customer/payments/{$payment->id}) للموافقة التلقائية على الدفع في بيئة التطوير.";
        }

        return $this->sendResponse([
            'payment_id' => $payment->id,
            'payment_link' => $paymentLink,
            'amount' => $payment->amount,
            'is_partial' => $isPartial,
            'deposit_percentage' => $depositPercentage,
            'payment_amount' => $paymentAmount,
            'currency' => config('services.paymob.currency', 'SAR'),
            'instructions' => [
                'ar' => ($isPartial ? 'سيتم دفع عربون بقيمة ' . number_format($paymentAmount, 2) . ' ريال. ' : '') .
                      'يجب فتح هذا الرابط في متصفح WebView داخل التطبيق. استخدم WebView مع تفعيل JavaScript وتأكد من السماح بالـ cookies. بعد إتمام الدفع، سيتم إعادة توجيهك إلى صفحة النجاح أو الفشل.' . $devInstructions,
                'en' => ($isPartial ? 'You will pay a deposit of SAR ' . number_format($paymentAmount, 2) . '. ' : '') .
                      'This link must be opened in a WebView browser within the app. Use WebView with JavaScript enabled and make sure cookies are allowed. After completing the payment, you will be redirected to a success or failure page.'
            ],
            'iframe_mode' => true,
            'auto_approval_enabled' => app()->environment('local', 'development'),
            'auto_approval_endpoint' => app()->environment('local', 'development') ? "/api/v1/customer/payments/{$payment->id}" : null
        ], 'تم إنشاء رابط الدفع بنجاح');
    }

    /**
     * الحصول على كائن العميل الحالي
     */
    protected function getCurrentCustomer()
    {
        $user = auth()->user();
        
        // إذا كان المستخدم هو عميل مباشرة
        if ($user instanceof \App\Models\Customer) {
            return $user;
        }
        
        // إذا كان المستخدم له علاقة عميل
        if ($user && method_exists($user, 'customer') && $user->customer) {
            return $user->customer;
        }
        
        return null;
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

    /**
     * إرجاع استجابة نجاح
     */
    protected function sendResponse($data, $message = 'تمت العملية بنجاح')
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * إرجاع استجابة خطأ
     */
    protected function sendError($message, $errors = [], $code = 400)
    {
        $response = [
            'status' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    public function getPaymentStatus(Request $request, $id)
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
        
        // البحث عن المعاملات المرتبطة
        $transactions = \App\Models\PaymentTransaction::where('payment_id', $payment->id)->get();
        
        // البحث عن الحجوزات المرتبطة
        $studioServiceBookings = \App\Models\StudioServiceBooking::where('payment_id', $payment->id)->get();
        $homeServiceBookings = \App\Models\HomeServiceBooking::where('payment_id', $payment->id)->get();
        
        // حل مؤقت: إذا كانت حالة الدفع معلقة وتم استدعاء هذا API، نفترض أن الدفع تم بنجاح
        // هذا مفيد في بيئة التطوير حيث لا يمكن استقبال الكولباك من باي موب
        if (app()->environment('local', 'development') && $payment->status == 'Pending') {
            // تحويل حالة الدفع إلى "مدفوع"
            $payment->status = 'paid';
            $payment->save();
            
            // تسجيل معاملة ناجحة
            if ($transactions->isEmpty()) {
                $transaction = new \App\Models\PaymentTransaction();
                $transaction->payment_id = $payment->id;
                $transaction->transaction_id = 'auto_' . time();
                $transaction->amount = $payment->amount * 100;
                $transaction->currency = config('services.paymob.currency', 'SAR');
                $transaction->status = 'success';
                $transaction->gateway_response = json_encode([
                    'auto_approved' => true,
                    'timestamp' => now()->toIso8601String(),
                    'environment' => app()->environment()
                ]);
                $transaction->save();
                
                Log::info('Auto-approved payment transaction created', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $transaction->id
                ]);
            }
            
            // تحديث حالة الحجوزات المرتبطة
            foreach ($studioServiceBookings as $booking) {
                $booking->payment_status = 'paid';
                $booking->booking_status = 'confirmed';
                $booking->save();
                
                Log::info('Studio Service Booking Payment Status Auto-Updated', [
                    'booking_id' => $booking->id,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status
                ]);
            }
            
            foreach ($homeServiceBookings as $booking) {
                $booking->payment_status = 'paid';
                $booking->booking_status = 'confirmed';
                $booking->save();
                
                Log::info('Home Service Booking Payment Status Auto-Updated', [
                    'booking_id' => $booking->id,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status
                ]);
            }
            
            Log::info('Payment Status Auto-Updated', [
                'payment_id' => $payment->id,
                'status' => $payment->status
            ]);
        }
        
        return $this->sendResponse([
            'payment' => $payment,
            'transactions' => $transactions,
            'studio_service_bookings' => $studioServiceBookings,
            'home_service_bookings' => $homeServiceBookings,
            'auto_approval_enabled' => app()->environment('local', 'development')
        ], 'تم جلب حالة الدفع بنجاح');
    }
} 