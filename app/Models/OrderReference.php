<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReference extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'reference_id',
    ];

    /**
     * No timestamps needed for this model
     */
    public $timestamps = false;
    
    /**
     * Get the payment that owns the reference.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
} 