<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'section_id',
        'price_per_hour',
        'is_reserved',
        'is_occupied',
    ];

    protected $casts = [
        'is_reserved' => 'boolean',
        'is_occupied' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class, 'parking_spot_id')
            ->whereNull('left_at')
            ->latest();
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'parking_spot_id');
    }
}

