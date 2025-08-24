<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChapaTransaction;
use App\Services\ChapaService;

$txRef = 'TX_LKPIA4PU2Q_1756043910';

echo "Testing new transaction: $txRef\n\n";

$transaction = ChapaTransaction::where('tx_ref', $txRef)->with('user')->first();

if ($transaction) {
    echo "✅ Transaction found:\n";
    echo "  Status: " . $transaction->status . "\n";
    echo "  Amount: " . $transaction->amount . " ETB\n";
    echo "  User: " . $transaction->user->name . "\n";
    echo "  User balance before: " . $transaction->user->balance . " ETB\n";
    echo "  Return URL: " . $transaction->return_url . "\n\n";
    
    if ($transaction->status === 'pending') {
        echo "🔄 Verifying payment...\n";
        
        try {
            $chapaService = new ChapaService();
            $result = $chapaService->verifyPayment($txRef);
            
            echo "Verification result: " . $result['status'] . "\n";
            
            if (isset($result['data']['data']['status'])) {
                echo "Chapa status: " . $result['data']['data']['status'] . "\n";
            }
            
            $transaction = $transaction->fresh();
            echo "Final status: " . $transaction->status . "\n";
            echo "User balance after: " . $transaction->user->balance . " ETB\n";
            
        } catch (Exception $e) {
            echo "❌ Verification failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "ℹ️ Transaction already processed (status: " . $transaction->status . ")\n";
    }
    
} else {
    echo "❌ Transaction not found\n";
}

echo "\nTest completed.\n";