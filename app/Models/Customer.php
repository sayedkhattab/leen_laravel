<?php

namespace App\Models;

use App\Traits\PhoneVerification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, PhoneVerification;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'phone_verified_at',
        'status',
        'image',
        'location',
        'last_latitude',
        'last_longitude',
        'last_location_update',
        'location_tracking_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_location_update' => 'datetime',
        'location_tracking_enabled' => 'boolean',
        'last_latitude' => 'decimal:7',
        'last_longitude' => 'decimal:7',
    ];

    /**
     * Get the home service bookings for the customer.
     */
    public function homeServiceBookings()
    {
        return $this->hasMany(HomeServiceBooking::class);
    }

    /**
     * Get the studio service bookings for the customer.
     */
    public function studioServiceBookings()
    {
        return $this->hasMany(StudioServiceBooking::class);
    }

    /**
     * Get the chat rooms for the customer.
     */
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    /**
     * Get the notifications for the customer.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Update customer location
     *
     * @param float $latitude
     * @param float $longitude
     * @return bool
     */
    public function updateLocation($latitude, $longitude)
    {
        return $this->update([
            'last_latitude' => $latitude,
            'last_longitude' => $longitude,
            'last_location_update' => now(),
        ]);
    }
} 