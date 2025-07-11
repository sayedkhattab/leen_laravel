<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SellerAuthController extends BaseController
{
    /**
     * Seller register API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:sellers',
            'password' => 'required|min:6',
            'phone' => 'required|unique:sellers',
            'service_type' => 'required|in:home,studio,both',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['status'] = 'inactive';
        $input['request_status'] = 'pending';

        $seller = Seller::create($input);

        $success['token'] = $seller->createToken('SellerToken', ['seller'])->plainTextToken;
        $success['first_name'] = $seller->first_name;
        $success['last_name'] = $seller->last_name;
        $success['id'] = $seller->id;

        return $this->sendResponse($success, 'Seller registered successfully.', 201);
    }

    /**
     * Seller login API
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

        // Try to authenticate seller
        if (Auth::guard('seller')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $seller = Auth::guard('seller')->user();

            // Check if seller account is active
            if ($seller->status !== 'active') {
                // تحديث حالة الحساب إلى نشط حتى لو لم يتم التحقق من الهاتف بعد
                $seller->status = 'active';
                $seller->save();
            }

            $success['token'] = $seller->createToken('SellerToken', ['seller'])->plainTextToken;
            $success['first_name'] = $seller->first_name;
            $success['last_name'] = $seller->last_name;
            $success['id'] = $seller->id;
            $success['request_status'] = $seller->request_status;
            
            // إضافة حقل يشير إلى حالة التحقق من الهاتف
            $success['phone_verified'] = !is_null($seller->phone_verified_at);
            $success['phone'] = $seller->phone;

            return $this->sendResponse($success, 'Seller logged in successfully.');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Invalid credentials'], 401);
        }
    }

    /**
     * Send OTP to seller's phone
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

        $seller = Seller::where('phone', $request->phone)->first();

        if (!$seller) {
            return $this->sendError('Not Found.', ['error' => 'No seller found with this phone number.'], 404);
        }

        // Generate OTP (in a real application, you would send this via SMS)
        $otp = rand(1000, 9999);
        
        // Store OTP in session or database
        // For this example, we'll store it in the seller's remember_token field
        $seller->remember_token = $otp;
        $seller->save();

        // In a real application, you would send the OTP via SMS here
        // For this example, we'll just return it in the response
        $success['otp'] = $otp;
        $success['phone'] = $seller->phone;

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

        $seller = Seller::where('phone', $request->phone)->first();

        if (!$seller) {
            return $this->sendError('Not Found.', ['error' => 'No seller found with this phone number.'], 404);
        }

        // Verify OTP
        if ($seller->remember_token != $request->otp) {
            return $this->sendError('Invalid OTP.', ['error' => 'The OTP you entered is invalid.'], 422);
        }

        // Mark phone as verified
        $seller->phone_verified_at = now();
        $seller->remember_token = null;
        $seller->save();

        $success['token'] = $seller->createToken('SellerToken', ['seller'])->plainTextToken;
        $success['first_name'] = $seller->first_name;
        $success['last_name'] = $seller->last_name;
        $success['id'] = $seller->id;

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

        $seller = Seller::where('phone', $request->phone)->first();

        if (!$seller) {
            return $this->sendError('Not Found.', ['error' => 'No seller found with this phone number.'], 404);
        }

        // Check if phone is verified
        if (!$seller->phone_verified_at) {
            return $this->sendError('Verification Required.', ['error' => 'Phone number not verified.'], 403);
        }

        // Update password
        $seller->password = Hash::make($request->password);
        $seller->save();

        return $this->sendResponse([], 'Password reset successfully.');
    }

    /**
     * Seller logout API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'Seller logged out successfully.');
    }
    
    /**
     * Check if seller's account is approved
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkApprovalStatus(Request $request): JsonResponse
    {
        try {
            Log::info('checkApprovalStatus called', [
                'headers' => $request->headers->all(),
                'has_authorization' => $request->hasHeader('Authorization'),
                'user' => $request->user() ? 'User exists' : 'No user',
                'method' => $request->method(),
                'path' => $request->path(),
                'full_url' => $request->fullUrl()
            ]);
            
            $user = $request->user();
            
            if (!$user) {
                Log::error('No authenticated user found');
                return response()->json([
                    'status' => false,
                    'message' => 'غير مصرح لك بالوصول إلى هذه البيانات',
                    'errors' => ['error' => 'غير مصرح لك بالوصول إلى هذه البيانات']
                ], 401);
            }
            
            Log::info('User details', [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'is_seller' => $user instanceof Seller
            ]);
            
            if (!($user instanceof Seller)) {
                Log::error('User is not a seller', [
                    'user_type' => get_class($user)
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'يجب أن تكون مقدم خدمة للوصول إلى هذه البيانات',
                    'errors' => ['error' => 'يجب أن تكون مقدم خدمة للوصول إلى هذه البيانات']
                ], 403);
            }
            
            $success = [
                'request_status' => $user->request_status,
                'is_approved' => $user->request_status === 'approved',
                'rejection_reason' => $user->request_rejection_reason ?? null
            ];
            
            Log::info('Returning approval status', $success);
            
            return response()->json([
                'status' => true,
                'message' => 'تم التحقق من حالة الاعتماد بنجاح',
                'data' => $success
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error checking seller approval status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء التحقق من حالة الاعتماد',
                'errors' => ['error' => $e->getMessage()]
            ], 500);
        }
    }
} 