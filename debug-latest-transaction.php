<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChapaTransaction;
use App\Services\ChapaService;

$txRef = 'TX_C5OWLHXYSR_1756044338';

echo "Debugging transaction: $txRef\n";
echo "================================\n\n";

// Check if transaction exists
$transaction = ChapaTransaction::where('tx_ref', $txRef)->with('user')->first();

if ($transaction) {
    echo "✅ Transaction found in database:\n";
    echo "  ID: " . $transaction->id . "\n";
    echo "  Status: " . $transaction->status . "\n";
    echo "  Amount: " . $transaction->amount . " ETB\n";
    echo "  User: " . $transaction->user->name . "\n";
    echo "  User Balance: " . $transaction->user->balance . " ETB\n";
    echo "  Return URL: " . $transaction->return_url . "\n";
    echo "  Created: " . $transaction->created_at . "\n\n";
    
    // Test verification if pending
    if ($transaction->status === 'pending') {
        echo "🔄 Transaction is pending, verifying...\n";
        
        try {
            $chapaService = new ChapaService();
            $result = $chapaService->verifyPayment($txRef);
            
            echo "Verification result: " . $result['status'] . "\n";
            
            if (isset($result['data']['data']['status'])) {
                echo "Chapa status: " . $result['data']['data']['status'] . "\n";
            }
            
            // Refresh transaction
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
    echo "❌ Transaction not found in database\n";
    
    // Check recent transactions
    echo "\nRecent transactions:\n";
    $recent = ChapaTransaction::where('user_id', 4)->orderBy('created_at', 'desc')->take(3)->get();
    foreach ($recent as $t) {
        echo "  - " . $t->tx_ref . " (" . $t->status . ") - " . $t->amount . " ETB\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Testing web request to receipt page...\n\n";

// Test the web request
$url = "http://127.0.0.1:8000/payment-success?type=wallet&tx_ref=" . urlencode($txRef);
echo "URL: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "HTTP Code: $httpCode\n";
    
    if ($httpCode == 200) {
        // Check for debug info
        if (preg_match('/<!-- Debug: (.*?) -->/', $response, $matches)) {
            echo "🔍 Debug Info: " . $matches[1] . "\n";
        }
        
        // Check for transaction data
        if (strpos($response, '— ETB') !== false) {
            echo "❌ Transaction data missing (showing — ETB)\n";
        } else if (preg_match('/(\d+\.?\d*)\s+ETB/', $response, $matches)) {
            echo "✅ Amount found: " . $matches[1] . " ETB\n";
        }
        
        // Check for transaction reference
        if (strpos($response, $txRef) !== false) {
            echo "✅ Transaction reference found\n";
        } else {
            echo "❌ Transaction reference NOT found\n";
        }
    }
}

echo "\nDebug completed.\n";