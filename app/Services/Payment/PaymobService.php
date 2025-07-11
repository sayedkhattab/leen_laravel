<?php

namespace App\Services\Payment;

use App\Models\Customer;
use App\Models\OrderReference;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected $baseUrl;
    protected $apiKey;
    protected $merchantId;
    protected $integrationId;
    protected $iframeId;
    protected $hmacSecret;
    protected $currency;
    
    public function __construct()
    {
        $this->baseUrl = config('services.paymob.base_url');
        $this->apiKey = config('services.paymob.api_key');
        $this->merchantId = config('services.paymob.merchant_id');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
        $this->currency = config('services.paymob.currency', 'SAR');
        
        // Log configuration for debugging
        Log::info('PayMob Configuration Loaded', [
            'base_url' => $this->baseUrl,
            'merchant_id' => $this->merchantId,
            'integration_id' => $this->integrationId,
            'iframe_id' => $this->iframeId,
            'hmac_secret_exists' => !empty($this->hmacSecret),
            'currency' => $this->currency
        ]);
    }
    
    /**
     * الحصول على رمز المصادقة من باي موب
     */
    public function getAuthToken()
    {
        try {
            $response = Http::post("{$this->baseUrl}/auth/tokens", [
                'api_key' => $this->apiKey,
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
     * يمكن الآن تحديد ما إذا كان الدفع كاملًا أو جزئيًا
     */
    public function createOrder($authToken, $payment, $customer)
    {
        try {
            // تحديد المبلغ المطلوب دفعه (إما كامل المبلغ أو العربون)
            $paymentAmount = $payment->is_partial ? $this->calculatePartialAmount($payment) : $payment->amount;
            $amountCents = $paymentAmount * 100; // تحويل إلى سنتات
            
            // التأكد من أن المبلغ أكبر من أو يساوي 10 سنتات
            if ($amountCents < 10) {
                Log::error('PayMob Order Creation Error: Amount too small', [
                    'amount' => $paymentAmount,
                    'amount_cents' => $amountCents
                ]);
                return null;
            }
            
            // إنشاء معرف فريد للطلب باستخدام الوقت الحالي لتجنب التكرار
            $merchantOrderId = $payment->id . '_' . time();
            
            $response = Http::withToken($authToken)
                ->post("{$this->baseUrl}/ecommerce/orders", [
                    'merchant_id' => $this->merchantId,
                    'amount_cents' => $amountCents,
                    'currency' => $this->currency,
                    'delivery_needed' => false,
                    'merchant_order_id' => $merchantOrderId,
                    'items' => [
                        [
                            'name' => $payment->is_partial ? 
                                'Deposit Payment #' . $payment->id : 
                                'Service Payment #' . $payment->id,
                            'amount_cents' => $amountCents,
                            'description' => $payment->is_partial ? 
                                'Deposit payment for service booking' : 
                                'Full payment for service booking',
                            'quantity' => 1
                        ]
                    ],
                ]);
            
            if (!$response->successful()) {
                Log::error('PayMob Order Creation Error', ['response' => $response->json()]);
                return null;
            }
            
            $orderId = $response->json('id');
            
            // حفظ مرجع الطلب في قاعدة البيانات
            \App\Models\OrderReference::create([
                'payment_id' => $payment->id,
                'reference_id' => $orderId
            ]);
            
            // تحديث معرف المرجع في سجل الدفع
            $payment->reference_id = $orderId;
            $payment->save();
            
            Log::info('PayMob Order Created', [
                'payment_id' => $payment->id,
                'order_id' => $orderId,
                'merchant_order_id' => $merchantOrderId,
                'is_partial' => $payment->is_partial,
                'amount' => $paymentAmount
            ]);
            
            return $orderId;
        } catch (\Exception $e) {
            Log::error('PayMob Order Creation Exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return null;
        }
    }
    
    /**
     * حساب مبلغ الدفع الجزئي (العربون) بناءً على النسبة المئوية
     */
    private function calculatePartialAmount($payment)
    {
        if (!$payment->is_partial || !$payment->deposit_percentage) {
            return $payment->amount;
        }
        
        $finalAmount = $payment->getFinalAmountAttribute();
        $partialAmount = $finalAmount * ($payment->deposit_percentage / 100);
        
        // التأكد من أن المبلغ الجزئي لا يقل عن 1 ريال
        return max($partialAmount, 1.0);
    }
    
    /**
     * إنشاء مفتاح دفع
     * يدعم الآن الدفع الجزئي
     */
    public function getPaymentKey($authToken, $orderId, $payment, $customer)
    {
        try {
            // تحديد المبلغ المطلوب دفعه (إما كامل المبلغ أو العربون)
            $paymentAmount = $payment->is_partial ? $this->calculatePartialAmount($payment) : $payment->amount;
            $amountCents = $paymentAmount * 100; // تحويل إلى سنتات
            
            $response = Http::withToken($authToken)
                ->post("{$this->baseUrl}/acceptance/payment_keys", [
                    'amount_cents' => $amountCents,
                    'expiration' => 3600, // صلاحية ساعة واحدة
                    'order_id' => $orderId,
                    'billing_data' => [
                        'apartment' => 'NA',
                        'email' => $customer->email,
                        'floor' => 'NA',
                        'first_name' => $customer->first_name,
                        'last_name' => $customer->last_name,
                        'street' => 'NA',
                        'building' => 'NA',
                        'phone_number' => $customer->phone,
                        'shipping_method' => 'NA',
                        'postal_code' => 'NA',
                        'city' => 'NA',
                        'country' => 'SA',
                        'state' => 'NA',
                    ],
                    'currency' => $this->currency,
                    'integration_id' => $this->integrationId,
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
    
    /**
     * إنشاء رابط دفع
     * يدعم الآن الدفع الجزئي
     */
    public function generatePaymentLink($payment, $customer)
    {
        // الحصول على رمز المصادقة
        $authToken = $this->getAuthToken();
        if (!$authToken) {
            Log::error('PayMob Auth Token Failed', ['payment_id' => $payment->id]);
            return null;
        }
        
        Log::info('PayMob Auth Token Generated', ['payment_id' => $payment->id]);
        
        // إنشاء طلب جديد
        $orderId = $this->createOrder($authToken, $payment, $customer);
        if (!$orderId) {
            Log::error('PayMob Order Creation Failed', [
                'payment_id' => $payment->id,
                'customer_id' => $customer->id,
                'auth_token_exists' => !empty($authToken)
            ]);
            return null;
        }
        
        Log::info('PayMob Order Created', [
            'payment_id' => $payment->id,
            'order_id' => $orderId,
            'is_partial' => $payment->is_partial
        ]);
        
        // الحصول على مفتاح الدفع
        $paymentKey = $this->getPaymentKey($authToken, $orderId, $payment, $customer);
        if (!$paymentKey) {
            Log::error('PayMob Payment Key Failed', [
                'payment_id' => $payment->id,
                'order_id' => $orderId
            ]);
            return null;
        }
        
        Log::info('PayMob Payment Key Generated', [
            'payment_id' => $payment->id,
            'order_id' => $orderId
        ]);
        
        // إنشاء رابط الدفع المباشر وفقًا للمرجع المرفق
        // الصيغة الصحيحة: https://ksa.paymob.com/api/acceptance/iframes/{iframe_id}?payment_token={payment_key}
        $directPaymentUrl = 'https://ksa.paymob.com/api/acceptance/iframes/' . $this->iframeId . '?payment_token=' . $paymentKey;
        
        Log::info('PayMob Payment Link Generated', [
            'payment_id' => $payment->id,
            'payment_link' => $directPaymentUrl,
            'is_partial' => $payment->is_partial
        ]);
        
        return $directPaymentUrl;
    }
    
    /**
     * التحقق من صحة استجابة باي موب
     */
    public function validateCallback($data)
    {
        // إذا كان الطلب فارغًا أو لا يحتوي على بيانات كافية
        if (empty($data)) {
            Log::warning('PayMob Callback empty data', ['data' => $data]);
            
            // في بيئة التطوير، نقبل الطلبات بدون تحقق
            if (app()->environment('local', 'development')) {
                Log::warning('PayMob Callback validation bypassed in development environment');
                return true;
            }
            
            return false;
        }

        // إذا كان هناك obj ولكن لا يوجد hmac في الطلب الرئيسي
        if (isset($data['obj']) && !isset($data['hmac']) && isset($data['obj']['hmac'])) {
            $data['hmac'] = $data['obj']['hmac'];
        }

        // إذا لم يكن هناك hmac، نتحقق من وجود معلمات أخرى للتحقق من صحة الطلب
        if (!isset($data['hmac'])) {
            // التحقق من وجود معلمات أساسية في الطلب
            if (isset($data['success']) || isset($data['order']) || isset($data['id'])) {
                // في بيئة التطوير، نقبل الطلبات بدون hmac
                if (app()->environment('local', 'development')) {
                    Log::warning('PayMob Callback missing HMAC but has other params - validation bypassed in development', ['data' => $data]);
                    return true;
                }
            }
            
            Log::error('PayMob Callback missing HMAC', ['data' => $data]);
            return false;
        }

        // الحصول على سر HMAC من الإعدادات
        $hmacSecret = config('services.paymob.hmac_secret');
        
        if (empty($hmacSecret)) {
            Log::error('PayMob HMAC Secret not configured');
            
            // في بيئة التطوير، نقبل الطلبات بدون تحقق
            if (app()->environment('local', 'development')) {
                Log::warning('PayMob HMAC validation bypassed due to missing secret in development');
                return true;
            }
            
            return false;
        }

        // نسخة من البيانات بدون hmac للتحقق
        $dataToValidate = $data;
        unset($dataToValidate['hmac']);
        
        // ترتيب المفاتيح أبجديًا
        ksort($dataToValidate);
        
        // تحويل البيانات إلى سلسلة نصية
        $concatenatedString = '';
        foreach ($dataToValidate as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue; // تخطي المصفوفات والكائنات
            }
            $concatenatedString .= $key . '=' . $value;
        }
        
        // إنشاء توقيع HMAC
        $calculatedHmac = hash_hmac('sha512', $concatenatedString, $hmacSecret);
        
        // مقارنة التوقيع المحسوب مع التوقيع المستلم
        if ($calculatedHmac === $data['hmac']) {
            Log::info('PayMob Callback HMAC validation successful');
            return true;
        }
        
        // في حالة عدم تطابق التوقيع، نسجل الخطأ
        Log::error('PayMob Callback HMAC validation failed', [
            'received_hmac' => $data['hmac'],
            'calculated_hmac' => $calculatedHmac
        ]);
        
        // في بيئة التطوير، نقبل الطلبات حتى لو فشل التحقق
        if (app()->environment('local', 'development')) {
            Log::warning('PayMob HMAC validation failed but bypassed in development');
            return true;
        }
        
        return false;
    }
    
    /**
     * معالجة استجابة باي موب (Callback)
     * تم تحسينها للتعامل مع مختلف أنواع الاستجابات
     */
    public function processCallback($data)
    {
        Log::info('Processing PayMob Callback', ['data_keys' => array_keys($data)]);
        
        // إذا كان هناك obj في البيانات، نستخدمه
        if (isset($data['obj'])) {
            $payload = $data['obj'];
        } else {
            $payload = $data;
        }
        
        // استخراج معرف الطلب
        $orderId = $payload['order']['id'] ?? $payload['order_id'] ?? null;
        
        if (!$orderId) {
            Log::error('PayMob Callback: Missing order ID', ['payload' => $payload]);
            return [
                'success' => false,
                'message' => 'Missing order ID in payload'
            ];
        }
        
        // البحث عن مرجع الطلب في قاعدة البيانات
        $orderReference = \App\Models\OrderReference::where('reference_id', $orderId)->first();
        
        if (!$orderReference) {
            Log::error('PayMob Callback: Order reference not found', ['order_id' => $orderId]);
            return [
                'success' => false,
                'message' => 'Order reference not found'
            ];
        }
        
        // البحث عن سجل الدفع المرتبط
        $payment = \App\Models\Payment::find($orderReference->payment_id);
        
        if (!$payment) {
            Log::error('PayMob Callback: Payment not found', ['payment_id' => $orderReference->payment_id]);
            return [
                'success' => false,
                'message' => 'Payment not found'
            ];
        }
        
        // معالجة استجابة الدفع
        return $this->processPaymentResponse($payload, $payment);
    }
    
    /**
     * معالجة استجابة الدفع من باي موب
     * تم تحديثها لدعم الدفع الجزئي وتحسين التعامل مع الأخطاء
     */
    public function processPaymentResponse($payload, $payment)
    {
        if (!$payment) {
            Log::error('PayMob Payment not found for processing response');
            return [
                'success' => false,
                'message' => 'Payment not found'
            ];
        }
        
        // تسجيل المعاملة في جدول المعاملات
        $transactionData = [
            'payment_id' => $payment->id,
            'transaction_id' => $payload['id'] ?? $payload['transaction_id'] ?? null,
            'type' => $payload['source_data']['type'] ?? $payload['source_data_type'] ?? 'card',
            'amount' => ($payload['amount_cents'] ?? 0) / 100, // تحويل من سنتات إلى وحدة العملة
            'status' => $this->determineTransactionStatus($payload),
            'transaction_data' => json_encode($payload),
        ];
        
        // تسجيل المعاملة
        $transaction = \App\Models\PaymentTransaction::create($transactionData);
        Log::info('PayMob Transaction Recorded', [
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'amount' => $transaction->amount
        ]);
        
        // تحديث بيانات الدفع
        $paymentData = $payment->payment_data ?? [];
        $paymentData['transaction_data'] = $payload;
        $payment->payment_data = $paymentData;
        $payment->transaction_id = $payload['id'] ?? $payload['transaction_id'] ?? null;
        
        if ($transaction->status === 'success') {
            // الدفع ناجح
            
            // تحديث المبلغ المدفوع
            $paidAmount = ($payload['amount_cents'] ?? 0) / 100;
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
            $this->updateBookingStatus($payment);
            
            return [
                'success' => true,
                'message' => $payment->is_partial ? 'Deposit payment successful' : 'Payment successful',
                'payment' => $payment
            ];
        } else {
            // الدفع فشل
            $payment->status = 'Failed';
            $payment->save();
            
            Log::info('PayMob Payment Marked as Failed', [
                'payment_id' => $payment->id
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment failed',
                'payment' => $payment
            ];
        }
    }
    
    /**
     * تحديد حالة المعاملة بناءً على البيانات المستلمة من باي موب
     */
    private function determineTransactionStatus($payload)
    {
        // التحقق من وجود مؤشر نجاح صريح
        if (isset($payload['success'])) {
            return ($payload['success'] === true || $payload['success'] === 'true') ? 'success' : 'failed';
        }
        
        // التحقق من حالة المعاملة
        if (isset($payload['is_refunded']) && $payload['is_refunded'] === true) {
            return 'refunded';
        }
        
        if (isset($payload['is_voided']) && $payload['is_voided'] === true) {
            return 'voided';
        }
        
        if (isset($payload['pending']) && $payload['pending'] === true) {
            return 'pending';
        }
        
        // التحقق من وجود مؤشرات نجاح أخرى
        if (isset($payload['txn_response_code']) && $payload['txn_response_code'] === '0') {
            return 'success';
        }
        
        if (isset($payload['data_message']) && $payload['data_message'] === 'Approved') {
            return 'success';
        }
        
        // في حالة عدم التأكد، نعتبر المعاملة ناجحة إذا كان هناك معرف معاملة
        if (isset($payload['id']) || isset($payload['transaction_id'])) {
            return 'success';
        }
        
        // افتراضيًا، نعتبر المعاملة فاشلة
        return 'failed';
    }
    
    /**
     * تحديث حالة الحجوزات المرتبطة بالدفع
     */
    private function updateBookingStatus($payment)
    {
        // تحديث حجوزات الخدمات المنزلية
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
                'paid_amount' => $booking->paid_amount
            ]);
        }
        
        // تحديث حجوزات خدمات الاستوديو
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
                'paid_amount' => $booking->paid_amount
            ]);
        }
    }
} 