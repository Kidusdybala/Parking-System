<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChapaService;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChapaController extends Controller
{
    private $chapaService;

    public function __construct(ChapaService $chapaService)
    {
        $this->chapaService = $chapaService;
    }

    /**
     * Initialize wallet top-up payment
     */
    public function initializeWalletTopup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10|max:10000',
            'phone_number' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
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
            return response()->json([
                'status' => 'success',
                'message' => 'Payment initialized successfully',
                'data' => [
                    'checkout_url' => $result['checkout_url'],
                    'tx_ref' => $result['transaction']->tx_ref,
                    'amount' => $result['transaction']->amount,
                ],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'],
            'error' => $result['error'] ?? null,
        ], 400);
    }

    /**
     * Initialize reservation payment
     */
    public function initializeReservationPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservations,id',
            'phone_number' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = Auth::user();
        $reservation = Reservation::findOrFail($request->reservation_id);

        // Check if user owns the reservation
        if ($reservation->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to reservation',
            ], 403);
        }

        // Check if reservation is already paid
        if ($reservation->is_paid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reservation is already paid',
            ], 400);
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
            return response()->json([
                'status' => 'success',
                'message' => 'Payment initialized successfully',
                'data' => [
                    'checkout_url' => $result['checkout_url'],
                    'tx_ref' => $result['transaction']->tx_ref,
                    'amount' => $result['transaction']->amount,
                    'reservation_id' => $reservation->id,
                ],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'],
            'error' => $result['error'] ?? null,
        ], 400);
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Request $request, $txRef)
    {
        $result = $this->chapaService->verifyPayment($txRef);

        if ($result['status'] === 'success') {
            return response()->json([
                'status' => 'success',
                'message' => 'Payment verification successful',
                'data' => [
                    'transaction_status' => $result['transaction']->status,
                    'amount' => $result['transaction']->amount,
                    'paid_at' => $result['transaction']->paid_at,
                    'chapa_response' => $result['data'],
                ],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'],
            'error' => $result['error'] ?? null,
        ], 400);
    }

    /**
     * Handle Chapa webhook callback
     */
    public function handleCallback(Request $request)
    {
        try {
            Log::info('Chapa callback received', [
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'query' => $request->query(),
                'body' => $request->all(),
                'raw_body' => $request->getContent(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Handle both GET and POST requests
            $webhookData = $request->all();
            
            // If it's a GET request, it might be a return URL callback
            if ($request->isMethod('GET')) {
                $txRef = $request->get('tx_ref') ?? $request->get('trx');
                if ($txRef) {
                    Log::info('Processing GET callback as payment verification', ['tx_ref' => $txRef]);
                    $result = $this->chapaService->verifyPayment($txRef);
                } else {
                    Log::warning('GET callback received without transaction reference');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaction reference required',
                    ], 400);
                }
            } else {
                // POST request - process as webhook
                $result = $this->chapaService->handleWebhook($webhookData);
            }

            if ($result['status'] === 'success') {
                Log::info('Chapa callback processed successfully', [
                    'method' => $request->method(),
                    'result' => $result
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Callback processed successfully',
                ]);
            }

            Log::warning('Chapa callback processing failed', [
                'method' => $request->method(),
                'result' => $result
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Chapa callback handling failed', [
                'method' => $request->method(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Callback processing failed',
            ], 500);
        }
    }

    /**
     * Get user's transaction history
     */
    public function getTransactionHistory(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10);

        $transactions = $user->chapaTransactions()
            ->with('reservation.parkingSpot')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetails(Request $request, $txRef)
    {
        $user = Auth::user();
        $transaction = $user->chapaTransactions()
            ->with('reservation.parkingSpot')
            ->where('tx_ref', $txRef)
            ->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction,
        ]);
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
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found',
            ], 404);
        }

        $result = $this->chapaService->cancelTransaction($txRef);

        if ($result['status'] === 'success') {
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction cancelled successfully',
                'data' => $result['transaction'],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'],
        ], 400);
    }

    /**
     * Force verify a payment (for debugging)
     */
    public function forceVerifyPayment(Request $request, $txRef)
    {
        try {
            Log::info('Force verification requested', [
                'tx_ref' => $txRef,
                'user_id' => Auth::id()
            ]);

            $result = $this->chapaService->verifyPayment($txRef);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verification completed',
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Force verification failed', [
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}