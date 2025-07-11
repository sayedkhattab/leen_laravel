<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseController
{
    /**
     * Get admin profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $admin = $request->user();

        return $this->sendResponse($admin, 'Admin profile retrieved successfully.');
    }

    /**
     * Update admin profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $admin = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:admins,email,' . $admin->id,
            'current_password' => 'required_with:password',
            'password' => 'sometimes|required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Check current password if trying to update password
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return $this->sendError('Validation Error.', ['current_password' => ['Current password is incorrect.']], 422);
            }
            $admin->password = Hash::make($request->password);
        }

        if ($request->has('name')) {
            $admin->name = $request->name;
        }

        if ($request->has('email')) {
            $admin->email = $request->email;
        }

        $admin->save();

        return $this->sendResponse($admin, 'Admin profile updated successfully.');
    }
} 