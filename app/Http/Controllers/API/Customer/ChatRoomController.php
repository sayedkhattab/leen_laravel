<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseController;
use App\Models\ChatRoom;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatRoomController extends BaseController
{
    /**
     * إنشاء غرفة دردشة مع بائع
     */
    public function createWithSeller($sellerId)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $seller = Seller::find($sellerId);
        if (!$seller) {
            return $this->sendError('البائع غير موجود', [], 404);
        }

        // التحقق من وجود غرفة دردشة سابقة
        $existingChatRoom = ChatRoom::where('customer_id', $customer->id)
            ->where('seller_id', $sellerId)
            ->first();

        if ($existingChatRoom) {
            return $this->sendResponse($existingChatRoom->load(['customer', 'seller', 'messages']), 'تم استرجاع غرفة الدردشة الموجودة');
        }

        // إنشاء غرفة دردشة جديدة
        $chatRoom = new ChatRoom();
        $chatRoom->customer_id = $customer->id;
        $chatRoom->seller_id = $sellerId;
        $chatRoom->save();

        return $this->sendResponse($chatRoom->load(['customer', 'seller']), 'تم إنشاء غرفة الدردشة بنجاح');
    }

    /**
     * عرض قائمة غرف الدردشة للعميل
     */
    public function index()
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $chatRooms = ChatRoom::where('customer_id', $customer->id)
            ->with(['seller'])
            ->withCount('unreadMessages')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->sendResponse($chatRooms, 'تم استرجاع قائمة غرف الدردشة بنجاح');
    }

    /**
     * عرض تفاصيل غرفة دردشة محددة
     */
    public function show($id)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $chatRoom = ChatRoom::where('id', $id)
            ->where('customer_id', $customer->id)
            ->with(['seller', 'messages'])
            ->first();

        if (!$chatRoom) {
            return $this->sendError('غرفة الدردشة غير موجودة', [], 404);
        }

        // تحديث حالة الرسائل غير المقروءة
        $chatRoom->messages()
            ->where('sender_type', 'seller')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->sendResponse($chatRoom, 'تم استرجاع تفاصيل غرفة الدردشة بنجاح');
    }

    /**
     * إرسال رسالة في غرفة دردشة
     */
    public function sendMessage(Request $request, $chatRoomId)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $chatRoom = ChatRoom::where('id', $chatRoomId)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$chatRoom) {
            return $this->sendError('غرفة الدردشة غير موجودة', [], 404);
        }

        // إنشاء رسالة جديدة
        $message = $chatRoom->messages()->create([
            'message' => $request->message,
            'sender_type' => 'customer',
            'is_read' => false
        ]);

        // تحديث وقت آخر نشاط لغرفة الدردشة
        $chatRoom->touch();

        // إرسال إشعار للبائع بوجود رسالة جديدة
        // TODO: إضافة رمز إرسال الإشعارات

        return $this->sendResponse($message, 'تم إرسال الرسالة بنجاح');
    }
} 