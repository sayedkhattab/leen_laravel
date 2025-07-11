<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Services\ForJawalyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class PhoneVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_otp()
    {
        $forJawalyMock = Mockery::mock(ForJawalyService::class);
        $forJawalyMock->shouldReceive('sendSMS')->once()->andReturn([
            'code' => 200,
            'message' => 'Message sent successfully'
        ]);
        $this->app->instance(ForJawalyService::class, $forJawalyMock);

        $response = $this->postJson('/api/v1/send-otp', [
            'phone' => '0500000000',
            'type' => 'registration'
        ]);

        $response->assertStatus(200)
                ->assertJson(['status' => 'success']);
    }

    public function test_can_verify_otp()
    {
        $phone = '0500000000';
        $otp = '123456';
        
        Cache::put("otp:registration:{$phone}", [
            'code' => $otp,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0
        ], now()->addMinutes(5));

        $response = $this->postJson('/api/v1/verify-otp', [
            'phone' => $phone,
            'otp' => $otp,
            'type' => 'registration'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'verified' => true
                ]);
    }

    public function test_can_check_verification_status()
    {
        $phone = '0500000000';
        $verificationKey = "verified:registration:{$phone}";
        
        Cache::put($verificationKey, true, now()->addMinutes(30));

        $response = $this->postJson('/api/v1/check-verification', [
            'phone' => $phone,
            'type' => 'registration'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'verified' => true
                ]);
    }

    public function test_invalid_otp_returns_error()
    {
        $phone = '0500000000';
        $correctOtp = '123456';
        $wrongOtp = '654321';
        
        Cache::put("otp:registration:{$phone}", [
            'code' => $correctOtp,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0
        ], now()->addMinutes(5));

        $response = $this->postJson('/api/v1/verify-otp', [
            'phone' => $phone,
            'otp' => $wrongOtp,
            'type' => 'registration'
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => 'error'
                ]);
    }

    public function test_expired_otp_returns_error()
    {
        $phone = '0500000000';
        $otp = '123456';
        
        Cache::put("otp:registration:{$phone}", [
            'code' => $otp,
            'expires_at' => now()->subMinutes(10),
            'attempts' => 0
        ], now()->subMinutes(10));

        $response = $this->postJson('/api/v1/verify-otp', [
            'phone' => $phone,
            'otp' => $otp,
            'type' => 'registration'
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'OTP not found or expired. Please request a new one.'
                ]);
    }

    public function test_test_number_works_with_fixed_otp()
    {
        $response = $this->postJson('/api/v1/send-otp', [
            'phone' => '01121926996',
            'type' => 'registration'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'test_mode' => true,
                    'test_otp' => '123456'
                ]);

        $verifyResponse = $this->postJson('/api/v1/verify-otp', [
            'phone' => '01121926996',
            'otp' => '123456',
            'type' => 'registration'
        ]);

        $verifyResponse->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'verified' => true
                ]);
    }

    public function test_too_many_failed_attempts_locks_verification()
    {
        $phone = '0500000000';
        $correctOtp = '123456';
        $wrongOtp = '654321';
        
        Cache::put("otp:registration:{$phone}", [
            'code' => $correctOtp,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 4
        ], now()->addMinutes(5));

        $response = $this->postJson('/api/v1/verify-otp', [
            'phone' => $phone,
            'otp' => $wrongOtp,
            'type' => 'registration'
        ]);

        $response->assertStatus(429)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Too many failed attempts. Please request a new OTP.'
                ]);

        $this->assertNull(Cache::get("otp:registration:{$phone}"));
    }
} 