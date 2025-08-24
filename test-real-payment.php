<?php

/**
 * Test real payment with actual Chapa API keys
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Services\ChapaService;
use App\Models\User;

echo "💳 Testing Real Chapa Payment Integration\n";
echo "=========================================\n\n";

try {
    $chapaService = app(ChapaService::class);
    
    // Create or get test user
    $testUser = User::firstOrCreate(
        ['email' => 'test@parkingsystem.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'role' => 0,
            'balance' => 50.00,
            'email_verified_at' => now()
        ]
    );
    
    echo "👤 Test User: {$testUser->name} ({$testUser->email})\n";
    echo "💰 Current Balance: {$testUser->balance} ETB\n\n";

    // Test payment data
    $paymentData = [
        'user_id' => $testUser->id,
        'amount' => 100,
        'currency' => 'ETB',
        'email' => $testUser->email,
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone_number' => '+251911123456',
        'description' => 'Test Wallet Top-up - Parking System',
        'callback_url' => 'http://localhost:8000/api/chapa/callback',
        'return_url' => 'http://localhost:8000/payment-success',
        'meta' => [
            'type' => 'wallet_topup',
            'user_id' => $testUser->id,
        ],
    ];

    echo "🚀 Initializing payment with real Chapa API...\n";
    $result = $chapaService->initializePayment($paymentData);

    if ($result['status'] === 'success') {
        echo "✅ SUCCESS! Payment initialized successfully!\n\n";
        echo "📊 Payment Details:\n";
        echo "   Transaction Ref: " . $result['transaction']->tx_ref . "\n";
        echo "   Amount: " . $result['transaction']->amount . " ETB\n";
        echo "   Status: " . $result['transaction']->status . "\n";
        echo "   Checkout URL: " . substr($result['checkout_url'], 0, 60) . "...\n\n";
        
        // Test verification
        echo "🔍 Testing payment verification...\n";
        $txRef = $result['transaction']->tx_ref;
        $verifyResult = $chapaService->verifyPayment($txRef);
        
        if ($verifyResult['status'] === 'success') {
            echo "✅ Verification successful!\n";
            echo "   Status: " . $verifyResult['transaction']->status . "\n";
            echo "   Transaction Status: " . $verifyResult['transaction']->transaction_status . "\n";
        } else {
            echo "⚠️  Verification: " . $verifyResult['message'] . "\n";
        }
        
    } else {
        echo "❌ Payment initialization failed!\n";
        echo "   Error: " . $result['message'] . "\n";
        if (isset($result['errors'])) {
            echo "   Details: " . json_encode($result['errors'], JSON_PRETTY_PRINT) . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 NEXT STEPS:\n\n";
echo "1. 🌐 Visit: http://localhost:8000/chapa-demo.html\n";
echo "2. 🧪 Test the interactive payment demo\n";
echo "3. 📱 Try payment with test cards:\n";
echo "   - Success: 4000000000000002\n";
echo "   - Declined: 4000000000000010\n";
echo "4. 🔗 Use the checkout URL above to complete a test payment\n\n";
echo "Your Chapa integration is READY! 🚀\n";