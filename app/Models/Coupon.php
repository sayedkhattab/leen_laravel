<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'is_active',
        'starts_at',
        'expires_at',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
    
    public function discountApplications()
    {
        return $this->hasMany(DiscountApplication::class);
    }
    
    // Check if coupon is valid
    public function isValid()
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }
        
        // Check if expired
        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return false;
        }
        
        // Check if not started yet
        if ($this->starts_at && now()->lessThan($this->starts_at)) {
            return false;
        }
        
        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }
    
    // Calculate discount amount
    public function calculateDiscount($amount)
    {
        $discount = 0;
        
        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
            
            // Apply max discount if set
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = $this->max_discount_amount;
            }
        } else { // fixed amount
            $discount = $this->value;
            
            // Don't allow discount greater than order amount
            if ($discount > $amount) {
                $discount = $amount;
            }
        }
        
        return $discount;
    }
}
