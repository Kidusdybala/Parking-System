<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('client.profile.wallet', compact('user'));
    }

    public function addBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $user->balance += $request->amount;
        $user->save();

        return redirect()->back()->with('success', 'Balance added successfully!');
    }

    public function makePayment(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
    ]);

    $user = Auth::user();

    if ($user->balance < $request->amount) {
        return redirect()->back()->with('error', 'Insufficient balance.');
    }

    // Deduct balance
    $user->balance -= $request->amount;
    $user->save();

    // Generate receipt data
    $receiptData = [
        'user' => $user->name,
        'email' => $user->email,
        'amount' => $request->amount,
        'date' => now()->format('Y-m-d H:i:s'),
    ];

    // Store receipt data in session to access it in view
    return redirect()->route('receipt')->with('receiptData', $receiptData);
}

}
