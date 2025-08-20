# Parking System API Documentation

## Overview
This API provides JWT token-based authentication and comprehensive parking management functionality.

## Base URL
```
http://localhost:8000/api
```

## Authentication
The API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header:
```
Authorization: Bearer {your-token}
```

## Response Format
All API responses follow this format:
```json
{
    "success": true|false,
    "message": "Response message",
    "data": {}, // Response data (when applicable)
    "errors": {} // Validation errors (when applicable)
}
```

## Authentication Endpoints

### Register User
**POST** `/auth/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "client" // Optional: admin, client
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "client",
            "balance": 0
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Login
**POST** `/auth/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "client",
            "balance": 100.00
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Get Current User
**GET** `/auth/me`
*Requires Authentication*

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "client",
            "balance": 100.00,
            "email_verified_at": "2024-01-01T00:00:00.000000Z",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### Logout
**POST** `/auth/logout`
*Requires Authentication*

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Change Password
**POST** `/auth/change-password`
*Requires Authentication*

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

## Parking Spots Endpoints

### Get All Parking Spots
**GET** `/parking-spots`

**Query Parameters:**
- `status` (optional): Filter by status (available, occupied, maintenance)
- `location` (optional): Filter by location
- `per_page` (optional): Number of items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "spot_number": "A001",
                "name": "Spot A001",
                "location": "Building A - Level 1",
                "hourly_rate": 5.00,
                "status": "available",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "current_page": 1,
        "total": 50
    }
}
```

### Get Available Parking Spots
**GET** `/parking-spots/available/list`

**Query Parameters:**
- `start_time` (optional): Start time for availability check
- `end_time` (optional): End time for availability check
- `per_page` (optional): Number of items per page

### Get Recommended Spot
**GET** `/parking-spots/recommend/{userId}`
*Requires Authentication*

**Query Parameters:**
- `start_time` (optional): Preferred start time
- `end_time` (optional): Preferred end time

**Response:**
```json
{
    "success": true,
    "data": {
        "recommended_spot": {
            "id": 1,
            "spot_number": "A001",
            "name": "Spot A001",
            "location": "Building A - Level 1",
            "hourly_rate": 5.00,
            "status": "available"
        },
        "reason": "Based on your previous reservations",
        "requested_time": {
            "start_time": "2024-01-01T10:00:00.000000Z",
            "end_time": "2024-01-01T12:00:00.000000Z"
        }
    }
}
```

### Create Parking Spot (Admin Only)
**POST** `/parking-spots`
*Requires Authentication & Admin Role*

**Request Body:**
```json
{
    "spot_number": "A001",
    "name": "Spot A001",
    "location": "Building A - Level 1",
    "hourly_rate": 5.00,
    "status": "available"
}
```

## Reservations Endpoints

### Get User's Reservations
**GET** `/reservations`
*Requires Authentication*

**Query Parameters:**
- `status` (optional): Filter by status (active, completed, cancelled)
- `start_date` (optional): Filter by start date
- `end_date` (optional): Filter by end date
- `per_page` (optional): Number of items per page

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "parking_spot_id": 1,
                "start_time": "2024-01-01T10:00:00.000000Z",
                "end_time": "2024-01-01T12:00:00.000000Z",
                "total_cost": 10.00,
                "status": "active",
                "created_at": "2024-01-01T09:00:00.000000Z",
                "parking_spot": {
                    "id": 1,
                    "spot_number": "A001",
                    "location": "Building A - Level 1"
                }
            }
        ]
    }
}
```

### Create Reservation
**POST** `/reservations`
*Requires Authentication*

**Request Body:**
```json
{
    "parking_spot_id": 1,
    "start_time": "2024-01-01T10:00:00Z",
    "end_time": "2024-01-01T12:00:00Z"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Reservation created successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "parking_spot_id": 1,
        "start_time": "2024-01-01T10:00:00.000000Z",
        "end_time": "2024-01-01T12:00:00.000000Z",
        "total_cost": 10.00,
        "status": "active",
        "parking_spot": {
            "id": 1,
            "spot_number": "A001",
            "location": "Building A - Level 1"
        }
    }
}
```

### Cancel Reservation
**POST** `/reservations/{id}/cancel`
*Requires Authentication*

**Response:**
```json
{
    "success": true,
    "message": "Reservation cancelled successfully",
    "data": {
        "reservation": {
            "id": 1,
            "status": "cancelled"
        },
        "refund_amount": 10.00
    }
}
```

## User Management Endpoints

### Get All Users (Admin Only)
**GET** `/users`
*Requires Authentication & Admin Role*

**Query Parameters:**
- `role` (optional): Filter by role
- `search` (optional): Search by name or email
- `per_page` (optional): Number of items per page

### Add Balance to User Account
**POST** `/users/{id}/add-balance`
*Requires Authentication*

**Request Body:**
```json
{
    "amount": 50.00
}
```

**Response:**
```json
{
    "success": true,
    "message": "Balance added successfully",
    "data": {
        "old_balance": 100.00,
        "amount_added": 50.00,
        "new_balance": 150.00
    }
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Forbidden (403)
```json
{
    "success": false,
    "message": "Unauthorized. Admin access required."
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Resource not found"
}
```

## Rate Limiting
API requests are rate-limited to prevent abuse. Default limits:
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users

## Testing with cURL

### Register a new user:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get parking spots (with token):
```bash
curl -X GET http://localhost:8000/api/parking-spots \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create a reservation:
```bash
curl -X POST http://localhost:8000/api/reservations \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "parking_spot_id": 1,
    "start_time": "2024-01-01T10:00:00Z",
    "end_time": "2024-01-01T12:00:00Z"
  }'
```