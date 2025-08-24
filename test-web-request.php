<?php

$txRef = 'TX_LKPIA4PU2Q_1756043910';
$url = "http://127.0.0.1:8000/payment-success?type=wallet&tx_ref=" . urlencode($txRef);

echo "Testing web request to: $url\n\n";

// Use cURL to make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Test Script');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "✅ HTTP Response Code: $httpCode\n";
    
    if ($httpCode == 200) {
        // Look for debug information in the response
        if (strpos($response, '<!-- Debug:') !== false) {
            preg_match('/<!-- Debug: (.*?) -->/', $response, $matches);
            if (isset($matches[1])) {
                echo "🔍 Debug Info Found: " . $matches[1] . "\n";
            }
        }
        
        // Check if transaction data is present
        if (strpos($response, '— ETB') !== false) {
            echo "❌ Transaction data is missing (showing — ETB)\n";
        } else {
            echo "✅ Transaction data appears to be present\n";
        }
        
        // Look for the amount in the response
        if (preg_match('/(\d+\.?\d*)\s+ETB/', $response, $matches)) {
            echo "💰 Amount found: " . $matches[1] . " ETB\n";
        }
        
        // Look for transaction reference
        if (strpos($response, $txRef) !== false) {
            echo "✅ Transaction reference found in response\n";
        } else {
            echo "❌ Transaction reference NOT found in response\n";
        }
        
    } else {
        echo "❌ HTTP Error: $httpCode\n";
        echo "Response: " . substr($response, 0, 500) . "\n";
    }
}

echo "\nTest completed.\n";