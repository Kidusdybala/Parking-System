<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chapa Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for Chapa payment integration
    |
    */

    // API Configuration
    'public_key' => env('CHAPA_PUBLIC_KEY'),
    'secret_key' => env('CHAPA_SECRET_KEY'),
    'base_url' => env('CHAPA_BASE_URL', 'https://api.chapa.co/v1'),

    // Payment Page Customization
    'customization' => [
        'title' => env('CHAPA_TITLE', 'MikiPark Payment'),
        'description' => env('CHAPA_DESCRIPTION', 'After payment: Close this tab manually'),
        'logo' => env('CHAPA_LOGO_URL', null), // URL to your logo
    ],

    // Redirect Settings
    'redirect_delay' => env('CHAPA_REDIRECT_DELAY', 30), // Seconds to stay on Chapa success page (increased)
    'auto_redirect' => env('CHAPA_AUTO_REDIRECT', true), // Enable auto redirect to our custom page

    // Webhook Settings
    'webhook_tolerance' => env('CHAPA_WEBHOOK_TOLERANCE', 300), // 5 minutes tolerance for webhook timing

    // Test Mode Settings
    'test_mode' => env('CHAPA_TEST_MODE', true),
    'test_phone_numbers' => [
        'awash' => ['0900123456', '0900112233', '0900881111'],
        'telebirr' => ['0900123456', '0900112233', '0900881111'],
        'cbebirr' => ['0900123456', '0900112233', '0900881111'],
        'mpesa' => ['0700123456', '0700112233', '0700881111'],
    ],
];