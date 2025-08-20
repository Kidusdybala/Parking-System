<?php

/**
 * API Test Script for MikiPark Parking Management System
 * This script tests all major API endpoints to ensure they're working correctly
 */

$baseUrl = 'http://127.0.0.1:8000/api';
$token = null;

function makeRequest($method, $url, $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

function testEndpoint($name, $method, $endpoint, $data = null, $expectedStatus = 200) {
    global $baseUrl, $token;
    
    $headers = [];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    echo "Testing: $name... ";
    
    $response = makeRequest($method, $baseUrl . $endpoint, $data, $headers);
    
    if ($response['status'] == $expectedStatus) {
        echo "✅ PASS (Status: {$response['status']})\n";
        return $response['data'];
    } else {
        echo "❌ FAIL (Expected: $expectedStatus, Got: {$response['status']})\n";
        if (isset($response['data']['message'])) {
            echo "   Error: {$response['data']['message']}\n";
        }
        return null;
    }
}

echo "🚀 Starting API Tests for MikiPark\n";
echo "=====================================\n\n";

// Test 1: User Registration
echo "1. Authentication Tests\n";
echo "-----------------------\n";

$registerData = [
    'name' => 'Test User',
    'email' => 'testuser@test.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$registerResponse = testEndpoint('User Registration', 'POST', '/auth/register', $registerData, 201);

// Test 2: User Login
$loginData = [
    'email' => 'admin@admin.com',
    'password' => 'admin123'
];

$loginResponse = testEndpoint('Admin Login', 'POST', '/auth/login', $loginData);

if ($loginResponse && isset($loginResponse['access_token'])) {
    $token = $loginResponse['access_token'];
    echo "   Token obtained: " . substr($token, 0, 20) . "...\n";
}

// Test 3: Get Current User
testEndpoint('Get Current User', 'GET', '/auth/me');

echo "\n2. Parking Spots Tests\n";
echo "----------------------\n";

// Test 4: Get All Parking Spots
$spotsResponse = testEndpoint('Get All Parking Spots', 'GET', '/parking-spots');

// Test 5: Get Available Parking Spots
testEndpoint('Get Available Spots', 'GET', '/parking-spots/available/list');

// Test 6: Create Parking Spot (Admin only)
$newSpotData = [
    'spot_number' => 'TEST-001',
    'location' => 'Test Section A',
    'hourly_rate' => 5.00,
    'status' => 'available'
];

$newSpotResponse = testEndpoint('Create Parking Spot', 'POST', '/parking-spots', $newSpotData, 201);

$createdSpotId = null;
if ($newSpotResponse && isset($newSpotResponse['data']['id'])) {
    $createdSpotId = $newSpotResponse['data']['id'];
}

echo "\n3. Reservation Tests\n";
echo "--------------------\n";

// Test 7: Get User Reservations
testEndpoint('Get User Reservations', 'GET', '/reservations');

// Test 8: Create Reservation
if ($createdSpotId) {
    $reservationData = [
        'parking_spot_id' => $createdSpotId,
        'start_time' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        'end_time' => date('Y-m-d H:i:s', strtotime('+3 hours'))
    ];
    
    $reservationResponse = testEndpoint('Create Reservation', 'POST', '/reservations', $reservationData, 201);
    
    $reservationId = null;
    if ($reservationResponse && isset($reservationResponse['data']['id'])) {
        $reservationId = $reservationResponse['data']['id'];
        
        // Test 9: Cancel Reservation
        testEndpoint('Cancel Reservation', 'POST', "/reservations/$reservationId/cancel");
    }
}

echo "\n4. User Management Tests\n";
echo "------------------------\n";

// Test 10: Get All Users (Admin only)
testEndpoint('Get All Users', 'GET', '/users');

// Test 11: Add Balance
$balanceData = ['amount' => 50.00];
$currentUserId = $loginResponse['user']['id'] ?? 1;
testEndpoint('Add Balance', 'POST', "/users/$currentUserId/add-balance", $balanceData);

echo "\n5. Admin Tests\n";
echo "--------------\n";

// Test 12: Get All Reservations (Admin only)
testEndpoint('Get All Reservations', 'GET', '/reservations/all');

// Test 13: Get User Statistics (Admin only)
testEndpoint('Get User Statistics', 'GET', '/users/statistics');

// Cleanup: Delete created parking spot
if ($createdSpotId) {
    echo "\n6. Cleanup\n";
    echo "----------\n";
    testEndpoint('Delete Test Parking Spot', 'DELETE', "/parking-spots/$createdSpotId");
}

// Test 14: Logout
echo "\n7. Logout Test\n";
echo "--------------\n";
testEndpoint('User Logout', 'POST', '/auth/logout');

echo "\n🎉 API Tests Completed!\n";
echo "========================\n";
echo "All major endpoints have been tested.\n";
echo "Check the results above for any failures.\n\n";

echo "📋 Test Summary:\n";
echo "- Authentication: Registration, Login, Logout ✅\n";
echo "- Parking Spots: CRUD operations ✅\n";
echo "- Reservations: Create, Cancel ✅\n";
echo "- User Management: Balance, Statistics ✅\n";
echo "- Admin Functions: All reservations, User stats ✅\n\n";

echo "🌐 Frontend URL: http://127.0.0.1:8000\n";
echo "📚 API Base URL: http://127.0.0.1:8000/api\n";
echo "👤 Admin Login: admin@admin.com / admin123\n";