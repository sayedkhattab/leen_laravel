<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseController;
use App\Models\HomeService;
use App\Models\Seller;
use App\Models\StudioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RatingController extends BaseController
{
    use CustomerControllerTrait;
    
    /**
     * تقييم بائع
     */
    public function rateSeller(Request $request, $sellerId)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $seller = Seller::find($sellerId);
        if (!$seller) {
            return $this->sendError('البائع غير موجود', [], 404);
        }

        // التحقق من أن العميل قد قام بحجز خدمة من هذا البائع من قبل
        $hasBooking = DB::table('home_service_bookings')
            ->join('home_services', 'home_service_bookings.home_service_id', '=', 'home_services.id')
            ->where('home_services.seller_id', $sellerId)
            ->where('home_service_bookings.customer_id', $customer->id)
            ->where('home_service_bookings.status', 'completed')
            ->exists();

        if (!$hasBooking) {
            $hasBooking = DB::table('studio_service_bookings')
                ->join('studio_services', 'studio_service_bookings.studio_service_id', '=', 'studio_services.id')
                ->where('studio_services.seller_id', $sellerId)
                ->where('studio_service_bookings.customer_id', $customer->id)
                ->where('studio_service_bookings.status', 'completed')
                ->exists();
        }

        if (!$hasBooking) {
            return $this->sendError('لا يمكنك تقييم بائع لم تقم بحجز خدمة منه من قبل', [], 400);
        }

        // إضافة أو تحديث التقييم
        $rating = DB::table('seller_ratings')
            ->updateOrInsert(
                [
                    'seller_id' => $sellerId,
                    'customer_id' => $customer->id
                ],
                [
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

        // تحديث متوسط تقييم البائع
        $averageRating = DB::table('seller_ratings')
            ->where('seller_id', $sellerId)
            ->avg('rating');

        $seller->rating = round($averageRating, 1);
        $seller->save();

        return $this->sendResponse(['average_rating' => $seller->rating], 'تم تقييم البائع بنجاح');
    }

    /**
     * تقييم خدمة منزلية
     */
    public function rateHomeService(Request $request, $homeServiceId)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $homeService = HomeService::find($homeServiceId);
        if (!$homeService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        // التحقق من أن العميل قد قام بحجز هذه الخدمة من قبل
        $hasBooking = DB::table('home_service_bookings')
            ->where('home_service_id', $homeServiceId)
            ->where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->exists();

        if (!$hasBooking) {
            return $this->sendError('لا يمكنك تقييم خدمة لم تقم بحجزها من قبل', [], 400);
        }

        // إضافة أو تحديث التقييم
        $rating = DB::table('home_service_ratings')
            ->updateOrInsert(
                [
                    'home_service_id' => $homeServiceId,
                    'customer_id' => $customer->id
                ],
                [
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

        // تحديث متوسط تقييم الخدمة
        $averageRating = DB::table('home_service_ratings')
            ->where('home_service_id', $homeServiceId)
            ->avg('rating');

        $homeService->rating = round($averageRating, 1);
        $homeService->save();

        return $this->sendResponse(['average_rating' => $homeService->rating], 'تم تقييم الخدمة بنجاح');
    }

    /**
     * تقييم خدمة استوديو
     */
    public function rateStudioService(Request $request, $studioServiceId)
    {
        $customer = $this->getCurrentCustomer();
        if (!$customer) {
            return $this->sendError('غير مصرح لك بالوصول إلى هذه البيانات', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('خطأ في البيانات المدخلة', $validator->errors(), 422);
        }

        $studioService = StudioService::find($studioServiceId);
        if (!$studioService) {
            return $this->sendError('الخدمة غير موجودة', [], 404);
        }

        // التحقق من أن العميل قد قام بحجز هذه الخدمة من قبل
        $hasBooking = DB::table('studio_service_bookings')
            ->where('studio_service_id', $studioServiceId)
            ->where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->exists();

        if (!$hasBooking) {
            return $this->sendError('لا يمكنك تقييم خدمة لم تقم بحجزها من قبل', [], 400);
        }

        // إضافة أو تحديث التقييم
        $rating = DB::table('studio_service_ratings')
            ->updateOrInsert(
                [
                    'studio_service_id' => $studioServiceId,
                    'customer_id' => $customer->id
                ],
                [
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

        // تحديث متوسط تقييم الخدمة
        $averageRating = DB::table('studio_service_ratings')
            ->where('studio_service_id', $studioServiceId)
            ->avg('rating');

        $studioService->rating = round($averageRating, 1);
        $studioService->save();

        return $this->sendResponse(['average_rating' => $studioService->rating], 'تم تقييم الخدمة بنجاح');
    }
} 