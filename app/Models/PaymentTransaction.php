<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'payment_id',
        'transaction_id',
        'type',
        'amount',
        'status',
        'transaction_data',
    ];
    
    protected $casts = [
        'transaction_data' => 'array',
    ];
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
