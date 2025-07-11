<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountApplication extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'payment_id',
        'coupon_id',
        'discount_amount',
        'discount_type',
        'discount_percentage',
    ];
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
