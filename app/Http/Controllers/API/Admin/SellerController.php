<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SellerController extends BaseController
{
    /**
     * Display a listing of the sellers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Seller::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by request_status
        if ($request->has('request_status')) {
            $query->where('request_status', $request->request_status);
        }

        // Filter by service_type
        if ($request->has('service_type')) {
            if ($request->service_type === 'both') {
                $query->where('service_type', 'both');
            } else {
                $query->where(function ($q) use ($request) {
                    $q->where('service_type', $request->service_type)
                      ->orWhere('service_type', 'both');
                });
            }
        }

        // Search by name or email
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $sellers = $query->paginate(10);

        return $this->sendResponse($sellers, 'Sellers retrieved successfully.');
    }

    /**
     * Display the specified seller.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Seller $seller): JsonResponse
    {
        $seller->load(['employees', 'homeServices', 'studioServices']);

        return $this->sendResponse($seller, 'Seller retrieved successfully.');
    }

    /**
     * Update the specified seller in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Seller $seller): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:active,inactive',
            'request_status' => 'sometimes|required|in:pending,approved,rejected',
            'request_rejection_reason' => 'required_if:request_status,rejected',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        if ($request->has('status')) {
            $seller->status = $request->status;
        }

        if ($request->has('request_status')) {
            $seller->request_status = $request->request_status;
            
            // If request is approved, set seller status to active
            if ($request->request_status === 'approved') {
                $seller->status = 'active';
            }
            
            // If request is rejected, set rejection reason
            if ($request->request_status === 'rejected') {
                $seller->request_rejection_reason = $request->request_rejection_reason;
            }
        }

        $seller->save();

        return $this->sendResponse($seller, 'Seller updated successfully.');
    }

    /**
     * Remove the specified seller from storage.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Seller $seller): JsonResponse
    {
        // Check if seller has services or bookings
        if ($seller->homeServices()->count() > 0 || $seller->studioServices()->count() > 0 || 
            $seller->homeServiceBookings()->count() > 0 || $seller->studioServiceBookings()->count() > 0) {
            return $this->sendError('Cannot delete seller with services or bookings.', [], 422);
        }

        $seller->delete();

        return $this->sendResponse([], 'Seller deleted successfully.');
    }
} 