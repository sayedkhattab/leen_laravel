<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfileController extends BaseController
{
    /**
     * Get seller profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Check if user is a Seller instance
            if (!$user instanceof Seller) {
                Log::error('User is not a Seller instance', [
                    'user_type' => get_class($user),
                    'user_id' => $user->id
                ]);
                return $this->sendError('Unauthorized.', ['error' => 'You are not authorized to access this profile.'], 403);
            }
            
            return $this->sendResponse($user, 'Seller profile retrieved successfully.');
        } catch (\Exception $e) {
            Log::error('Error retrieving seller profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Server Error.', ['error' => 'An error occurred while retrieving the profile.'], 500);
        }
    }

    /**
     * Update seller profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            Log::info('Seller profile update request received', [
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'has_files' => $request->hasFile('seller_logo') || $request->hasFile('seller_banner') || $request->hasFile('license'),
                'file_sizes' => [
                    'seller_logo' => $request->hasFile('seller_logo') ? $request->file('seller_logo')->getSize() : null,
                    'seller_banner' => $request->hasFile('seller_banner') ? $request->file('seller_banner')->getSize() : null,
                    'license' => $request->hasFile('license') ? $request->file('license')->getSize() : null,
                ]
            ]);
            
            $user = $request->user();
            
            // Check if user is a Seller instance
            if (!$user instanceof Seller) {
                Log::error('User is not a Seller instance', [
                    'user_type' => get_class($user),
                    'user_id' => $user->id
                ]);
                return $this->sendError('Unauthorized.', ['error' => 'You are not authorized to update this profile.'], 403);
            }

            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required',
                'last_name' => 'sometimes|required',
                'email' => 'sometimes|required|email|unique:sellers,email,' . $user->id,
                'phone' => 'sometimes|required|unique:sellers,phone,' . $user->id,
                'location' => 'sometimes|required',
                'current_password' => 'required_with:password',
                'password' => 'sometimes|required|min:6|confirmed',
                'seller_logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120', // Increased to 5MB
                'seller_banner' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120', // Increased to 5MB
                'license' => 'sometimes|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // Increased to 5MB
            ]);

            if ($validator->fails()) {
                Log::warning('Seller profile update validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
            }

            // Check current password if trying to update password
            if ($request->has('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return $this->sendError('Validation Error.', ['current_password' => ['Current password is incorrect.']], 422);
                }
                $user->password = Hash::make($request->password);
            }

            // Update basic info
            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('phone')) {
                $user->phone = $request->phone;
                // If phone is changed, require verification again
                $user->phone_verified_at = null;
            }

            if ($request->has('location')) {
                $user->location = $request->location;
            }

            // Handle file uploads
            if ($request->hasFile('seller_logo')) {
                try {
                    Log::info('Processing seller logo upload');
                    
                    // Save file to storage
                    $path = $request->file('seller_logo')->store('images/sellers', 'public');
                    $user->seller_logo = 'storage/' . $path;
                    
                    Log::info('Seller logo uploaded successfully', ['path' => $user->seller_logo]);
                } catch (\Exception $e) {
                    Log::error('Error uploading seller logo', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if ($request->hasFile('seller_banner')) {
                try {
                    Log::info('Processing seller banner upload');
                    
                    // Save file to storage
                    $path = $request->file('seller_banner')->store('images/sellers', 'public');
                    $user->seller_banner = 'storage/' . $path;
                    
                    Log::info('Seller banner uploaded successfully', ['path' => $user->seller_banner]);
                } catch (\Exception $e) {
                    Log::error('Error uploading seller banner', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if ($request->hasFile('license')) {
                try {
                    Log::info('Processing seller license upload');
                    
                    // Save file to storage
                    $path = $request->file('license')->store('images/sellers/licenses', 'public');
                    $user->license = 'storage/' . $path;
                    
                    Log::info('Seller license uploaded successfully', ['path' => $user->license]);
                } catch (\Exception $e) {
                    Log::error('Error uploading seller license', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $user->save();
            
            Log::info('Seller profile updated successfully', ['seller_id' => $user->id]);

            return $this->sendResponse($user, 'Seller profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating seller profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Server Error.', ['error' => 'An error occurred while updating the profile.'], 500);
        }
    }
} 