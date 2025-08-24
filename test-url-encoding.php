<?php

$txRef = 'TX_C5OWLHXYSR_1756044338';

echo "Testing URL encoding issue...\n";
echo "============================\n\n";

// Test both URL formats
$urls = [
    "http://127.0.0.1:8000/payment-success?type=wallet&tx_ref=" . urlencode($txRef),
    "http://127.0.0.1:8000/payment-success?type=wallet&amp;tx_ref=" . urlencode($txRef)
];

foreach ($urls as $i => $url) {
    echo "Testing URL " . ($i + 1) . ": $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Code: $httpCode\n";
    
    if ($httpCode == 200) {
        // Check for debug info
        if (preg_match('/<!-- Debug: (.*?) -->/', $response, $matches)) {
            echo "  Debug Info: " . $matches[1] . "\n";
        }
        
        // Check for transaction data
        if (strpos($response, '— ETB') !== false) {
            echo "  ❌ Transaction data missing\n";
        } else if (preg_match('/(\d+\.?\d*)\s+ETB/', $response, $matches)) {
            echo "  ✅ Amount found: " . $matches[1] . " ETB\n";
        }
        
        // Check for transaction reference
        if (strpos($response, $txRef) !== false) {
            echo "  ✅ Transaction reference found\n";
        } else {
            echo "  ❌ Transaction reference NOT found\n";
        }
    }
    
    echo "\n";
}

echo "Test completed.\n";