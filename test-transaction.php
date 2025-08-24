<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChapaTransaction;
use App\Services\ChapaService;

$txRef = 'TX_H4JMNLWOAV_1756041050';

echo "Testing transaction: $txRef\n\n";

// Find transaction in database
$transaction = ChapaTransaction::where('tx_ref', $txRef)->with('user')->first();

if ($transaction) {
    echo "✅ Transaction found in database:\n";
    echo "  ID: " . $transaction->id . "\n";
    echo "  Status: " . $transaction->status . "\n";
    echo "  Amount: " . $transaction->amount . " " . $transaction->currency . "\n";
    echo "  User: " . $transaction->user->name . " (" . $transaction->user->email . ")\n";
    echo "  User Balance: " . $transaction->user->balance . " ETB\n";
    echo "  Created: " . $transaction->created_at . "\n";
    echo "  Paid At: " . ($transaction->paid_at ?? 'Not paid') . "\n\n";
    
    // Test verification if pending
    if ($transaction->status === 'pending') {
        echo "🔄 Transaction is pending, testing verification...\n";
        
        try {
            $chapaService = new ChapaService();
            $result = $chapaService->verifyPayment($txRef);
            
            echo "Verification result:\n";
            echo "  Status: " . $result['status'] . "\n";
            echo "  Message: " . ($result['message'] ?? 'No message') . "\n";
            
            if (isset($result['data'])) {
                echo "  Chapa Data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
            }
            
            // Refresh transaction
            $transaction = $transaction->fresh();
            echo "\nAfter verification:\n";
            echo "  Status: " . $transaction->status . "\n";
            echo "  User Balance: " . $transaction->user->balance . " ETB\n";
            echo "  Paid At: " . ($transaction->paid_at ?? 'Not paid') . "\n";
            
        } catch (Exception $e) {
            echo "❌ Verification failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "ℹ️ Transaction is not pending (status: " . $transaction->status . ")\n";
    }
    
} else {
    echo "❌ Transaction not found in database\n";
}

echo "\nTest completed.\n";