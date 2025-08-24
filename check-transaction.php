<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChapaTransaction;
use App\Models\User;

echo "CHECKING TRANSACTION STATUS\n";
echo "===========================\n\n";

// Get the latest transaction
$transaction = ChapaTransaction::where('tx_ref', 'TX_SXH1E4YRUX_1756046019')->first();

if ($transaction) {
    echo "📋 Transaction Details:\n";
    echo "   TX Ref: " . $transaction->tx_ref . "\n";
    echo "   Status: " . $transaction->status . "\n";
    echo "   Amount: " . $transaction->amount . " " . $transaction->currency . "\n";
    echo "   User ID: " . $transaction->user_id . "\n";
    echo "   Created: " . $transaction->created_at . "\n";
    echo "   Paid At: " . ($transaction->paid_at ?: 'Not paid') . "\n\n";
    
    $user = $transaction->user;
    if ($user) {
        echo "👤 User Details:\n";
        echo "   Name: " . $user->name . "\n";
        echo "   Email: " . $user->email . "\n";
        echo "   Current Balance: " . $user->balance . " ETB\n\n";
    }
    
    if ($transaction->status === 'pending') {
        echo "⚠️  ISSUE FOUND: Transaction is still PENDING\n";
        echo "   This means payment verification hasn't run\n";
        echo "   The user's balance hasn't been updated\n\n";
        
        echo "🔧 SOLUTION: Let's verify this payment manually\n";
        
        // Manual verification
        $chapaService = new App\Services\ChapaService();
        
        echo "   Verifying payment with Chapa...\n";
        $result = $chapaService->verifyPayment($transaction->tx_ref);
        
        if ($result['status'] === 'success') {
            echo "   ✅ Payment verification successful!\n";
            echo "   Transaction should now be updated\n\n";
            
            // Refresh transaction
            $transaction->refresh();
            $user->refresh();
            
            echo "📋 Updated Transaction Status:\n";
            echo "   Status: " . $transaction->status . "\n";
            echo "   Paid At: " . ($transaction->paid_at ?: 'Not paid') . "\n";
            echo "   User Balance: " . $user->balance . " ETB\n\n";
            
            if ($transaction->status === 'success') {
                echo "🎉 SUCCESS: Transaction processed and balance updated!\n";
            } else {
                echo "❌ Transaction still pending - check logs for errors\n";
            }
        } else {
            echo "   ❌ Payment verification failed: " . $result['message'] . "\n";
        }
    } else {
        echo "✅ Transaction status is: " . $transaction->status . "\n";
        echo "   Balance should be updated correctly\n";
    }
    
} else {
    echo "❌ Transaction not found with TX Ref: TX_SXH1E4YRUX_1756046019\n";
    
    // Show latest transactions
    echo "\n📋 Latest Transactions:\n";
    $latest = ChapaTransaction::latest()->take(5)->get();
    foreach ($latest as $tx) {
        echo "   " . $tx->tx_ref . " - " . $tx->status . " - " . $tx->amount . " ETB\n";
    }
}

echo "\nCheck completed.\n";