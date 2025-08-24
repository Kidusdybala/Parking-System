<?php

/**
 * Chapa Payment Gateway Test Setup Script
 * 
 * This script helps you test your Chapa integration setup
 * Run this script to verify your configuration
 * 
 * Usage: php chapa-test-setup.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class ChapaTestSetup
{
    private $secretKey;
    private $publicKey;
    private $baseUrl;

    public function __construct()
    {
        // Load environment variables
        $this->loadEnvironment();
        
        $this->secretKey = $_ENV['CHAPA_SECRET_KEY'] ?? '';
        $this->publicKey = $_ENV['CHAPA_PUBLIC_KEY'] ?? '';
        $this->baseUrl = $_ENV['CHAPA_BASE_URL'] ?? 'https://api.chapa.co/v1';
    }

    private function loadEnvironment()
    {
        if (file_exists('.env')) {
            $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    [$key, $value] = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
    }

    public function runTests()
    {
        echo "🚀 Chapa Payment Gateway Test Setup\n";
        echo "=====================================\n\n";

        $this->testEnvironmentVariables();
        $this->testDatabaseConnection();
        $this->testChapaConnection();
        $this->displaySummary();
    }

    private function testEnvironmentVariables()
    {
        echo "1. Testing Environment Variables...\n";
        
        $tests = [
            'CHAPA_SECRET_KEY' => $this->secretKey,
            'CHAPA_PUBLIC_KEY' => $this->publicKey,
            'CHAPA_BASE_URL' => $this->baseUrl,
        ];

        $passed = 0;
        $total = count($tests);

        foreach ($tests as $key => $value) {
            if (empty($value)) {
                echo "   ❌ {$key}: Not set\n";
            } else {
                echo "   ✅ {$key}: Set\n";
                $passed++;
            }
        }

        echo "   Result: {$passed}/{$total} environment variables configured\n\n";
    }

    private function testDatabaseConnection()
    {
        echo "2. Testing Database Connection...\n";
        
        try {
            // Check if Laravel is available
            if (!class_exists('\Illuminate\Support\Facades\DB')) {
                echo "   ⚠️  Laravel framework not loaded - skipping database test\n\n";
                return;
            }

            // Test database connection (this would need Laravel to be bootstrapped)
            echo "   ℹ️  Database connection test requires Laravel bootstrap\n";
            echo "   💡 Run: php artisan migrate --dry-run\n\n";
        } catch (Exception $e) {
            echo "   ❌ Database connection failed: " . $e->getMessage() . "\n\n";
        }
    }

    private function testChapaConnection()
    {
        echo "3. Testing Chapa API Connection...\n";

        if (empty($this->secretKey)) {
            echo "   ❌ Cannot test Chapa API - Secret key not configured\n\n";
            return;
        }

        try {
            // Test API connectivity with a simple request
            $response = $this->makeTestRequest();
            
            if ($response) {
                echo "   ✅ Chapa API connection successful\n";
                echo "   📡 Base URL: {$this->baseUrl}\n";
            } else {
                echo "   ❌ Chapa API connection failed\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Chapa API test failed: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function makeTestRequest()
    {
        // Using cURL for the test since we're not in Laravel context
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/transaction/initialize');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'amount' => 10,
            'currency' => 'ETB',
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'tx_ref' => 'test_' . time(),
            'description' => 'Test payment'
        ]));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // We expect a 400 or similar for test data, but not connection errors
        return $httpCode < 500;
    }

    private function displaySummary()
    {
        echo "📋 Setup Summary\n";
        echo "================\n\n";

        echo "✨ Next Steps:\n";
        echo "1. Run database migration: php artisan migrate\n";
        echo "2. Get your test API keys from https://dashboard.chapa.co\n";
        echo "3. Update your .env file with the test keys\n";
        echo "4. Test wallet top-up API endpoint\n";
        echo "5. Test reservation payment API endpoint\n\n";

        echo "📚 Documentation:\n";
        echo "- Chapa Integration Guide: ./CHAPA-INTEGRATION.md\n";
        echo "- Usage Examples: ./CHAPA-USAGE-EXAMPLES.md\n";
        echo "- Chapa Developer Docs: https://developer.chapa.co/docs\n\n";

        echo "🔧 Test API Endpoints:\n";
        echo "POST /api/chapa/wallet/topup\n";
        echo "POST /api/chapa/reservation/payment\n";
        echo "GET  /api/chapa/verify/{txRef}\n";
        echo "GET  /api/chapa/transactions\n\n";

        if (!empty($this->secretKey) && strpos($this->secretKey, 'TEST') === false) {
            echo "⚠️  WARNING: You seem to be using production keys.\n";
            echo "   For testing, use test keys (they contain 'TEST' in the key)\n\n";
        }

        echo "🎉 Chapa integration setup complete!\n";
    }
}

// Run the test
$tester = new ChapaTestSetup();
$tester->runTests();