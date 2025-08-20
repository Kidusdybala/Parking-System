<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParkingSpot;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ParkingController extends Controller
{
    /**
     * Get all parking spots
     */
    public function index(Request $request)
    {
        $query = ParkingSpot::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $parkingSpots = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $parkingSpots
        ]);
    }

    /**
     * Get a specific parking spot
     */
    public function show($id)
    {
        $parkingSpot = ParkingSpot::find($id);

        if (!$parkingSpot) {
            return response()->json([
                'success' => false,
                'message' => 'Parking spot not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $parkingSpot
        ]);
    }

    /**
     * Get available parking spots
     */
    public function available(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'sometimes|date|after:now',
            'end_time' => 'sometimes|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $startTime = $request->start_time ? Carbon::parse($request->start_time) : now();
        $endTime = $request->end_time ? Carbon::parse($request->end_time) : now()->addHours(2);

        // Get spots that are not reserved during the requested time
        $availableSpots = ParkingSpot::where('status', 'available')
            ->whereDoesntHave('reservations', function ($query) use ($startTime, $endTime) {
                $query->where('status', 'active')
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->whereBetween('start_time', [$startTime, $endTime])
                          ->orWhereBetween('end_time', [$startTime, $endTime])
                          ->orWhere(function ($q2) use ($startTime, $endTime) {
                              $q2->where('start_time', '<=', $startTime)
                                 ->where('end_time', '>=', $endTime);
                          });
                    });
            })
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $availableSpots
        ]);
    }

    /**
     * Get recommended parking spot for a user
     */
    public function getRecommendedSpot($userId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'sometimes|date|after:now',
            'end_time' => 'sometimes|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $startTime = $request->start_time ? Carbon::parse($request->start_time) : now();
        $endTime = $request->end_time ? Carbon::parse($request->end_time) : now()->addHours(2);

        // Get user's reservation history to find preferred locations
        $userReservations = Reservation::where('user_id', $userId)
            ->with('parkingSpot')
            ->get();

        $preferredLocations = $userReservations->pluck('parkingSpot.location')
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(3);

        // Find available spots, prioritizing user's preferred locations
        $query = ParkingSpot::where('status', 'available')
            ->whereDoesntHave('reservations', function ($query) use ($startTime, $endTime) {
                $query->where('status', 'active')
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->whereBetween('start_time', [$startTime, $endTime])
                          ->orWhereBetween('end_time', [$startTime, $endTime])
                          ->orWhere(function ($q2) use ($startTime, $endTime) {
                              $q2->where('start_time', '<=', $startTime)
                                 ->where('end_time', '>=', $endTime);
                          });
                    });
            });

        // If user has preferred locations, prioritize them
        if ($preferredLocations->isNotEmpty()) {
            $recommendedSpot = $query->whereIn('location', $preferredLocations->toArray())
                ->orderByRaw("FIELD(location, '" . $preferredLocations->implode("','") . "')")
                ->first();
        }

        // If no preferred spot available, get any available spot
        if (!isset($recommendedSpot) || !$recommendedSpot) {
            $recommendedSpot = $query->orderBy('hourly_rate', 'asc')->first();
        }

        if (!$recommendedSpot) {
            return response()->json([
                'success' => false,
                'message' => 'No available parking spots found for the requested time'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'recommended_spot' => $recommendedSpot,
                'reason' => $preferredLocations->contains($recommendedSpot->location) 
                    ? 'Based on your previous reservations' 
                    : 'Best available option',
                'requested_time' => [
                    'start_time' => $startTime->toISOString(),
                    'end_time' => $endTime->toISOString()
                ]
            ]
        ]);
    }

    /**
     * Create a new parking spot (Admin only)
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'spot_number' => 'required|string|unique:parking_spots',
            'location' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'sometimes|in:available,occupied,maintenance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $parkingSpot = ParkingSpot::create([
            'spot_number' => $request->spot_number,
            'location' => $request->location,
            'hourly_rate' => $request->hourly_rate,
            'status' => $request->status ?? 'available',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parking spot created successfully',
            'data' => $parkingSpot
        ], 201);
    }

    /**
     * Update a parking spot (Admin only)
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $parkingSpot = ParkingSpot::find($id);

        if (!$parkingSpot) {
            return response()->json([
                'success' => false,
                'message' => 'Parking spot not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'spot_number' => 'sometimes|string|unique:parking_spots,spot_number,' . $id,
            'location' => 'sometimes|string|max:255',
            'hourly_rate' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:available,occupied,maintenance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $parkingSpot->update($request->only([
            'spot_number', 'location', 'hourly_rate', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Parking spot updated successfully',
            'data' => $parkingSpot
        ]);
    }

    /**
     * Delete a parking spot (Admin only)
     */
    public function destroy(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $parkingSpot = ParkingSpot::find($id);

        if (!$parkingSpot) {
            return response()->json([
                'success' => false,
                'message' => 'Parking spot not found'
            ], 404);
        }

        // Check if spot has active reservations
        $activeReservations = $parkingSpot->reservations()
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->count();

        if ($activeReservations > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete parking spot with active reservations'
            ], 400);
        }

        $parkingSpot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Parking spot deleted successfully'
        ]);
    }
}