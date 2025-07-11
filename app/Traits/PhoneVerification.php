<?php

namespace App\Traits;

use App\Services\ForJawalyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait PhoneVerification
{
    /**
     * تحديث حالة التحقق من الهاتف
     */
    public function markPhoneAsVerified()
    {
        $this->phone_verified_at = Carbon::now();
        $this->save();
        
        // حذف حالة التحقق من الكاش
        $this->clearPhoneVerificationCache();
        
        return $this;
    }

    /**
     * التحقق مما إذا كان الهاتف مُتحقق منه
     */
    public function hasVerifiedPhone()
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * إرسال إشعار تحقق من الهاتف
     */
    public function sendPhoneVerificationNotification()
    {
        $service = app(ForJawalyService::class);
        $code = rand(100000, 999999);
        
        // تخزين الرمز في الكاش
        $expiryTime = Carbon::now()->addMinutes(5);
        Cache::put($this->getPhoneVerificationCacheKey(), [
            'code' => $code,
            'expires_at' => $expiryTime,
            'attempts' => 0
        ], $expiryTime);
        
        // إرسال الرسالة
        $message = "رمز التحقق الخاص بك هو: {$code}";
        return $service->sendSMS($this->phone, $message);
    }

    /**
     * التحقق من رمز التحقق
     */
    public function verifyPhoneCode($code)
    {
        $cachedData = Cache::get($this->getPhoneVerificationCacheKey());
        
        if (!$cachedData) {
            return false;
        }
        
        if (Carbon::now()->greaterThan($cachedData['expires_at'])) {
            Cache::forget($this->getPhoneVerificationCacheKey());
            return false;
        }
        
        if ($cachedData['code'] != $code) {
            // زيادة عداد المحاولات
            $attempts = $cachedData['attempts'] + 1;
            Cache::put($this->getPhoneVerificationCacheKey(), array_merge($cachedData, ['attempts' => $attempts]), $cachedData['expires_at']);
            
            // حظر بعد عدة محاولات فاشلة
            if ($attempts >= 5) {
                Cache::forget($this->getPhoneVerificationCacheKey());
            }
            
            return false;
        }
        
        // نجاح التحقق
        $this->markPhoneAsVerified();
        return true;
    }

    /**
     * حذف بيانات التحقق من الكاش
     */
    public function clearPhoneVerificationCache()
    {
        Cache::forget($this->getPhoneVerificationCacheKey());
    }

    /**
     * إنشاء مفتاح تخزين بيانات التحقق في الكاش
     */
    protected function getPhoneVerificationCacheKey(): string
    {
        return "phone_verification:{$this->getTable()}:{$this->phone}";
    }
} 