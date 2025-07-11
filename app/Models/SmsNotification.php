<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsNotification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'phone',
        'message',
        'type',
        'status',
        'response_data',
    ];
    
    protected $casts = [
        'response_data' => 'array',
    ];
}
