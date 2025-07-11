<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeServiceBooking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'home_service_id',
        'customer_id',
        'seller_id',
        'employee_id',
        'date',
        'start_time',
        'payment_status',
        'booking_status',
        'location',
        'paid_amount',
        'request_rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the home service that owns the booking.
     */
    public function homeService()
    {
        return $this->belongsTo(HomeService::class);
    }

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the seller that owns the booking.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the employee that owns the booking.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the payment associated with the booking.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Alias for the related HomeService to allow generic `service` relation usage.
     *
     * This makes it possible to eager-load nested relations like `service.seller`
     * or `service.subCategory` across both HomeServiceBooking and
     * StudioServiceBooking models using the same name (`service`).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        // Keep the explicit foreign key for clarity even though Eloquent would
        // detect it automatically from the column name.
        return $this->belongsTo(HomeService::class, 'home_service_id');
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors (Virtual Attributes)
     |--------------------------------------------------------------------------
     | These provide backwards-compatibility with existing blades/controllers
     | that expect generic names across booking types (booking_date, booking_time, status)
     */

    /**
     * Get the booking_date attribute (alias for `date`).
     */
    public function getBookingDateAttribute()
    {
        return $this->date; // already cast to Carbon instance
    }

    /**
     * Get the booking_time attribute (alias for `start_time`).
     */
    public function getBookingTimeAttribute()
    {
        return $this->start_time;
    }

    /**
     * Alias for booking_status to unify naming across blades.
     */
    public function getStatusAttribute()
    {
        return $this->booking_status;
    }

    /**
     * Ensure the virtual attributes are serialized.
     */
    protected $appends = [
        'booking_date',
        'booking_time',
        'status',
    ];
} 