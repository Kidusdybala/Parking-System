<?php

namespace App\Http\Controllers;

use App\Services\ChapaService;
use App\Models\Reservation;
use App\Models\User;
use App\Models\ChapaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChapaWebController extends Controller
{
    private $chapaService;

    public function __construct(ChapaService $chapaService)
    {
        $this->chapaService = $chapaService;
    }

    /**
     * Show wallet top-up page
     */
    public function showWalletTopupPage()
    {
        $user = Auth::user();
        $recentTransactions = $user->chapaTransactions()
            ->latest()
            ->take(5)
            ->get();

        return view('client.payments.wallet-topup', compact('user', 'recentTransactions'));
    }

    /**
     * Process wallet top-up
     */
    public function processWalletTopup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10|max:10000',
            'phone_number' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        $paymentData = [
            'user_id' => $user->id,
            'amount' => $request->amount,
            'currency' => 'ETB',
            'email' => $user->email,
            'first_name' => explode(' ', $user->name)[0] ?? $user->name,
            'last_name' => explode(' ', $user->name, 2)[1] ?? '',
            'phone_number' => $request->phone_number,
            'description' => 'Wallet Top-up - Parking System',
            'callback_url' => config('app.url') . '/api/chapa/callback',
            'return_url' => config('app.url') . '/payment-success?type=wallet',
            'meta' => [
                'type' => 'wallet_topup',
                'user_id' => $user->id,
            ],
        ];

        $result = $this->chapaService->initializePayment($paymentData);

        if ($result['status'] === 'success') {
            // Store transaction reference in session
            session(['pending_tx_ref' => $result['transaction']->tx_ref]);
            
            // Redirect to Chapa checkout
            return redirect($result['checkout_url']);
        }

        return back()->with('error', 'Payment initialization failed: ' . $result['message']);
    }

    /**
     * Show reservation payment page
     */
    public function showReservationPaymentPage(Reservation $reservation)
    {
        $user = Auth::user();

        // Check if user owns the reservation
        if ($reservation->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access to reservation');
        }

        // Check if reservation is already paid
        if ($reservation->is_paid) {
            return redirect()->route('reservations.show', $reservation)
                ->with('info', 'This reservation is already paid.');
        }

        return view('client.payments.reservation-payment', compact('reservation', 'user'));
    }

    /**
     * Process reservation payment
     */
    public function processReservationPayment(Request $request, Reservation $reservation)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        // Check if user owns the reservation
        if ($reservation->user_id !== $user->id && !$user->isAdmin()) {
            return back()->with('error', 'Unauthorized access to reservation');
        }

        // Check if reservation is already paid
        if ($reservation->is_paid) {
            return back()->with('info', 'This reservation is already paid.');
        }

        // Calculate payment amount
        $amount = $reservation->total_cost ?? $reservation->calculateTotalPrice();

        $paymentData = [
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'amount' => $amount,
            'currency' => 'ETB',
            'email' => $user->email,
            'first_name' => explode(' ', $user->name)[0] ?? $user->name,
            'last_name' => explode(' ', $user->name, 2)[1] ?? '',
            'phone_number' => $request->phone_number,
            'description' => 'Parking Payment - Spot #' . $reservation->parkingSpot->spot_number,
            'callback_url' => config('app.url') . '/api/chapa/callback',
            'return_url' => config('app.url') . '/payment-success?type=reservation&id=' . $reservation->id,
            'meta' => [
                'type' => 'reservation_payment',
                'user_id' => $user->id,
                'reservation_id' => $reservation->id,
                'spot_id' => $reservation->parking_spot_id,
            ],
        ];

        $result = $this->chapaService->initializePayment($paymentData);

        if ($result['status'] === 'success') {
            // Store transaction reference in session
            session(['pending_tx_ref' => $result['transaction']->tx_ref]);
            
            // Redirect to Chapa checkout
            return redirect($result['checkout_url']);
        }

        return back()->with('error', 'Payment initialization failed: ' . $result['message']);
    }

    /**
     * Show payment receipt page with exit button
     */
    public function showPaymentReceipt(Request $request)
    {
        $type = $request->get('type', 'wallet');
        $txRef = session('pending_tx_ref') ?? $request->get('tx_ref') ?? $request->get('trx') ?? $request->get('amp;tx_ref');
        $id = $request->get('id');

        Log::info('Payment receipt page accessed', [
            'type' => $type,
            'tx_ref' => $txRef,
            'id' => $id,
            'session_tx_ref' => session('pending_tx_ref'),
            'query_params' => $request->query(),
            'user_id' => Auth::id()
        ]);

        $transaction = null;
        $user = Auth::user();

        // Try to find transaction by tx_ref in the database
        if ($txRef) {
            $transaction = ChapaTransaction::where('tx_ref', $txRef)
                ->with(['user', 'reservation.parkingSpot'])
                ->first();
            
            Log::info('Transaction lookup by tx_ref', [
                'tx_ref' => $txRef,
                'found' => $transaction ? true : false,
                'transaction_id' => $transaction->id ?? null
            ]);
            
            // If we found a transaction but no authenticated user, use the transaction's user
            if ($transaction && !$user) {
                $user = $transaction->user;
                Log::info('Using transaction user', ['user_id' => $user->id ?? null]);
            }
        }

        // If still no transaction and we have a user, get the most recent one
        if (!$transaction && $user) {
            $transaction = $user->chapaTransactions()
                ->with('reservation.parkingSpot')
                ->latest()
                ->first();
                
            Log::info('Using most recent transaction for user', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id ?? null,
                'tx_ref' => $transaction->tx_ref ?? null
            ]);
        }

        // If still no transaction, try to find by user ID and type
        if (!$transaction && $user && $type) {
            $query = $user->chapaTransactions()->with('reservation.parkingSpot');
            
            if ($type === 'wallet') {
                $query->whereNull('reservation_id');
            } elseif ($type === 'reservation' && $id) {
                $query->where('reservation_id', $id);
            }
            
            $transaction = $query->latest()->first();
            
            Log::info('Transaction lookup by type', [
                'type' => $type,
                'user_id' => $user->id,
                'reservation_id' => $id,
                'found' => $transaction ? true : false,
                'tx_ref' => $transaction->tx_ref ?? null
            ]);
        }

        // Verify payment status with Chapa if transaction is pending
        if ($transaction && $transaction->status === 'pending') {
            try {
                Log::info('Verifying payment status for transaction', ['tx_ref' => $transaction->tx_ref]);
                
                // Use the Chapa service to verify payment
                $verificationResult = $this->chapaService->verifyPayment($transaction->tx_ref);
                
                if ($verificationResult['status'] === 'success') {
                    // Payment verification was successful, transaction should be updated
                    $transaction = $transaction->fresh();
                    Log::info('Payment verification successful', [
                        'tx_ref' => $transaction->tx_ref,
                        'status' => $transaction->status
                    ]);
                } else {
                    Log::warning('Payment verification failed', [
                        'tx_ref' => $transaction->tx_ref,
                        'error' => $verificationResult['message'] ?? 'Unknown error'
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('Error verifying payment on receipt page', [
                    'transaction_id' => $transaction->id ?? null,
                    'tx_ref' => $transaction->tx_ref ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $receiptData = [
            'transaction' => $transaction,
            'type' => $type,
            'reservation_id' => $id,
            'success' => $transaction && $transaction->isSuccessful(),
            'user' => $user,
            'show_login_reminder' => !Auth::check() && $user,
        ];

        Log::info('Payment receipt data prepared', [
            'has_transaction' => $transaction ? true : false,
            'transaction_id' => $transaction->id ?? null,
            'transaction_status' => $transaction->status ?? null,
            'transaction_amount' => $transaction->amount ?? null,
            'user_id' => $user->id ?? null,
            'success' => $receiptData['success'],
            'receipt_data_keys' => array_keys($receiptData)
        ]);

        // Payment receipt data is ready

        return view('payment-receipt', $receiptData);
    }

    /**
     * Handle successful payment return (legacy method)
     */
    public function handlePaymentSuccess(Request $request)
    {
        // Redirect to new receipt page
        return redirect()->route('chapa.success', $request->all());
    }

    /**
     * Handle Chapa callback (for webhooks)
     */
    public function handleCallback(Request $request)
    {
        // This is handled by the API controller, but we can also handle web callbacks here
        return response('OK');
    }

    /**
     * Show payment history
     */
    public function showPaymentHistory(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);

        $transactions = $user->chapaTransactions()
            ->with('reservation.parkingSpot')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('client.payments.history', compact('transactions'));
    }

    /**
     * Show transaction details
     */
    public function showTransactionDetails($txRef)
    {
        $user = Auth::user();
        $transaction = $user->chapaTransactions()
            ->with('reservation.parkingSpot')
            ->where('tx_ref', $txRef)
            ->first();

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        return view('client.payments.transaction-details', compact('transaction'));
    }

    /**
     * Cancel a pending transaction
     */
    public function cancelTransaction(Request $request, $txRef)
    {
        $user = Auth::user();
        $transaction = $user->chapaTransactions()
            ->where('tx_ref', $txRef)
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Transaction not found');
        }

        $result = $this->chapaService->cancelTransaction($txRef);

        if ($result['status'] === 'success') {
            return back()->with('success', 'Transaction cancelled successfully');
        }

        return back()->with('error', $result['message']);
    }
}