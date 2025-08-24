<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "CHAPA CONFIGURATION TEST\n";
echo "========================\n\n";

echo "📋 Current Chapa Settings:\n";
echo "   Title: " . config('chapa.customization.title') . "\n";
echo "   Description: " . config('chapa.customization.description') . "\n";
echo "   Logo URL: " . (config('chapa.customization.logo') ?: 'Not set') . "\n";
echo "   Redirect Delay: " . config('chapa.redirect_delay') . " seconds\n";
echo "   Auto Redirect: " . (config('chapa.auto_redirect') ? 'Enabled' : 'Disabled') . "\n";
echo "   Test Mode: " . (config('chapa.test_mode') ? 'Enabled' : 'Disabled') . "\n\n";

echo "🔧 Environment Variables:\n";
echo "   CHAPA_TITLE: " . env('CHAPA_TITLE') . "\n";
echo "   CHAPA_DESCRIPTION: " . env('CHAPA_DESCRIPTION') . "\n";
echo "   CHAPA_REDIRECT_DELAY: " . env('CHAPA_REDIRECT_DELAY') . "\n";
echo "   CHAPA_AUTO_REDIRECT: " . env('CHAPA_AUTO_REDIRECT') . "\n\n";

echo "⚙️ How to Control Chapa Page Behavior:\n";
echo "=====================================\n\n";

echo "1. 📝 STAY LONGER ON SUCCESS PAGE:\n";
echo "   Edit .env file: CHAPA_REDIRECT_DELAY=30\n";
echo "   (This keeps Chapa success page open for 30 seconds)\n\n";

echo "2. 🚫 DISABLE AUTO REDIRECT COMPLETELY:\n";
echo "   Edit .env file: CHAPA_AUTO_REDIRECT=false\n";
echo "   (Users must manually close Chapa tab)\n\n";

echo "3. 🎨 CUSTOMIZE CHAPA PAGE:\n";
echo "   CHAPA_TITLE=\"Your Custom Title\"\n";
echo "   CHAPA_DESCRIPTION=\"Your custom description\"\n";
echo "   CHAPA_LOGO_URL=\"https://yoursite.com/logo.png\"\n\n";

echo "4. 🔄 APPLY CHANGES:\n";
echo "   Run: php artisan config:clear\n";
echo "   Then test a new payment\n\n";

echo "✅ Configuration loaded successfully!\n";
echo "Next payment will use these settings.\n";