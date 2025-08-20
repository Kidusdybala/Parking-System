<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'spot_number',
        'name',
        'location',
        'price_per_hour',
        'hourly_rate', // Keep for backward compatibility
        'status',
        'is_reserved',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'price_per_hour' => 'decimal:2',
        'is_reserved' => 'boolean',
    ];

    /**
     * Get the reservations for the parking spot.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the current active reservation for the parking spot.
     */
    public function currentReservation()
    {
        return $this->hasOne(Reservation::class)
            ->where('status', 'active')
            ->whereNull('left_at'); // Active reservation that hasn't left yet
    }

    /**
     * Check if the parking spot is available (not reserved).
     */
    public function isAvailable()
    {
        return $this->status === 'available' && !$this->is_reserved;
    }

    /**
     * Check if the parking spot is available for a given time period (legacy method).
     */
    public function isAvailableForPeriod($startTime, $endTime)
    {
        return $this->isAvailable();
    }

    /**
     * Scope to get available parking spots.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to get occupied parking spots.
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    /**
     * Scope to get parking spots under maintenance.
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }
}

