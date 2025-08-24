<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Test script to manually verify Chapa payments
 * 
 * Usage: php test-payment-verification.php TX_REFERENCE
 */

if ($argc < 2) {
    echo "Usage: php test-payment-verification.php TX_REFERENCE\n";
    echo "Example: php test-payment-verification.php TX_H4JMNLWOAV_1756041050\n";
    exit(1);
}

$txRef = $argv[1];
$secretKey = $_ENV['CHAPA_SECRET_KEY'] ?? '';
$baseUrl = $_ENV['CHAPA_BASE_URL'] ?? 'https://api.chapa.co/v1';

if (empty($secretKey)) {
    echo "Error: CHAPA_SECRET_KEY not found in .env file\n";
    exit(1);
}

echo "Testing payment verification for: $txRef\n";
echo "Using Chapa API: $baseUrl\n";
echo "Secret Key: " . substr($secretKey, 0, 10) . "...\n\n";

try {
    // Make verification request to Chapa
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $secretKey,
        'Content-Type' => 'application/json',
    ])->get($baseUrl . '/transaction/verify/' . $txRef);

    echo "Response Status: " . $response->status() . "\n";
    echo "Response Headers:\n";
    foreach ($response->headers() as $key => $values) {
        echo "  $key: " . implode(', ', $values) . "\n";
    }
    echo "\nResponse Body:\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";

    if ($response->successful()) {
        $data = $response->json();
        
        if ($data['status'] === 'success') {
            echo "✅ Payment verification successful!\n";
            echo "Transaction Status: " . ($data['data']['status'] ?? 'unknown') . "\n";
            echo "Amount: " . ($data['data']['amount'] ?? 'unknown') . "\n";
            echo "Currency: " . ($data['data']['currency'] ?? 'unknown') . "\n";
            echo "Method: " . ($data['data']['method'] ?? 'unknown') . "\n";
            
            // Now test our local API
            echo "\n--- Testing Local API ---\n";
            $localResponse = Http::get('http://localhost:8000/api/chapa/callback?tx_ref=' . urlencode($txRef));
            echo "Local API Status: " . $localResponse->status() . "\n";
            echo "Local API Response:\n";
            echo json_encode($localResponse->json(), JSON_PRETTY_PRINT) . "\n";
            
        } else {
            echo "❌ Payment verification failed\n";
            echo "Chapa Status: " . ($data['status'] ?? 'unknown') . "\n";
            echo "Message: " . ($data['message'] ?? 'No message') . "\n";
        }
    } else {
        echo "❌ HTTP request failed\n";
        echo "Error: " . $response->body() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n--- Testing Database Query ---\n";

// Bootstrap Laravel to check database
require_once 'bootstrap/app.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $transaction = App\Models\ChapaTransaction::where('tx_ref', $txRef)->first();
    
    if ($transaction) {
        echo "✅ Transaction found in database\n";
        echo "ID: " . $transaction->id . "\n";
        echo "Status: " . $transaction->status . "\n";
        echo "Amount: " . $transaction->amount . " " . $transaction->currency . "\n";
        echo "User ID: " . $transaction->user_id . "\n";
        echo "Created: " . $transaction->created_at . "\n";
        echo "Updated: " . $transaction->updated_at . "\n";
        echo "Paid At: " . ($transaction->paid_at ?? 'Not paid') . "\n";
        
        if ($transaction->user) {
            echo "User: " . $transaction->user->name . " (" . $transaction->user->email . ")\n";
            echo "User Balance: " . $transaction->user->balance . " ETB\n";
        }
        
    } else {
        echo "❌ Transaction not found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";