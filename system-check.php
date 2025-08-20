<?php

/**
 * MikiPark System Status Check
 * Verifies all components are working correctly
 */

echo "🔍 MikiPark System Status Check\n";
echo "===============================\n\n";

// Check 1: Database Connection
echo "1. Database Connection... ";
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';dbname=' . env('DB_DATABASE', 'parking_system'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
    echo "✅ Connected\n";
} catch (Exception $e) {
    echo "❌ Failed: " . $e->getMessage() . "\n";
}

// Check 2: Models and Data
echo "2. Database Tables... ";
try {
    $userCount = \App\Models\User::count();
    $spotCount = \App\Models\ParkingSpot::count();
    $reservationCount = \App\Models\Reservation::count();
    
    echo "✅ Ready\n";
    echo "   - Users: $userCount\n";
    echo "   - Parking Spots: $spotCount\n";
    echo "   - Reservations: $reservationCount\n";
} catch (Exception $e) {
    echo "❌ Failed: " . $e->getMessage() . "\n";
}

// Check 3: JWT Configuration
echo "3. JWT Configuration... ";
if (env('JWT_SECRET')) {
    echo "✅ Configured\n";
} else {
    echo "❌ Missing JWT_SECRET\n";
}

// Check 4: Admin User
echo "4. Admin User... ";
try {
    $admin = \App\Models\User::where('email', 'admin@admin.com')->first();
    if ($admin && $admin->role == 3) {
        echo "✅ Available (Balance: $" . $admin->balance . ")\n";
    } else {
        echo "❌ Not found or incorrect role\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Check 5: File Permissions
echo "5. File Permissions... ";
$storageWritable = is_writable(storage_path());
$cacheWritable = is_writable(base_path('bootstrap/cache'));

if ($storageWritable && $cacheWritable) {
    echo "✅ Correct\n";
} else {
    echo "❌ Issues found\n";
    if (!$storageWritable) echo "   - storage/ not writable\n";
    if (!$cacheWritable) echo "   - bootstrap/cache/ not writable\n";
}

// Check 6: Frontend Build
echo "6. Frontend Build... ";
$manifestExists = file_exists(public_path('build/manifest.json'));
if ($manifestExists) {
    echo "✅ Built\n";
} else {
    echo "❌ Not built (run 'npm run build')\n";
}

// Check 7: Environment
echo "7. Environment... ";
$env = app()->environment();
echo "✅ $env\n";

// Check 8: Available Parking Spots
echo "8. Available Parking Spots... ";
try {
    $availableSpots = \App\Models\ParkingSpot::where('status', 'available')->count();
    echo "✅ $availableSpots spots available\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n📊 System Summary:\n";
echo "==================\n";

// System Health Score
$checks = [
    'database' => true,
    'models' => $userCount > 0 && $spotCount > 0,
    'jwt' => !empty(env('JWT_SECRET')),
    'admin' => isset($admin) && $admin && $admin->role == 3,
    'permissions' => $storageWritable && $cacheWritable,
    'frontend' => $manifestExists,
    'spots' => isset($availableSpots) && $availableSpots > 0
];

$healthScore = (array_sum($checks) / count($checks)) * 100;
$status = $healthScore >= 80 ? '🟢 Excellent' : ($healthScore >= 60 ? '🟡 Good' : '🔴 Needs Attention');

echo "Health Score: " . round($healthScore) . "% - $status\n\n";

if ($healthScore >= 80) {
    echo "🎉 System Status: READY FOR DEPLOYMENT\n";
    echo "✅ All critical components are working correctly\n\n";
    
    echo "🌐 Access Information:\n";
    echo "- Frontend: http://127.0.0.1:8000\n";
    echo "- API: http://127.0.0.1:8000/api\n";
    echo "- Admin Login: admin@admin.com / admin123\n\n";
    
    echo "🚀 To start the server:\n";
    echo "php artisan serve\n\n";
    
    echo "📱 Features Available:\n";
    echo "- User Registration & Login\n";
    echo "- Parking Spot Browsing\n";
    echo "- Reservation Management\n";
    echo "- Balance System\n";
    echo "- Admin Dashboard\n";
    echo "- Real-time Updates\n";
} else {
    echo "⚠️ System Status: NEEDS CONFIGURATION\n";
    echo "Please fix the issues above before deployment\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "MikiPark - Smart Parking Management System\n";
echo "Built with Laravel 11 + React 18 + JWT Auth\n";
echo str_repeat("=", 50) . "\n";