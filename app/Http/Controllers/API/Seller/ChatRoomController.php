<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\API\BaseController;
use App\Models\ChatRoom;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatRoomController extends BaseController
{
    /**
     * عرض قائمة غرف الدردشة للبائع
     */
    public function index()
    {
        $seller = Auth::user()->seller;
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $chatRooms = ChatRoom::where('seller_id', $seller->id)
            ->with(['customer'])
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
        $seller = Auth::user()->seller;
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $chatRoom = ChatRoom::where('id', $id)
            ->where('seller_id', $seller->id)
            ->with(['customer', 'messages'])
            ->first();

        if (!$chatRoom) {
            return $this->sendError('غرفة الدردشة غير موجودة', [], 404);
        }

        // تحديث حالة الرسائل غير المقروءة
        $chatRoom->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->sendResponse($chatRoom, 'تم استرجاع تفاصيل غرفة الدردشة بنجاح');
    }

    /**
     * إرسال رسالة في غرفة دردشة
     */
    public function sendMessage(Request $request, $chatRoomId)
    {
        $seller = Auth::user()->seller;
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $chatRoom = ChatRoom::where('id', $chatRoomId)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$chatRoom) {
            return $this->sendError('غرفة الدردشة غير موجودة', [], 404);
        }

        // إنشاء رسالة جديدة
        $message = $chatRoom->messages()->create([
            'message' => $request->message,
            'sender_type' => 'seller',
            'is_read' => false
        ]);

        // تحديث وقت آخر نشاط لغرفة الدردشة
        $chatRoom->touch();

        // إرسال إشعار للعميل بوجود رسالة جديدة
        // TODO: إضافة رمز إرسال الإشعارات

        return $this->sendResponse($message, 'تم إرسال الرسالة بنجاح');
    }

    /**
     * البحث في غرف الدردشة
     */
    public function search(Request $request)
    {
        $seller = Auth::user()->seller;
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $keyword = $request->keyword;

        // البحث في غرف الدردشة حسب اسم العميل
        $chatRooms = ChatRoom::where('seller_id', $seller->id)
            ->whereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            })
            ->with(['customer'])
            ->get();

        // البحث في الرسائل
        $messageResults = Message::whereHas('chatRoom', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
            ->where('message', 'like', "%{$keyword}%")
            ->with(['chatRoom', 'chatRoom.customer'])
            ->get()
            ->groupBy('chat_room_id');

        return $this->sendResponse([
            'chat_rooms' => $chatRooms,
            'messages' => $messageResults
        ], 'تم استرجاع نتائج البحث بنجاح');
    }

    /**
     * تحديد الرسائل كمقروءة
     */
    public function markAsRead($chatRoomId)
    {
        $seller = Auth::user()->seller;
        if (!$seller) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $chatRoom = ChatRoom::where('id', $chatRoomId)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$chatRoom) {
            return $this->sendError('غرفة الدردشة غير موجودة', [], 404);
        }

        // تحديث حالة الرسائل غير المقروءة
        $count = $chatRoom->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->sendResponse(['marked_count' => $count], 'تم تحديد الرسائل كمقروءة بنجاح');
    }
} 