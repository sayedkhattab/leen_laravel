<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    /**
     * عرض قائمة الإشعارات للعميل
     */
    public function index(Request $request)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $query = Notification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customer->id);

        // تصفية حسب النوع
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // تصفية حسب حالة القراءة
        if ($request->has('read')) {
            $query->where('read_at', $request->read ? '!=' : '=', null);
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        $query->orderBy('created_at', 'desc');

        $notifications = $query->paginate(10);
        return $this->sendResponse($notifications, 'تم استرجاع قائمة الإشعارات بنجاح');
    }

    /**
     * عرض تفاصيل إشعار محدد
     */
    public function show($id)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $notification = Notification::where('id', $id)
            ->where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customer->id)
            ->first();

        if (!$notification) {
            return $this->sendError('الإشعار غير موجود', [], 404);
        }

        // تحديث حالة القراءة إذا لم يكن مقروءاً
        if (!$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }

        return $this->sendResponse($notification, 'تم استرجاع تفاصيل الإشعار بنجاح');
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead($id)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $notification = Notification::where('id', $id)
            ->where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customer->id)
            ->first();

        if (!$notification) {
            return $this->sendError('الإشعار غير موجود', [], 404);
        }

        if (!$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }

        return $this->sendResponse($notification, 'تم تحديد الإشعار كمقروء بنجاح');
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $count = Notification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customer->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->sendResponse(['marked_count' => $count], 'تم تحديد جميع الإشعارات كمقروءة بنجاح');
    }

    /**
     * حذف إشعار محدد
     */
    public function destroy($id)
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $notification = Notification::where('id', $id)
            ->where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customer->id)
            ->first();

        if (!$notification) {
            return $this->sendError('الإشعار غير موجود', [], 404);
        }

        $notification->delete();
        return $this->sendResponse([], 'تم حذف الإشعار بنجاح');
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public function getUnreadCount()
    {
        $customer = Auth::user()->customer;
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $count = Notification::where('notifiable_type', 'App\Models\Customer')
            ->where('notifiable_id', $customer->id)
            ->whereNull('read_at')
            ->count();

        return $this->sendResponse(['unread_count' => $count], 'تم استرجاع عدد الإشعارات غير المقروءة بنجاح');
    }
} 