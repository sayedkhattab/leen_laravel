<?php

namespace App\Services;

use App\Models\SmsNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForJawalyService
{
    protected $baseUrl;
    protected $apiKey;
    protected $apiSecret;
    protected $sender;
    
    public function __construct()
    {
        $this->baseUrl = config('services.forjawaly.base_url');
        $this->apiKey = config('services.forjawaly.key');
        $this->apiSecret = config('services.forjawaly.secret');
        // اسم المرسل من ملف الإعدادات (يجب أن يكون معتمداً في بوابة فورجوالي)
        $this->sender = config('services.forjawaly.sender');
    }

    /**
     * إرسال رسالة SMS عبر خدمة فورجوالي
     *
     * @param string $phone رقم الهاتف
     * @param string $message نص الرسالة
     * @param string $type نوع الرسالة (otp, marketing, notification)
     * @return array استجابة الخدمة
     */
    public function sendSMS($phone, $message, $type = 'notification')
    {
        $headers = [
            "Accept: application/json",
            "Content-Type: application/json"
        ];

        $data = [
            "messages" => [
                [
                    "text" => $message,
                    "numbers" => [$phone],
                    "sender" => $this->sender
                ]
            ]
        ];

        Log::info("Attempting to send SMS to {$phone}", ['message' => $message]);

        try {
            $response = Http::withHeaders($headers)
                ->baseUrl($this->baseUrl)
                ->withBasicAuth($this->apiKey, $this->apiSecret)
                ->post('account/area/sms/send', $data);

            $result = $response->json();
            
            Log::info("ForJawaly API response", ['response' => $result]);
            
            // Log to database
            $status = isset($result['code']) && $result['code'] == 200 ? 'sent' : 'failed';
            
            SmsNotification::create([
                'phone' => $phone,
                'message' => $message,
                'type' => $type,
                'status' => $status,
                'response_data' => $result
            ]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error("ForJawaly API error: " . $e->getMessage());
            
            // Log failed attempt to database
            SmsNotification::create([
                'phone' => $phone,
                'message' => $message,
                'type' => $type,
                'status' => 'failed',
                'response_data' => ['error' => $e->getMessage()]
            ]);
            
            return [
                'code' => 500,
                'message' => 'Error connecting to SMS service: ' . $e->getMessage()
            ];
        }
    }
} 