<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ChapaService;
use App\Models\User;

echo "TESTING NO AUTO-REDIRECT CONFIGURATION\n";
echo "======================================\n\n";

echo "📋 Current Settings:\n";
echo "   Auto Redirect: " . (config('chapa.auto_redirect') ? 'Enabled' : 'Disabled') . "\n";
echo "   Title: " . config('chapa.customization.title') . "\n";
echo "   Description: " . config('chapa.customization.description') . "\n\n";

try {
    $user = User::find(4);
    $chapaService = new ChapaService();
    
    $paymentData = [
        'amount' => 75,
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
    
    echo "🧪 Testing Payment Initialization:\n";
    echo "   Amount: " . $paymentData['amount'] . " ETB\n";
    echo "   User: " . $paymentData['first_name'] . "\n\n";
    
    $result = $chapaService->initializePayment($paymentData);
    
    if ($result['status'] === 'success') {
        echo "✅ PAYMENT INITIALIZATION SUCCESSFUL!\n";
        echo "   Transaction ID: " . $result['transaction']->tx_ref . "\n";
        echo "   Checkout URL: " . $result['checkout_url'] . "\n\n";
        
        // Check if return_url was set to null
        $transaction = $result['transaction'];
        echo "🔍 Return URL Check:\n";
        echo "   Return URL: " . ($transaction->return_url ?: 'NULL (No auto redirect)') . "\n\n";
        
        if (!$transaction->return_url) {
            echo "🎯 SUCCESS: No return URL set!\n";
            echo "   Users will stay on Chapa's success page\n";
            echo "   They must manually close the tab or navigate away\n";
            echo "   Payment will still be processed via webhook\n\n";
        } else {
            echo "ℹ️  Return URL is still set - auto redirect enabled\n\n";
        }
        
        echo "🧪 Test this payment:\n";
        echo "   1. Open: " . $result['checkout_url'] . "\n";
        echo "   2. Use phone: 0900123456\n";
        echo "   3. Complete payment\n";
        echo "   4. You should stay on Chapa's success page!\n";
        
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