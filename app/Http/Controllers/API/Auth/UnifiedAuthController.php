<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\PhoneVerification;
use App\Models\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UnifiedAuthController extends BaseController
{
    /**
     * Register a new user (customer or seller)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email|unique:sellers,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|unique:customers,phone|unique:sellers,phone',
            'user_type' => 'required|in:customer,seller',
            // Additional seller fields if user_type is seller
            'service_type' => 'required_if:user_type,seller|in:home,studio,both',
            'license' => 'required_if:user_type,seller|string',
            'commercial_register' => 'nullable|string|max:255',
            'commercial_register_document' => 'nullable|string|max:255',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Create user based on user_type
        if ($request->user_type === 'customer') {
            $user = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'location' => $request->location,
                'status' => 'active',
            ]);

            $token = $user->createToken('CustomerToken')->plainTextToken;
            $role = 'customer';
        } else {
            $user = Seller::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'location' => $request->location,
                'service_type' => $request->service_type,
                'license' => $request->license,
                'commercial_register' => $request->commercial_register,
                'commercial_register_document' => $request->commercial_register_document,
                'status' => 'active',
                'request_status' => 'pending',
            ]);

            $token = $user->createToken('SellerToken')->plainTextToken;
            $role = 'seller';
        }

        $response = [
            'user' => $user,
            'token' => $token,
            'role' => $role,
        ];

        return $this->sendResponse($response, 'User registered successfully.');
    }

    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:customer,seller,admin',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Check if login is using email or phone
        $loginField = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $loginValue = $request->input('email') ?? $request->input('phone');

        if (!$loginValue) {
            return $this->sendError('Validation Error.', ['error' => 'Email or phone number is required'], 422);
        }

        // Attempt to authenticate based on user_type
        $guard = $request->user_type;
        
        if (!Auth::guard($guard)->attempt([$loginField => $loginValue, 'password' => $request->password])) {
            return $this->sendError('Unauthorized.', ['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::guard($guard)->user();
        
        // Check if user is active
        if ($user->status !== 'active') {
            // تحديث حالة الحساب إلى نشط حتى لو لم يتم التحقق من الهاتف بعد
            $user->status = 'active';
            $user->save();
        }
        
        // For sellers, check if request is approved
        if ($guard === 'seller' && $user->request_status !== 'approved') {
            return $this->sendError('Unauthorized.', ['error' => 'Seller account is pending approval'], 403);
        }

        $token = $user->createToken($guard . 'Token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'role' => $guard,
            'phone_verified' => !is_null($user->phone_verified_at),
            'phone' => $user->phone
        ];

        return $this->sendResponse($response, 'User logged in successfully.');
    }

    /**
     * Logout user (revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'User logged out successfully.');
    }

    /**
     * Send OTP for password reset or registration verification
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'user_type' => 'nullable|in:customer,seller',
            'action' => 'nullable|in:registration,reset_password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Handle both user_type and action parameters
        $userType = $request->user_type;
        $type = $request->action ?? 'registration';
        
        // For registration action, we don't need to find an existing user
        if ($type === 'registration') {
            // Generate OTP
            $otp = rand(1000, 9999);
            
            // تسجيل رمز التحقق في ملف Laravel.log بتنسيق واضح للعثور عليه بسهولة
            Log::info("======= UNIFIED OTP CODE =======");
            Log::info("Phone: {$request->phone}");
            Log::info("OTP Code: {$otp}");
            Log::info("User Type: {$userType}");
            Log::info("Action: {$type}");
            Log::info("==============================");
            
            // Store OTP in database
            $expiryTime = Carbon::now()->addMinutes(5);
            PhoneVerification::updateOrCreate(
                ['phone' => $request->phone, 'type' => $type],
                [
                    'verification_code' => $otp,
                    'expires_at' => $expiryTime,
                    'attempts' => 0,
                    'verified' => false
                ]
            );

            return $this->sendResponse(['otp' => $otp], 'OTP sent successfully.');
        }
        
        // For reset_password action, we need to find the user
        if ($type === 'reset_password') {
            if (!$userType) {
                return $this->sendError('Validation Error.', ['error' => 'User type is required for password reset'], 422);
            }
            
            $model = $userType === 'customer' ? Customer::class : Seller::class;
            $user = $model::where('phone', $request->phone)->first();

            if (!$user) {
                return $this->sendError('Not Found.', ['error' => 'User not found'], 404);
            }
            
            // Generate OTP
            $otp = rand(1000, 9999);
            
            // تسجيل رمز التحقق في ملف Laravel.log بتنسيق واضح للعثور عليه بسهولة
            Log::info("======= UNIFIED OTP CODE =======");
            Log::info("Phone: {$request->phone}");
            Log::info("OTP Code: {$otp}");
            Log::info("User Type: {$userType}");
            Log::info("Action: {$type}");
            Log::info("==============================");
            
            // Store OTP in database
            $expiryTime = Carbon::now()->addMinutes(5);
            PhoneVerification::updateOrCreate(
                ['phone' => $request->phone, 'type' => $type],
                [
                    'verification_code' => $otp,
                    'expires_at' => $expiryTime,
                    'attempts' => 0,
                    'verified' => false
                ]
            );

            return $this->sendResponse(['otp' => $otp], 'OTP sent successfully.');
        }
        
        // Default case (backward compatibility)
        // Generate OTP
        $otp = rand(1000, 9999);
        
        // تسجيل رمز التحقق في ملف Laravel.log بتنسيق واضح للعثور عليه بسهولة
        Log::info("======= UNIFIED OTP CODE =======");
        Log::info("Phone: {$request->phone}");
        Log::info("OTP Code: {$otp}");
        Log::info("User Type: {$userType}");
        Log::info("Action: {$type}");
        Log::info("==============================");
        
        // Store OTP in database
        $expiryTime = Carbon::now()->addMinutes(5);
        PhoneVerification::updateOrCreate(
            ['phone' => $request->phone, 'type' => $type],
            [
                'verification_code' => $otp,
                'expires_at' => $expiryTime,
                'attempts' => 0,
                'verified' => false
            ]
        );

        return $this->sendResponse(['otp' => $otp], 'OTP sent successfully.');
    }

    /**
     * Verify OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string',
            'user_type' => 'nullable|in:customer,seller',
            'action' => 'nullable|in:registration,reset_password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $phone = $request->phone;
        $otp = $request->otp;
        $type = $request->action ?? $request->type ?? 'registration';

        // Find verification record in database
        $verification = PhoneVerification::where('phone', $phone)
            ->where('type', $type)
            ->first();

        if (!$verification) {
            return $this->sendError('Invalid OTP.', ['error' => 'OTP not found or expired. Please request a new one.'], 400);
        }

        // Increment attempts counter
        $attempts = $verification->incrementAttempts();

        // Check if OTP is expired
        if ($verification->isExpired()) {
            return $this->sendError('Invalid OTP.', ['error' => 'OTP has expired. Please request a new one.'], 400);
        }

        // Check if OTP is valid
        if ($verification->verification_code != $otp) {
            // Block after too many failed attempts
            if ($attempts >= 5) {
                $verification->delete();
                return $this->sendError('Invalid OTP.', ['error' => 'Too many failed attempts. Please request a new OTP.'], 429);
            }
            return $this->sendError('Invalid OTP.', ['error' => 'Invalid OTP. Please try again.'], 400);
        }

        // Mark as verified
        $verification->markAsVerified();
        
        // Generate reset token for password reset
        $resetToken = md5(uniqid() . $request->phone);
        session(['reset_token_' . $request->phone => $resetToken]);

        return $this->sendResponse(['reset_token' => $resetToken], 'OTP verified successfully.');
    }

    /**
     * Reset password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:customer,seller',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Verify reset token
        $storedResetToken = session('reset_token_' . $request->phone);
        
        if (!$storedResetToken || $storedResetToken != $request->reset_token) {
            return $this->sendError('Invalid Reset Token.', ['error' => 'Invalid reset token'], 400);
        }

        // Find user based on user_type
        $model = $request->user_type === 'customer' ? Customer::class : Seller::class;
        $user = $model::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->sendError('Not Found.', ['error' => 'User not found'], 404);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear reset token
        session()->forget('reset_token_' . $request->phone);

        return $this->sendResponse([], 'Password reset successfully.');
    }
} 