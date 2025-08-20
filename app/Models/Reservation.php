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
        'status',
        'reserved_at',
        'parked_at',
        'left_at',
        'total_price',
        'is_paid',
        'start_time',
        'end_time',
        'total_cost',
        'reservation_expires_at',
        'actual_start_time',
        'actual_end_time',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'parked_at' => 'datetime',
        'left_at' => 'datetime',
        'total_price' => 'decimal:2',
        'is_paid' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_cost' => 'decimal:2',
        'reservation_expires_at' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
    ];

    // Define possible statuses
    const STATUS_RESERVED = 'reserved';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FREE = 'free';

    /**
     * Get the user that owns the reservation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parking spot that belongs to the reservation.
     */
    public function parkingSpot()
    {
        return $this->belongsTo(ParkingSpot::class);
    }

    /**
     * Check if the reservation is reserved.
     */
    public function isReserved()
    {
        return $this->status === self::STATUS_RESERVED;
    }

    /**
     * Check if the reservation is active.
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the reservation is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the reservation is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if the reservation is free (completed).
     */
    public function isFree()
    {
        return $this->status === self::STATUS_FREE;
    }

    /**
     * Check if the reservation has started (parked).
     */
    public function hasStarted()
    {
        return !is_null($this->parked_at);
    }

    /**
     * Check if the reservation has ended (left).
     */
    public function hasEnded()
    {
        return !is_null($this->left_at);
    }

    /**
     * Check if the reservation is currently ongoing.
     */
    public function isOngoing()
    {
        return $this->hasStarted() && !$this->hasEnded() && $this->isActive();
    }

    /**
     * Get the duration of the reservation in hours.
     */
    public function getDurationInHours()
    {
        if (!$this->parked_at || !$this->left_at) {
            return 0;
        }
        return Carbon::parse($this->parked_at)->diffInHours(Carbon::parse($this->left_at));
    }

    /**
     * Get the duration of the reservation in minutes.
     */
    public function getDurationInMinutes()
    {
        if (!$this->parked_at || !$this->left_at) {
            return 0;
        }
        return Carbon::parse($this->parked_at)->diffInMinutes(Carbon::parse($this->left_at));
    }

    /**
     * Calculate the total cost based on parking spot hourly rate.
     */
    public function calculateTotalCost()
    {
        if (!$this->parkingSpot || !$this->parked_at || !$this->left_at) {
            return 0;
        }

        $hours = $this->getDurationInHours();
        return $hours * $this->parkingSpot->price_per_hour;
    }

    /**
     * Legacy method for backward compatibility.
     */
    public function calculateTotalPrice()
    {
        // Flat base charge when parking starts
        $baseCharge = 10;
        // Per minute rate (30 ETB/hour → 0.5 ETB/minute)
        $perMinuteRate = 30 / 60;

        if (!$this->parked_at || !$this->left_at) {
            return $baseCharge;
        }

        // Total parking duration in full minutes
        $totalMinutes = Carbon::parse($this->parked_at)->diffInMinutes(Carbon::parse($this->left_at));

        // Apply per-minute charge for all minutes (including first)
        $timeBasedFee = round($totalMinutes * $perMinuteRate, 2);

        return $baseCharge + $timeBasedFee;
    }

    /**
     * Check if the reservation has expired (for legacy reservations).
     */
    public function isExpired()
    {
        return $this->reserved_at && Carbon::parse($this->reserved_at)->addMinutes(1)->isPast();
    }

    /**
     * Scope to get active reservations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get free (completed) reservations.
     */
    public function scopeFree($query)
    {
        return $query->where('status', self::STATUS_FREE);
    }

    /**
     * Scope to get completed reservations (legacy method).
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_FREE);
    }

    /**
     * Scope to get cancelled reservations (legacy method).
     */
    public function scopeCancelled($query)
    {
        return $query->whereRaw('1 = 0'); // No cancelled status in new structure
    }

    /**
     * Scope to get ongoing reservations.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->whereNotNull('parked_at')
                    ->whereNull('left_at');
    }
}

