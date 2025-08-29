<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'parking_spot_id' => $this->parking_spot_id,
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'total_cost' => $this->total_cost,
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Legacy fields for backward compatibility
            'reserved_at' => $this->reserved_at?->toISOString(),
            'parked_at' => $this->parked_at?->toISOString(),
            'left_at' => $this->left_at?->toISOString(),
            'total_price' => $this->total_price,
            'is_paid' => $this->is_paid,
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'parking_spot' => new ParkingSpotResource($this->whenLoaded('parkingSpot')),
            
            // Computed fields
            'duration_hours' => $this->when($this->start_time && $this->end_time, function () {
                return $this->getDurationInHours();
            }),
            'is_ongoing' => $this->when($this->start_time && $this->end_time, function () {
                return $this->isOngoing();
            }),
            'has_started' => $this->when($this->start_time, function () {
                return $this->hasStarted();
            }),
            'has_ended' => $this->when($this->end_time, function () {
                return $this->hasEnded();
            }),
        ];
    }
}