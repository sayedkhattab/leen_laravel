<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioServiceBooking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'studio_service_id',
        'customer_id',
        'seller_id',
        'employee_id',
        'date',
        'start_time',
        'payment_status',
        'booking_status',
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
     * Get the studio service that owns the booking.
     */
    public function studioService()
    {
        return $this->belongsTo(StudioService::class);
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
     * Alias for the related StudioService to mirror the `service` relation used
     * across booking types in the application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(StudioService::class, 'studio_service_id');
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors (Virtual Attributes)
     |--------------------------------------------------------------------------
     */

    public function getBookingDateAttribute()
    {
        return $this->date;
    }

    public function getBookingTimeAttribute()
    {
        return $this->start_time;
    }

    public function getStatusAttribute()
    {
        return $this->booking_status;
    }

    protected $appends = [
        'booking_date',
        'booking_time',
        'status',
    ];
} 