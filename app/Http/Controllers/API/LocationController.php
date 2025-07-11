<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Seller;
use App\Models\Customer;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends BaseController
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Update user's current location
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = Auth::user();
        $userType = $this->getUserType($user);
        
        if (!$userType) {
            return $this->sendError('Unauthorized. Only sellers and customers can update location.', [], 403);
        }

        $success = $this->firebaseService->updateUserLocation(
            $user->id,
            $request->latitude,
            $request->longitude,
            $userType
        );

        if ($success) {
            return $this->sendResponse([], 'Location updated successfully.');
        } else {
            return $this->sendError('Failed to update location. Please try again.', [], 500);
        }
    }

    /**
     * Get nearby sellers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNearbyUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:50',
            'user_type' => 'nullable|string|in:seller,customer',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $radius = $request->radius ?? 5; // Default 5km
        $userType = $request->user_type ?? 'seller'; // Default to finding sellers
        
        $nearbyUsers = $this->firebaseService->getNearbyUsers(
            $request->latitude,
            $request->longitude,
            $radius,
            $userType
        );
        
        // Get user details from database for the nearby users
        $userDetails = [];
        $userModel = ($userType === 'seller') ? Seller::class : Customer::class;
        
        foreach (array_keys($nearbyUsers) as $userId) {
            $user = $userModel::find($userId);
            if ($user) {
                $userDetails[$userId] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile_image' => $user->profile_image,
                    'location' => $nearbyUsers[$userId],
                ];
            }
        }

        return $this->sendResponse($userDetails, 'Nearby ' . $userType . 's retrieved successfully.');
    }

    /**
     * Determine user type from authenticated user
     *
     * @param mixed $user
     * @return string|null
     */
    private function getUserType($user)
    {
        if ($user instanceof Seller) {
            return 'seller';
        } elseif ($user instanceof Customer) {
            return 'customer';
        }
        
        return null;
    }
} 