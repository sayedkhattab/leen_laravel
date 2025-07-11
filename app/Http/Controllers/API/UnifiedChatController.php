<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\ChatRoom;
use App\Models\Customer;
use App\Models\Message;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnifiedChatController extends BaseController
{
    /**
     * Get all chat rooms for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        $chatRooms = $user->chatRooms()
            ->with(['customer', 'seller', 'lastMessage'])
            ->latest('updated_at')
            ->get();
        
        return $this->sendResponse($chatRooms, 'Chat rooms retrieved successfully.');
    }

    /**
     * Get a specific chat room with messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ChatRoom $chatRoom)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        // Verify user has access to this chat room
        if (
            ($userType === 'customer' && $chatRoom->customer_id !== $user->id) ||
            ($userType === 'seller' && $chatRoom->seller_id !== $user->id)
        ) {
            return $this->sendError('Unauthorized.', ['error' => 'You do not have access to this chat room'], 403);
        }
        
        // Load chat room with messages
        $chatRoom->load([
            'customer', 
            'seller', 
            'messages' => function ($query) {
                $query->latest();
            }
        ]);
        
        // Mark unread messages as read
        if ($userType === 'customer') {
            Message::where('chat_room_id', $chatRoom->id)
                ->where('sender_type', 'seller')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            Message::where('chat_room_id', $chatRoom->id)
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
        
        return $this->sendResponse($chatRoom, 'Chat room retrieved successfully.');
    }

    /**
     * Send a message in a chat room.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatRoom  $chatRoom
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        // Verify user has access to this chat room
        if (
            ($userType === 'customer' && $chatRoom->customer_id !== $user->id) ||
            ($userType === 'seller' && $chatRoom->seller_id !== $user->id)
        ) {
            return $this->sendError('Unauthorized.', ['error' => 'You do not have access to this chat room'], 403);
        }
        
        // Create message
        $message = new Message([
            'chat_room_id' => $chatRoom->id,
            'sender_type' => $userType,
            'message' => $request->message,
            'is_read' => false,
        ]);
        
        $message->save();
        
        // Update chat room's updated_at
        $chatRoom->touch();
        
        // TODO: Send push notification to the other user
        
        return $this->sendResponse($message, 'Message sent successfully.');
    }
    
    /**
     * Create a new chat room between customer and seller.
     * This is only accessible to customers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function createWithSeller(Request $request, Seller $seller)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);
        
        // Only customers can initiate chat
        if ($userType !== 'customer') {
            return $this->sendError('Unauthorized.', ['error' => 'Only customers can initiate chat'], 403);
        }
        
        // Check if chat room already exists
        $existingChatRoom = ChatRoom::where('customer_id', $user->id)
            ->where('seller_id', $seller->id)
            ->first();
        
        if ($existingChatRoom) {
            return $this->sendResponse($existingChatRoom, 'Chat room already exists.');
        }
        
        // Create new chat room
        $chatRoom = ChatRoom::create([
            'customer_id' => $user->id,
            'seller_id' => $seller->id,
        ]);
        
        $chatRoom->load(['customer', 'seller']);
        
        return $this->sendResponse($chatRoom, 'Chat room created successfully.');
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