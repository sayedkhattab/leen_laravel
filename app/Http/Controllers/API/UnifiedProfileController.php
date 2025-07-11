<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Customer;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UnifiedProfileController extends BaseController
{
    /**
     * Get the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        // Add user type to response
        $userData = $user->toArray();
        $userData['user_type'] = $userType;
        
        return $this->sendResponse($userData, 'User profile retrieved successfully.');
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        // Different validation rules based on user type
        if ($userType === 'customer') {
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:customers,email,' . $user->id,
                'phone' => 'sometimes|string|unique:customers,phone,' . $user->id,
                'location' => 'sometimes|string',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
                'current_password' => 'required_with:password|string',
                'password' => 'sometimes|string|min:8|confirmed',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:sellers,email,' . $user->id,
                'phone' => 'sometimes|string|unique:sellers,phone,' . $user->id,
                'location' => 'sometimes|string',
                'seller_logo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
                'seller_banner' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
                'current_password' => 'required_with:password|string',
                'password' => 'sometimes|string|min:8|confirmed',
            ]);
        }

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Check current password if trying to update password
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->sendError('Validation Error.', ['current_password' => 'Current password is incorrect'], 422);
            }
        }

        // Update user data
        $updateData = $request->only([
            'first_name', 
            'last_name', 
            'email', 
            'phone', 
            'location'
        ]);
        
        // Update password if provided
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        // Handle image uploads based on user type
        if ($userType === 'customer' && $request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                $oldPath = str_replace('/storage/', '', $user->image);
                Storage::disk('public')->delete($oldPath);
            }
            
            $imagePath = $request->file('image')->store('customers', 'public');
            $updateData['image'] = '/storage/' . $imagePath;
        } elseif ($userType === 'seller') {
            // Handle seller logo
            if ($request->hasFile('seller_logo')) {
                // Delete old logo if exists
                if ($user->seller_logo) {
                    $oldPath = str_replace('/storage/', '', $user->seller_logo);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $logoPath = $request->file('seller_logo')->store('sellers', 'public');
                $updateData['seller_logo'] = '/storage/' . $logoPath;
            }
            
            // Handle seller banner
            if ($request->hasFile('seller_banner')) {
                // Delete old banner if exists
                if ($user->seller_banner) {
                    $oldPath = str_replace('/storage/', '', $user->seller_banner);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $bannerPath = $request->file('seller_banner')->store('sellers', 'public');
                $updateData['seller_banner'] = '/storage/' . $bannerPath;
            }
        }
        
        // Update user
        $user->update($updateData);
        
        // Add user type to response
        $userData = $user->toArray();
        $userData['user_type'] = $userType;
        
        return $this->sendResponse($userData, 'User profile updated successfully.');
    }
    
    /**
     * Determine user type from user model.
     *
     * @param  mixed  $user
     * @return string
     */
    protected function getUserType($user)
    {
        if ($user instanceof Customer) {
            return 'customer';
        } elseif ($user instanceof Seller) {
            return 'seller';
        } else {
            return 'admin';
        }
    }
} 