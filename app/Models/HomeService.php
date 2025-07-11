<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seller_id',
        'category_id',
        'sub_category_id',
        'name',
        'gender',
        'service_details',
        'description',
        'employees',
        'price',
        'duration',
        'images',
        'booking_status',
        'discount',
        'percentage',
        'discount_percentage',
        'discounted_price',
        'points',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'employees' => 'array',
        'images' => 'array',
        'discount' => 'boolean',
    ];

    /**
     * Get the seller that owns the home service.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the category that owns the home service.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory that owns the home service.
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get the bookings for the home service.
     */
    public function bookings()
    {
        return $this->hasMany(HomeServiceBooking::class);
    }
} 