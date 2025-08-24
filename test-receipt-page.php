<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTING RECEIPT PAGE BUTTON\n";
echo "===============================\n\n";

// Test the receipt page URL
$baseUrl = config('app.url');
$receiptUrl = $baseUrl . '/payment-success';

echo "📋 Receipt Page Details:\n";
echo "   URL: " . $receiptUrl . "\n";
echo "   Expected Button: 'Go to Profile' (green button)\n";
echo "   Button HTML: <a class=\"btn success\" href=\"/profile\">\n\n";

echo "🔍 Checking if button CSS class exists:\n";
$viewContent = file_get_contents(resource_path('views/payment-receipt.blade.php'));

if (strpos($viewContent, 'btn success') !== false) {
    echo "   ✅ Button HTML found in template\n";
} else {
    echo "   ❌ Button HTML not found\n";
}

if (strpos($viewContent, 'Go to Profile') !== false) {
    echo "   ✅ Button text found in template\n";
} else {
    echo "   ❌ Button text not found\n";
}

if (strpos($viewContent, '.btn.success') !== false) {
    echo "   ✅ Button CSS class defined\n";
} else {
    echo "   ❌ Button CSS class not defined\n";
}

echo "\n🎯 TESTING STEPS:\n";
echo "=================\n";
echo "1. Visit: " . $receiptUrl . "\n";
echo "2. Look for green 'Go to Profile' button\n";
echo "3. Button should be next to other action buttons\n";
echo "4. Click should redirect to /profile\n\n";

echo "🔧 TROUBLESHOOTING:\n";
echo "===================\n";
echo "If button is missing:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Check browser developer tools for errors\n";
echo "3. Verify you're on the correct receipt page\n";
echo "4. Check if CSS is loading properly\n\n";

echo "💡 ALTERNATIVE ACCESS:\n";
echo "======================\n";
echo "Direct Profile Link: " . $baseUrl . "/profile\n";
echo "Payment Complete Page: " . $baseUrl . "/payment-complete\n\n";

echo "Ready to test! 🚀\n";