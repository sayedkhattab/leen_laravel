<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seller_id',
        'name',
        'phone',
        'status',
        'work_start_time',
        'work_end_time',
        'working_days',
        'position',
        'email',
        'photo',
        'experience_years',
        'specialization',
        'max_bookings_per_day',
        'is_available',
        'completed_bookings_count',
        'rating',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'working_days' => 'array',
        'experience_years' => 'integer',
        'max_bookings_per_day' => 'integer',
        'is_available' => 'boolean',
        'completed_bookings_count' => 'integer',
        'rating' => 'decimal:2',
        'work_start_time' => 'datetime:H:i',
        'work_end_time' => 'datetime:H:i',
    ];

    /**
     * Get the seller that owns the employee.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the home service bookings for the employee.
     */
    public function homeServiceBookings()
    {
        return $this->hasMany(HomeServiceBooking::class);
    }

    /**
     * Get the studio service bookings for the employee.
     */
    public function studioServiceBookings()
    {
        return $this->hasMany(StudioServiceBooking::class);
    }

    /**
     * Get the employee's full image URL.
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }

        return url('images/employees/' . $this->photo);
    }

    /**
     * Get the employee's availability status.
     */
    public function getIsAvailableTextAttribute()
    {
        return $this->is_available ? 'متاح' : 'غير متاح';
    }

    /**
     * Check if the employee is available at a specific date and time.
     */
    public function isAvailableAt($date, $time)
    {
        // التحقق من أن الموظف متاح بشكل عام
        if (!$this->is_available) {
            return false;
        }

        // التحقق من أن اليوم ضمن أيام العمل
        $dayOfWeek = date('l', strtotime($date));
        if (!in_array($dayOfWeek, $this->working_days ?? [])) {
            return false;
        }

        // التحقق من أن الوقت ضمن ساعات العمل
        $bookingTime = strtotime($time);
        $startTime = strtotime($this->work_start_time);
        $endTime = strtotime($this->work_end_time);

        if ($bookingTime < $startTime || $bookingTime > $endTime) {
            return false;
        }

        // التحقق من عدد الحجوزات في هذا اليوم
        $homeBookingsCount = $this->homeServiceBookings()
            ->whereDate('date', $date)
            ->count();
        
        $studioBookingsCount = $this->studioServiceBookings()
            ->whereDate('date', $date)
            ->count();
        
        $totalBookings = $homeBookingsCount + $studioBookingsCount;
        
        return $totalBookings < $this->max_bookings_per_day;
    }
} 