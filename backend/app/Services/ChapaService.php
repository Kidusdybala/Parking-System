<?php

namespace App\Services;

use App\Models\ChapaTransaction;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChapaService
{
    private $secretKey;
    private $publicKey;
    private $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.chapa.secret_key');
        $this->publicKey = config('services.chapa.public_key');
        $this->baseUrl = config('services.chapa.base_url');
    }

    /**
     * Initialize a payment transaction
     *
     * @param array $paymentData
     * @return array
     */
    public function initializePayment(array $paymentData)
    {
        try {
            $txRef = $this->generateTransactionReference();
            
            // Build return URL and append tx_ref so the app can verify on return
            $baseReturnUrl = $paymentData['return_url'] ?? (config('app.url') . '/payment-success');
            $returnUrlWithTx = $baseReturnUrl . (str_contains($baseReturnUrl, '?') ? '&' : '?') . 'tx_ref=' . urlencode($txRef);
            
            // Option to disable return URL completely (users stay on Chapa page)
            if (!config('chapa.auto_redirect', true)) {
                $returnUrlWithTx = null;
            }

            $data = [
                'amount' => $paymentData['amount'],
                'currency' => $paymentData['currency'] ?? 'ETB',
                'email' => $paymentData['email'],
                'first_name' => $paymentData['first_name'],
                'last_name' => $paymentData['last_name'],
                'phone_number' => $paymentData['phone_number'] ?? null,
                'tx_ref' => $txRef,
                'callback_url' => $paymentData['callback_url'] ?? config('app.url') . '/api/chapa/callback',
                'return_url' => $returnUrlWithTx, // Will be null if auto_redirect is disabled
                'description' => $paymentData['description'] ?? 'Parking Payment',
                'meta' => $paymentData['meta'] ?? null,
                'customization' => [
                    'title' => config('chapa.customization.title', 'Parking Payment'),
                    'description' => $paymentData['description'] ?? config('chapa.customization.description', 'Secure payment'),
                    'logo' => config('chapa.customization.logo'), // You can set CHAPA_LOGO_URL in .env
                ]
            ];

            // Make API request to Chapa
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', $data);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Create transaction record in database
                $transaction = ChapaTransaction::create([
                    'user_id' => $paymentData['user_id'],
                    'reservation_id' => $paymentData['reservation_id'] ?? null,
                    'tx_ref' => $txRef,
                    'amount' => $paymentData['amount'],
                    'currency' => $paymentData['currency'] ?? 'ETB',
                    'status' => ChapaTransaction::STATUS_PENDING,
                    'first_name' => $paymentData['first_name'],
                    'last_name' => $paymentData['last_name'],
                    'email' => $paymentData['email'],
                    'phone_number' => $paymentData['phone_number'] ?? null,
                    'callback_url' => $data['callback_url'],
                    'return_url' => $data['return_url'],
                    'description' => $data['description'],
                    'meta_data' => $paymentData['meta'] ?? null,
                ]);

                return [
                    'status' => 'success',
                    'data' => $responseData['data'],
                    'transaction' => $transaction,
                    'checkout_url' => $responseData['data']['checkout_url'] ?? null,
                ];
            }

            Log::error('Chapa payment initialization failed', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment initialization failed',
                'error' => $response->json()['message'] ?? 'Unknown error',
            ];

        } catch (\Exception $e) {
            Log::error('Chapa payment initialization exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment initialization failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment transaction
     *
     * @param string $txRef
     * @return array
     */
    public function verifyPayment(string $txRef)
    {
        try {
            Log::info('Starting payment verification', ['tx_ref' => $txRef]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $txRef);

            Log::info('Chapa verification response', [
                'tx_ref' => $txRef,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Find the transaction in our database
                $transaction = ChapaTransaction::where('tx_ref', $txRef)->first();
                
                if (!$transaction) {
                    Log::error('Transaction not found in database', ['tx_ref' => $txRef]);
                    return [
                        'status' => 'error',
                        'message' => 'Transaction not found in database',
                    ];
                }

                Log::info('Found transaction in database', [
                    'tx_ref' => $txRef,
                    'current_status' => $transaction->status,
                    'chapa_status' => $data['status'] ?? 'unknown'
                ]);

                // Update transaction based on Chapa response
                if ($data['status'] === 'success' && isset($data['data'])) {
                    $chapaData = $data['data'];
                    
                    // Only update if transaction is still pending
                    if ($transaction->status === ChapaTransaction::STATUS_PENDING) {
                        $transaction->update([
                            'status' => ChapaTransaction::STATUS_SUCCESS,
                            'chapa_tx_ref' => $chapaData['tx_ref'] ?? null,
                            'payment_method' => $chapaData['method'] ?? 'mobile_money',
                            'paid_at' => now(),
                        ]);

                        Log::info('Transaction marked as successful', ['tx_ref' => $txRef]);

                        // Process successful payment
                        $this->processSuccessfulPayment($transaction);
                    } else {
                        Log::info('Transaction already processed', [
                            'tx_ref' => $txRef,
                            'status' => $transaction->status
                        ]);
                    }

                } elseif ($data['status'] === 'failed') {
                    if ($transaction->status === ChapaTransaction::STATUS_PENDING) {
                        $transaction->markAsFailed();
                        Log::info('Transaction marked as failed', ['tx_ref' => $txRef]);
                    }
                } else {
                    Log::warning('Unexpected Chapa response status', [
                        'tx_ref' => $txRef,
                        'chapa_status' => $data['status'] ?? 'unknown',
                        'response_data' => $data
                    ]);
                }

                return [
                    'status' => 'success',
                    'data' => $data,
                    'transaction' => $transaction->fresh(),
                ];
            }

            Log::error('Chapa verification request failed', [
                'tx_ref' => $txRef,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
                'error' => $response->json()['message'] ?? 'Unknown error',
            ];

        } catch (\Exception $e) {
            Log::error('Chapa payment verification exception', [
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process successful payment
     *
     * @param ChapaTransaction $transaction
     * @return void
     */
    private function processSuccessfulPayment(ChapaTransaction $transaction)
    {
        try {
            $user = $transaction->user;
            
            if ($transaction->reservation_id) {
                // This is a reservation payment
                $reservation = $transaction->reservation;
                if ($reservation) {
                    $reservation->update([
                        'is_paid' => true,
                        'total_price' => $transaction->amount,
                    ]);
                }
            } else {
                // This is a wallet top-up
                $user->increment('balance', $transaction->amount);
            }

            // Receipt creation removed - using transaction record as receipt
            Log::info('Payment processed successfully', [
                'transaction_id' => $transaction->id,
                'tx_ref' => $transaction->tx_ref,
                'amount' => $transaction->amount,
                'user_id' => $user->id,
                'type' => $transaction->reservation_id ? 'reservation' : 'wallet'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing successful payment', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate unique transaction reference
     *
     * @return string
     */
    private function generateTransactionReference()
    {
        return 'TX_' . strtoupper(Str::random(10)) . '_' . time();
    }

    /**
     * Handle webhook callback from Chapa
     *
     * @param array $webhookData
     * @return array
     */
    public function handleWebhook(array $webhookData)
    {
        try {
            Log::info('Processing Chapa webhook', ['webhook_data' => $webhookData]);
            
            $txRef = $webhookData['tx_ref'] ?? $webhookData['trx'] ?? null;
            
            if (!$txRef) {
                Log::error('Transaction reference not found in webhook data', ['webhook_data' => $webhookData]);
                return [
                    'status' => 'error',
                    'message' => 'Transaction reference not found in webhook data',
                ];
            }

            // Find the transaction in our database first
            $transaction = ChapaTransaction::where('tx_ref', $txRef)->first();
            
            if (!$transaction) {
                Log::error('Transaction not found for webhook', ['tx_ref' => $txRef]);
                return [
                    'status' => 'error',
                    'message' => 'Transaction not found in database',
                ];
            }

            // Process webhook data directly if it contains status information
            if (isset($webhookData['status'])) {
                $webhookStatus = strtolower($webhookData['status']);
                
                if ($webhookStatus === 'success' && $transaction->status === ChapaTransaction::STATUS_PENDING) {
                    $transaction->update([
                        'status' => ChapaTransaction::STATUS_SUCCESS,
                        'chapa_tx_ref' => $webhookData['chapa_tx_ref'] ?? $txRef,
                        'payment_method' => $webhookData['method'] ?? 'mobile_money',
                        'paid_at' => now(),
                    ]);

                    Log::info('Transaction marked as successful via webhook', ['tx_ref' => $txRef]);

                    // Process successful payment
                    $this->processSuccessfulPayment($transaction);
                    
                    return [
                        'status' => 'success',
                        'message' => 'Webhook processed successfully',
                        'transaction' => $transaction->fresh(),
                    ];
                    
                } elseif ($webhookStatus === 'failed' && $transaction->status === ChapaTransaction::STATUS_PENDING) {
                    $transaction->markAsFailed();
                    Log::info('Transaction marked as failed via webhook', ['tx_ref' => $txRef]);
                    
                    return [
                        'status' => 'success',
                        'message' => 'Webhook processed successfully',
                        'transaction' => $transaction->fresh(),
                    ];
                }
            }

            // If webhook doesn't contain status, verify with Chapa API
            Log::info('Webhook missing status, verifying with Chapa API', ['tx_ref' => $txRef]);
            return $this->verifyPayment($txRef);

        } catch (\Exception $e) {
            Log::error('Chapa webhook handling exception', [
                'webhook_data' => $webhookData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => 'Webhook handling failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction by reference
     *
     * @param string $txRef
     * @return ChapaTransaction|null
     */
    public function getTransaction(string $txRef)
    {
        return ChapaTransaction::where('tx_ref', $txRef)->first();
    }

    /**
     * Cancel a pending transaction
     *
     * @param string $txRef
     * @return array
     */
    public function cancelTransaction(string $txRef)
    {
        try {
            $transaction = ChapaTransaction::where('tx_ref', $txRef)->first();
            
            if (!$transaction) {
                return [
                    'status' => 'error',
                    'message' => 'Transaction not found',
                ];
            }

            if ($transaction->status !== ChapaTransaction::STATUS_PENDING) {
                return [
                    'status' => 'error',
                    'message' => 'Transaction cannot be cancelled',
                ];
            }

            $transaction->markAsCancelled();

            return [
                'status' => 'success',
                'message' => 'Transaction cancelled successfully',
                'transaction' => $transaction,
            ];

        } catch (\Exception $e) {
            Log::error('Transaction cancellation exception', [
                'tx_ref' => $txRef,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Transaction cancellation failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}