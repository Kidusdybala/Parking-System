<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChapaTransaction;
use App\Services\ChapaService;

$txRef = 'TX_Q47HXMPVVJ_1756044277';

echo "Processing pending transaction: $txRef\n";
echo "=====================================\n\n";

$transaction = ChapaTransaction::where('tx_ref', $txRef)->with('user')->first();

if ($transaction && $transaction->status === 'pending') {
    echo "📋 Transaction Details:\n";
    echo "   Status: " . $transaction->status . "\n";
    echo "   Amount: " . $transaction->amount . " ETB\n";
    echo "   User: " . $transaction->user->name . "\n";
    echo "   Balance before: " . $transaction->user->balance . " ETB\n\n";
    
    echo "🔄 Verifying with Chapa...\n";
    
    try {
        $chapaService = new ChapaService();
        $result = $chapaService->verifyPayment($txRef);
        
        echo "✅ Verification result: " . $result['status'] . "\n";
        
        if (isset($result['data']['data']['status'])) {
            echo "   Chapa status: " . $result['data']['data']['status'] . "\n";
        }
        
        $transaction = $transaction->fresh();
        echo "\n📊 After verification:\n";
        echo "   Transaction status: " . $transaction->status . "\n";
        echo "   User balance: " . $transaction->user->balance . " ETB\n";
        
        if ($transaction->status === 'success') {
            echo "\n🎉 Transaction successfully processed!\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Verification failed: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "Transaction not found or already processed.\n";
}

echo "\nProcessing completed.\n";