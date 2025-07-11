<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerAuthController extends BaseController
{
    /**
     * Customer register API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required|min:6',
            'phone' => 'required|unique:customers',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['status'] = 'active';

        $customer = Customer::create($input);

        $success['token'] = $customer->createToken('CustomerToken', ['customer'])->plainTextToken;
        $success['first_name'] = $customer->first_name;
        $success['last_name'] = $customer->last_name;
        $success['id'] = $customer->id;

        return $this->sendResponse($success, 'Customer registered successfully.', 201);
    }

    /**
     * Customer login API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Try to authenticate customer
        if (Auth::guard('customer')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $customer = Auth::guard('customer')->user();

            // Check if customer account is active
            if ($customer->status !== 'active') {
                return $this->sendError('Account Inactive.', ['error' => 'Your account is not active.'], 403);
            }

            $success['token'] = $customer->createToken('CustomerToken', ['customer'])->plainTextToken;
            $success['first_name'] = $customer->first_name;
            $success['last_name'] = $customer->last_name;
            $success['id'] = $customer->id;

            return $this->sendResponse($success, 'Customer logged in successfully.');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Invalid credentials'], 401);
        }
    }

    /**
     * Send OTP to customer's phone
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return $this->sendError('Not Found.', ['error' => 'No customer found with this phone number.'], 404);
        }

        // Generate OTP (in a real application, you would send this via SMS)
        $otp = rand(1000, 9999);
        
        // Store OTP in session or database
        // For this example, we'll store it in the customer's remember_token field
        $customer->remember_token = $otp;
        $customer->save();

        // In a real application, you would send the OTP via SMS here
        // For this example, we'll just return it in the response
        $success['otp'] = $otp;
        $success['phone'] = $customer->phone;

        return $this->sendResponse($success, 'OTP sent successfully.');
    }

    /**
     * Verify OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return $this->sendError('Not Found.', ['error' => 'No customer found with this phone number.'], 404);
        }

        // Verify OTP
        if ($customer->remember_token != $request->otp) {
            return $this->sendError('Invalid OTP.', ['error' => 'The OTP you entered is invalid.'], 422);
        }

        // Mark phone as verified
        $customer->phone_verified_at = now();
        $customer->remember_token = null;
        $customer->save();

        $success['token'] = $customer->createToken('CustomerToken', ['customer'])->plainTextToken;
        $success['first_name'] = $customer->first_name;
        $success['last_name'] = $customer->last_name;
        $success['id'] = $customer->id;

        return $this->sendResponse($success, 'Phone verified successfully.');
    }

    /**
     * Reset password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return $this->sendError('Not Found.', ['error' => 'No customer found with this phone number.'], 404);
        }

        // Check if phone is verified
        if (!$customer->phone_verified_at) {
            return $this->sendError('Verification Required.', ['error' => 'Phone number not verified.'], 403);
        }

        // Update password
        $customer->password = Hash::make($request->password);
        $customer->save();

        return $this->sendResponse([], 'Password reset successfully.');
    }

    /**
     * Customer logout API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'Customer logged out successfully.');
    }
}