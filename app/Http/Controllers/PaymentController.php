<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentProof;
use App\Models\User;
use App\Services\ChapaService;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    private $chapaService;

    public function __construct(ChapaService $chapaService)
    {
        $this->chapaService = $chapaService;
    }

    public function showTopupPage()
    {
        $user = Auth::user();
        $recentTransactions = $user->chapaTransactions()->latest()->take(5)->get();
        
        return view('client.profile.topup', compact('user', 'recentTransactions'));
    }

    /**
     * Show enhanced payment options page with both Chapa and manual upload
     */
    public function showPaymentOptionsPage()
    {
        $user = Auth::user();
        $recentChapaTransactions = $user->chapaTransactions()->latest()->take(5)->get();
        $recentPaymentProofs = PaymentProof::where('user_id', $user->id)->latest()->take(5)->get();
        
        return view('client.profile.payment-options', compact('user', 'recentChapaTransactions', 'recentPaymentProofs'));
    }

    public function uploadPaymentProofPage()
    {
        return view('client.profile.payment_upload');
    }

    public function storePaymentProof(Request $request)
{
    $request->validate([
        'payment_screenshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Store the uploaded payment proof
    $imagePath = $request->file('payment_screenshot')->store('payment_proofs', 'public');

    // Create a new payment proof record with 'pending' status
    $payment = PaymentProof::create([
        'user_id' => Auth::id(), // Associate the proof with the logged-in client
        'image' => $imagePath,
        'status' => 'pending',
    ]);

    return redirect()->back()->with('success', 'Payment proof submitted successfully. Awaiting admin approval.');
}


public function viewPayments()
{
    // Fetch all pending payments for the admin to review
    $payments = PaymentProof::where('status', 'pending')->get();

    return view('admin.payments', compact('payments'));
}

public function approvePayment(Request $request, $id)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
    ]);

    $payment = PaymentProof::findOrFail($id);

    if ($payment->status !== 'pending') {
        return redirect()->back()->with('error', 'This payment has already been processed.');
    }

    // Update payment proof status
    $payment->update([
        'status' => 'approved',
        'amount' => $request->amount,
    ]);

    // Credit the user's balance
    $user = User::findOrFail($payment->user_id);
    $user->balance += $request->amount;
    $user->save();

    return redirect()->back()->with('success', 'Payment approved and balance updated.');
}


    public function rejectPayment($id)
    {
        $payment = PaymentProof::findOrFail($id);
        $payment->update(['status' => 'rejected']);

        return redirect()->back()->with('error', 'Payment rejected.');
    }
}





