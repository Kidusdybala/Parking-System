<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingSpotResource extends JsonResource
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
            'spot_number' => $this->spot_number,
            'name' => $this->name,
            'location' => $this->location,
            'hourly_rate' => $this->hourly_rate,
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'current_reservation' => new ReservationResource($this->whenLoaded('currentReservation')),
            'reservations_count' => $this->when(isset($this->reservations_count), $this->reservations_count),
        ];
    }
}