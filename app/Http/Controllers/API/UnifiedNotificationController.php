<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Seller;
use Illuminate\Http\Request;

class UnifiedNotificationController extends BaseController
{
    /**
     * Get all notifications for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        // Get notifications based on user type
        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);
        
        return $this->sendResponse($notifications, 'Notifications retrieved successfully.');
    }

    /**
     * Mark a notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        // Verify user has access to this notification
        if (
            ($userType === 'customer' && $notification->customer_id !== $user->id) ||
            ($userType === 'seller' && $notification->seller_id !== $user->id)
        ) {
            return $this->sendError('Unauthorized.', ['error' => 'You do not have access to this notification'], 403);
        }
        
        // Mark as read
        $notification->is_read = true;
        $notification->save();
        
        return $this->sendResponse($notification, 'Notification marked as read.');
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