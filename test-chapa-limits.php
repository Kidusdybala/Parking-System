<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "CHAPA API PARAMETER VALIDATION\n";
echo "==============================\n\n";

$title = config('chapa.customization.title');
$description = config('chapa.customization.description');
$redirectDelay = config('chapa.redirect_delay');

echo "📋 Current Settings:\n";
echo "   Title: '$title' (" . strlen($title) . " chars)\n";
echo "   Description: '$description' (" . strlen($description) . " chars)\n";
echo "   Redirect Delay: $redirectDelay seconds\n\n";

echo "✅ Chapa API Limits:\n";
echo "   Title: Max 16 characters - " . (strlen($title) <= 16 ? "✅ VALID" : "❌ TOO LONG") . "\n";
echo "   Description: Max 50 characters - " . (strlen($description) <= 50 ? "✅ VALID" : "❌ TOO LONG") . "\n";
echo "   Redirect Delay: 1-300 seconds - " . ($redirectDelay >= 1 && $redirectDelay <= 300 ? "✅ VALID" : "❌ INVALID") . "\n\n";

if (strlen($title) <= 16 && strlen($description) <= 50 && $redirectDelay >= 1 && $redirectDelay <= 300) {
    echo "🎉 ALL PARAMETERS ARE VALID!\n";
    echo "Payment initialization should work now.\n";
} else {
    echo "❌ SOME PARAMETERS NEED FIXING:\n";
    if (strlen($title) > 16) {
        echo "   - Title too long (max 16 chars)\n";
    }
    if (strlen($description) > 50) {
        echo "   - Description too long (max 50 chars)\n";
    }
    if ($redirectDelay < 1 || $redirectDelay > 300) {
        echo "   - Redirect delay out of range (1-300 seconds)\n";
    }
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "Testing payment initialization...\n\n";

// Test with a sample payment
use App\Services\ChapaService;
use App\Models\User;

try {
    $user = User::find(4);
    $chapaService = new ChapaService();
    
    $paymentData = [
        'amount' => 50,
        'currency' => 'ETB',
        'email' => $user->email,
        'first_name' => $user->name,
        'last_name' => '',
        'phone_number' => '0900123456',
        'user_id' => $user->id,
        'description' => 'Test wallet top-up',
        'return_url' => config('app.url') . '/payment-success?type=wallet',
        'meta' => [
            'type' => 'wallet_topup',
            'user_id' => $user->id
        ]
    ];
    
    echo "🧪 Test Payment Data:\n";
    echo "   Amount: " . $paymentData['amount'] . " ETB\n";
    echo "   User: " . $paymentData['first_name'] . "\n";
    echo "   Phone: " . $paymentData['phone_number'] . "\n\n";
    
    $result = $chapaService->initializePayment($paymentData);
    
    if ($result['status'] === 'success') {
        echo "✅ PAYMENT INITIALIZATION SUCCESSFUL!\n";
        echo "   Transaction ID: " . $result['transaction']->tx_ref . "\n";
        echo "   Checkout URL: " . $result['checkout_url'] . "\n";
        echo "\n🎯 The 400 error is now fixed!\n";
    } else {
        echo "❌ Payment initialization failed:\n";
        echo "   Error: " . $result['message'] . "\n";
        if (isset($result['error'])) {
            echo "   Details: " . $result['error'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";