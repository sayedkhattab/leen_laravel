<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Services\ForJawalyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
{
    protected $forJawalyService;
    protected $testNumbers = ['01121926996', '01092841138', '01094963620']; // أرقام الاختبار
    protected $otpExpiryMinutes = 5; // مدة صلاحية رمز التحقق بالدقائق

    public function __construct(ForJawalyService $forJawalyService)
    {
        $this->forJawalyService = $forJawalyService;
    }

    /**
     * إرسال رمز التحقق OTP
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'type' => 'sometimes|string|in:registration,login,reset_password',
            'action' => 'sometimes|string|in:registration,login,reset_password',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $phone = $request->phone;
        $type = $request->action ?? $request->type ?? 'registration';

        // التعامل مع أرقام الاختبار
        if (in_array($phone, $this->testNumbers)) {
            return $this->successResponse('OTP sent successfully', [
                'phone' => $phone,
                'test_mode' => true,
                'test_otp' => '123456'
            ]);
        }

        // إنشاء رمز تحقق عشوائي من 6 أرقام
        $verificationCode = rand(100000, 999999);
        $expiryTime = Carbon::now()->addMinutes($this->otpExpiryMinutes);

        // تخزين رمز التحقق في قاعدة البيانات
        PhoneVerification::updateOrCreate(
            ['phone' => $phone, 'type' => $type],
            [
                'verification_code' => $verificationCode,
                'expires_at' => $expiryTime,
                'attempts' => 0,
                'verified' => false
            ]
        );

        // تسجيل رمز التحقق في ملف Laravel.log بتنسيق واضح للعثور عليه بسهولة
        Log::info("======= OTP CODE =======");
        Log::info("Phone: {$phone}");
        Log::info("OTP Code: {$verificationCode}");
        Log::info("Type/Action: {$type}");
        Log::info("========================");

        Log::info("Sending OTP to {$phone} with code {$verificationCode} for {$type}");

        try {
            // تحديد نص الرسالة بناءً على نوع التحقق
            $message = $this->getOtpMessage($verificationCode, $type);
            
            // إرسال الرسالة عبر خدمة فورجوالي
            $result = $this->forJawalyService->sendSMS($phone, $message, 'otp');

            if (isset($result['code']) && $result['code'] === 200) {
                return $this->successResponse('OTP sent successfully', ['phone' => $phone]);
            } else {
                Log::error('Error sending OTP', ['response' => $result]);
                return $this->errorResponse($result['message'] ?? 'Error sending OTP', 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception sending OTP: ' . $e->getMessage());
            return $this->errorResponse('Failed to send OTP. Please try again later.', 500);
        }
    }

    /**
     * التحقق من رمز OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
            'type' => 'sometimes|string|in:registration,login,reset_password',
            'action' => 'sometimes|string|in:registration,login,reset_password',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $phone = $request->phone;
        $otp = $request->otp;
        $type = $request->action ?? $request->type ?? 'registration';

        // التعامل مع أرقام الاختبار
        if (in_array($phone, $this->testNumbers)) {
            if ($otp === '123456') {
                return $this->successResponse('Phone number verified successfully', [
                    'phone' => $phone,
                    'verified' => true
                ]);
            }
            return $this->errorResponse('Invalid OTP. Please try again.', 400);
        }

        // البحث عن سجل التحقق في قاعدة البيانات
        $verification = PhoneVerification::where('phone', $phone)
            ->where('type', $type)
            ->first();

        if (!$verification) {
            return $this->errorResponse('OTP not found or expired. Please request a new one.', 400);
        }

        // زيادة عداد المحاولات
        $attempts = $verification->incrementAttempts();

        // التحقق من انتهاء صلاحية الرمز
        if ($verification->isExpired()) {
            return $this->errorResponse('OTP has expired. Please request a new one.', 400);
        }

        // التحقق من صحة الرمز
        if ($verification->verification_code != $otp) {
            // حظر بعد عدة محاولات فاشلة
            if ($attempts >= 5) {
                $verification->delete();
                return $this->errorResponse('Too many failed attempts. Please request a new OTP.', 429);
            }
            return $this->errorResponse('Invalid OTP. Please try again.', 400);
        }

        // تحديث حالة التحقق
        $verification->markAsVerified();

        // تخزين حالة التحقق في الكاش للوصول السريع
        $verificationKey = $this->getVerificationCacheKey($phone, $type);
        Cache::put($verificationKey, true, now()->addMinutes(30));

        return $this->successResponse('Phone number verified successfully', [
            'phone' => $phone,
            'verified' => true
        ]);
    }

    /**
     * التحقق من حالة التحقق
     */
    public function checkVerificationStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'type' => 'sometimes|string|in:registration,login,reset_password',
            'action' => 'sometimes|string|in:registration,login,reset_password',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $phone = $request->phone;
        $type = $request->action ?? $request->type ?? 'registration';
        
        // أولاً نتحقق من الكاش للأداء السريع
        $verificationKey = $this->getVerificationCacheKey($phone, $type);
        $isVerified = Cache::get($verificationKey);
        
        // إذا لم يكن موجودًا في الكاش، نتحقق من قاعدة البيانات
        if ($isVerified === null) {
            $verification = PhoneVerification::where('phone', $phone)
                ->where('type', $type)
                ->where('verified', true)
                ->first();
                
            $isVerified = $verification !== null;
            
            // تخزين النتيجة في الكاش للاستخدام المستقبلي
            if ($isVerified) {
                Cache::put($verificationKey, true, now()->addMinutes(30));
            }
        }

        return $this->successResponse('Verification status retrieved', [
            'phone' => $phone,
            'verified' => $isVerified
        ]);
    }

    /**
     * إنشاء مفتاح تخزين حالة التحقق في الكاش
     */
    protected function getVerificationCacheKey($phone, $type): string
    {
        return "verified:{$type}:{$phone}";
    }

    /**
     * إنشاء نص رسالة OTP بناءً على نوع التحقق
     */
    protected function getOtpMessage($code, $type): string
    {
        $appName = config('app.name', 'لين');
        
        switch ($type) {
            case 'registration':
                return "كود التحقق الخاص بك في تطبيق {$appName} هو: {$code}";
            case 'login':
                return "كود التحقق الخاص بك في تطبيق {$appName} هو: {$code}";
            case 'reset_password':
                return "كود التحقق الخاص بك في تطبيق {$appName} هو: {$code}";
            default:
                return "كود التحقق الخاص بك في تطبيق {$appName} هو: {$code}";
        }
    }

    /**
     * إرجاع استجابة نجاح موحدة
     */
    protected function successResponse(string $message, array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json(array_merge([
            'status' => 'success',
            'message' => $message,
        ], $data));
    }

    /**
     * إرجاع استجابة خطأ موحدة
     */
    protected function errorResponse(string $message, int $status = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    /**
     * إرجاع استجابة خطأ تحقق موحدة
     */
    protected function validationErrorResponse($errors): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'errors' => $errors,
        ], 422);
    }
} 