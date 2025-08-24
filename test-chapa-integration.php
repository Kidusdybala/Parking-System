<?php

/**
 * Simple test script to verify Chapa integration
 * Run this after configuring your Chapa API keys
 * 
 * Usage: php test-chapa-integration.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Services\ChapaService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "🧪 Testing Chapa Integration\n";
echo "============================\n\n";

try {
    // Test 1: Check if service can be instantiated
    echo "1. Testing ChapaService instantiation...\n";
    $chapaService = app(ChapaService::class);
    echo "   ✅ ChapaService instantiated successfully\n\n";

    // Test 2: Check configuration
    echo "2. Checking configuration...\n";
    $secretKey = config('services.chapa.secret_key');
    $publicKey = config('services.chapa.public_key');
    $baseUrl = config('services.chapa.base_url');

    if (empty($secretKey)) {
        echo "   ❌ CHAPA_SECRET_KEY not configured\n";
    } else {
        echo "   ✅ CHAPA_SECRET_KEY configured\n";
    }

    if (empty($publicKey)) {
        echo "   ❌ CHAPA_PUBLIC_KEY not configured\n";
    } else {
        echo "   ✅ CHAPA_PUBLIC_KEY configured\n";
    }

    if (empty($baseUrl)) {
        echo "   ❌ CHAPA_BASE_URL not configured\n";
    } else {
        echo "   ✅ CHAPA_BASE_URL configured: $baseUrl\n";
    }
    echo "\n";

    // Test 3: Check database connectivity
    echo "3. Testing database...\n";
    $userCount = DB::table('users')->count();
    $transactionCount = DB::table('chapa_transactions')->count();
    echo "   ✅ Database connected - Users: $userCount, Transactions: $transactionCount\n\n";

    // Test 4: Test payment initialization (if keys are configured)
    if (!empty($secretKey) && !empty($publicKey)) {
        echo "4. Testing payment initialization...\n";
        
        // Create or get a test user
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 0,
                'balance' => 0,
                'email_verified_at' => now()
            ]
        );

        $paymentData = [
            'user_id' => $testUser->id,
            'amount' => 100,
            'currency' => 'ETB',
            'email' => $testUser->email,
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '+251911123456',
            'description' => 'Test Payment - Wallet Top-up',
            'callback_url' => 'http://localhost:8000/api/chapa/callback',
            'return_url' => 'http://localhost:8000/payment-success',
            'meta' => [
                'type' => 'wallet_topup',
                'user_id' => $testUser->id,
            ],
        ];

        $result = $chapaService->initializePayment($paymentData);

        if ($result['status'] === 'success') {
            echo "   ✅ Payment initialization successful!\n";
            echo "   💳 Transaction ID: " . $result['transaction']->tx_ref . "\n";
            echo "   🔗 Checkout URL: " . substr($result['checkout_url'], 0, 50) . "...\n\n";
            
            // Test verification
            echo "5. Testing payment verification...\n";
            $verifyResult = $chapaService->verifyPayment($result['transaction']->tx_ref);
            if ($verifyResult['status'] === 'success') {
                echo "   ✅ Payment verification endpoint working\n";
                echo "   📊 Status: " . $verifyResult['transaction']->status . "\n";
            } else {
                echo "   ⚠️  Payment verification returned: " . $verifyResult['message'] . "\n";
            }
        } else {
            echo "   ❌ Payment initialization failed: " . $result['message'] . "\n";
        }
    } else {
        echo "4. Skipping payment test - API keys not configured\n";
    }

    echo "\n🎉 Integration test completed!\n\n";

    if (empty($secretKey) || empty($publicKey)) {
        echo "⚡ Next Steps:\n";
        echo "1. Get your test API keys from https://dashboard.chapa.co\n";
        echo "2. Add them to your .env file:\n";
        echo "   CHAPA_SECRET_KEY=CHASECK_TEST-your_secret_key_here\n";
        echo "   CHAPA_PUBLIC_KEY=CHAPUBK_TEST-your_public_key_here\n";
        echo "3. Run this test again: php test-chapa-integration.php\n";
        echo "4. Start testing payments at: http://localhost:8000\n\n";
    } else {
        echo "🚀 Your Chapa integration is ready!\n";
        echo "   - API endpoints: http://localhost:8000/api/chapa/*\n";
        echo "   - Test with Postman or your frontend\n";
        echo "   - Check documentation: ./CHAPA-INTEGRATION.md\n\n";
    }

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}