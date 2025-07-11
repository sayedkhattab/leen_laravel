<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'paid_amount',
        'is_partial',
        'deposit_percentage',
        'status',
        'reference_id',
        'user_id',
        'transaction_id',
        'payment_method',
        'payment_data',
    ];

    protected $casts = [
        'payment_data' => 'array',
        'is_partial' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
    
    // العلاقة مع الحجز (إذا كان الدفع مرتبطًا بحجز)
    public function homeBooking()
    {
        return $this->hasOne(HomeServiceBooking::class, 'payment_id');
    }
    
    public function studioBooking()
    {
        return $this->hasOne(StudioServiceBooking::class, 'payment_id');
    }
    
    // العلاقة مع معاملات الدفع
    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    
    // العلاقة مع تطبيقات الخصم
    public function discountApplications()
    {
        return $this->hasMany(DiscountApplication::class);
    }
    
    // الحصول على إجمالي الخصم
    public function getTotalDiscountAttribute()
    {
        return $this->discountApplications()->sum('discount_amount');
    }
    
    // الحصول على المبلغ النهائي بعد الخصم
    public function getFinalAmountAttribute()
    {
        return $this->amount - $this->getTotalDiscountAttribute();
    }
    
    // الحصول على المبلغ المتبقي للدفع
    public function getRemainingAmountAttribute()
    {
        $finalAmount = $this->getFinalAmountAttribute();
        return $finalAmount - $this->paid_amount;
    }
    
    // التحقق إذا كان الدفع مكتمل
    public function getIsFullyPaidAttribute()
    {
        return $this->getRemainingAmountAttribute() <= 0;
    }
    
    // تحديث حالة الدفع بناءً على المبلغ المدفوع
    public function updatePaymentStatus()
    {
        if ($this->getIsFullyPaidAttribute()) {
            $this->status = 'Paid';
        } else if ($this->paid_amount > 0) {
            $this->status = 'Partially_Paid';
        } else {
            $this->status = 'Pending';
        }
        
        $this->save();
        return $this->status;
    }
}
