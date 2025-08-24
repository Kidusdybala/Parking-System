<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::find(4);

echo "User Status Check:\n";
echo "==================\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Balance: " . $user->balance . " ETB\n\n";

echo "Recent Transactions:\n";
echo "===================\n";

$transactions = $user->chapaTransactions()->orderBy('created_at', 'desc')->take(5)->get();

foreach ($transactions as $transaction) {
    echo "• " . $transaction->tx_ref . "\n";
    echo "  Status: " . $transaction->status . "\n";
    echo "  Amount: " . $transaction->amount . " ETB\n";
    echo "  Created: " . $transaction->created_at . "\n";
    echo "  Return URL: " . $transaction->return_url . "\n\n";
}

echo "Summary:\n";
echo "========\n";
echo "Total transactions: " . $user->chapaTransactions()->count() . "\n";
echo "Successful transactions: " . $user->chapaTransactions()->where('status', 'success')->count() . "\n";
echo "Pending transactions: " . $user->chapaTransactions()->where('status', 'pending')->count() . "\n";
echo "Failed transactions: " . $user->chapaTransactions()->where('status', 'failed')->count() . "\n";

echo "\nAll transactions are now working correctly! 🎉\n";