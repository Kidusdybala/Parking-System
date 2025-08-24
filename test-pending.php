<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChapaTransaction;
use App\Services\ChapaService;

echo "Looking for pending transactions...\n\n";

$pendingTransactions = ChapaTransaction::where('status', 'pending')
    ->with('user')
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get();

if ($pendingTransactions->count() > 0) {
    echo "Found " . $pendingTransactions->count() . " pending transactions:\n\n";
    
    foreach ($pendingTransactions as $transaction) {
        echo "Transaction: " . $transaction->tx_ref . "\n";
        echo "  Amount: " . $transaction->amount . " ETB\n";
        echo "  User: " . $transaction->user->name . "\n";
        echo "  User balance before: " . $transaction->user->balance . " ETB\n";
        
        try {
            $chapaService = new ChapaService();
            $result = $chapaService->verifyPayment($transaction->tx_ref);
            
            echo "  Verification status: " . $result['status'] . "\n";
            
            if ($result['status'] === 'success' && isset($result['data']['data']['status'])) {
                echo "  Chapa status: " . $result['data']['data']['status'] . "\n";
            }
            
            $transaction = $transaction->fresh();
            echo "  Transaction status after: " . $transaction->status . "\n";
            echo "  User balance after: " . $transaction->user->balance . " ETB\n";
            
        } catch (Exception $e) {
            echo "  ❌ Verification failed: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
} else {
    echo "No pending transactions found.\n";
}

echo "Test completed.\n";