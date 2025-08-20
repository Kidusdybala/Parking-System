<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JWTAuthController;
use App\Http\Controllers\Api\ParkingController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UserController;



// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [JWTAuthController::class, 'register']);
    Route::post('/login', [JWTAuthController::class, 'login']);
});

// Public parking information
Route::get('/parking-spots', [ParkingController::class, 'index']);
Route::get('/parking-spots/{id}', [ParkingController::class, 'show']);
Route::get('/parking-spots/available/list', [ParkingController::class, 'available']);

// Protected routes (authentication required)
Route::middleware('auth:api')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::get('/me', [JWTAuthController::class, 'me']);
        Route::post('/logout', [JWTAuthController::class, 'logout']);
        Route::post('/refresh', [JWTAuthController::class, 'refresh']);
        Route::post('/change-password', [JWTAuthController::class, 'changePassword']);
    });

    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']); // Admin only
        Route::get('/statistics', [UserController::class, 'statistics']); // Admin only
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']); // Admin only
        Route::post('/{id}/add-balance', [UserController::class, 'addBalance']);
        Route::put('/{id}/password', [UserController::class, 'updatePassword']);
    });

    // Parking spot management routes
    Route::prefix('parking-spots')->group(function () {
        Route::post('/', [ParkingController::class, 'store']); // Admin only
        Route::put('/{id}', [ParkingController::class, 'update']); // Admin only
        Route::delete('/{id}', [ParkingController::class, 'destroy']); // Admin only
        Route::get('/recommend/{userId}', [ParkingController::class, 'getRecommendedSpot']);
    });

    // Reservation routes
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index']); // User's own reservations
        Route::get('/all', [ReservationController::class, 'all']); // Admin only - all reservations
        Route::get('/statistics', [ReservationController::class, 'statistics']); // Admin only
        Route::get('/{id}', [ReservationController::class, 'show']);
        Route::post('/', [ReservationController::class, 'store']);
        Route::put('/{id}', [ReservationController::class, 'update']);
        Route::post('/{id}/cancel', [ReservationController::class, 'cancel']);
        Route::post('/{id}/complete', [ReservationController::class, 'complete']); // Admin only
    });
});

// Legacy route for backward compatibility
Route::middleware('api')->group(function () {
    Route::get('/recommend-spot/{userId}', [ParkingController::class, 'getRecommendedSpot']);
});
