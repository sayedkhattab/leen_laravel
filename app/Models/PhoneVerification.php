<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'phone',
        'verification_code',
        'expires_at',
        'attempts',
        'verified',
        'type',
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];
    
    // Check if verification code is expired
    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }
    
    // Check if verification code is valid
    public function isValid($code)
    {
        return !$this->isExpired() && $this->verification_code === $code;
    }
    
    // Increment attempts
    public function incrementAttempts()
    {
        $this->attempts++;
        $this->save();
        
        return $this->attempts;
    }
    
    // Mark as verified
    public function markAsVerified()
    {
        $this->verified = true;
        $this->save();
        
        return $this;
    }
}
