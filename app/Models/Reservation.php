<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parking_spot_id',
        'reserved_at',
        'parked_at',
        'left_at',
        'total_price',
        'is_paid',
        'status', // Added status field
    ];

    // Define possible statuses
    const STATUS_FREE = 'free';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAID = 'paid';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parkingSpot()
    {
        return $this->belongsTo(ParkingSpot::class);
    }

    // Check if the reservation has expired
    public function isExpired()
    {
        return $this->reserved_at && Carbon::parse($this->reserved_at)->addMinutes(1)->isPast();
    }

    public function calculateTotalPrice()
    {
        // Flat base charge when parking starts
        $baseCharge = 10
;
        // Per minute rate (30 ETB/hour → 0.5 ETB/minute)
        $perMinuteRate = 30 / 60;

        // Total parking duration in full minutes
        $totalMinutes = Carbon::parse($this->parked_at)->diffInMinutes(Carbon::parse($this->left_at));

        // Apply per-minute charge for all minutes (including first)
        $timeBasedFee = round($totalMinutes * $perMinuteRate, 2);

        $baseCharge = $baseCharge + $timeBasedFee;
        // Total = base + time-based
        return $baseCharge;
    }
}

