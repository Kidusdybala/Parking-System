# Chapa Payment Integration - Usage Examples

This document provides practical examples of how to use the Chapa payment integration in your frontend application.

## Frontend Implementation Examples

### 1. JavaScript/React Example for Wallet Top-up

```javascript
// Example: Wallet top-up using fetch API
async function initializeWalletTopup(amount, phoneNumber) {
    try {
        const token = localStorage.getItem('auth_token');
        
        const response = await fetch('/api/chapa/wallet/topup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                amount: amount,
                phone_number: phoneNumber
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Redirect user to Chapa checkout page
            window.location.href = result.data.checkout_url;
        } else {
            alert('Payment initialization failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing payment');
    }
}

// Usage
const topupButton = document.getElementById('topup-btn');
topupButton.addEventListener('click', () => {
    const amount = document.getElementById('amount').value;
    const phone = document.getElementById('phone').value;
    initializeWalletTopup(amount, phone);
});
```

### 2. React Component Example

```jsx
import React, { useState } from 'react';
import axios from 'axios';

const WalletTopup = () => {
    const [amount, setAmount] = useState('');
    const [phone, setPhone] = useState('');
    const [loading, setLoading] = useState(false);

    const handleTopup = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const token = localStorage.getItem('auth_token');
            const response = await axios.post('/api/chapa/wallet/topup', {
                amount: parseFloat(amount),
                phone_number: phone
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.data.status === 'success') {
                // Redirect to Chapa checkout
                window.location.href = response.data.data.checkout_url;
            }
        } catch (error) {
            alert('Payment failed: ' + error.response.data.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleTopup}>
            <div>
                <label>Amount (ETB):</label>
                <input
                    type="number"
                    min="10"
                    max="10000"
                    value={amount}
                    onChange={(e) => setAmount(e.target.value)}
                    required
                />
            </div>
            <div>
                <label>Phone Number:</label>
                <input
                    type="tel"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="+251911123456"
                />
            </div>
            <button type="submit" disabled={loading}>
                {loading ? 'Processing...' : 'Top Up Wallet'}
            </button>
        </form>
    );
};

export default WalletTopup;
```

### 3. Reservation Payment Example

```javascript
async function payReservation(reservationId, phoneNumber) {
    try {
        const token = localStorage.getItem('auth_token');
        
        const response = await fetch('/api/chapa/reservation/payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                reservation_id: reservationId,
                phone_number: phoneNumber
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Store transaction reference for later verification
            localStorage.setItem('pending_tx_ref', result.data.tx_ref);
            
            // Redirect to Chapa checkout
            window.location.href = result.data.checkout_url;
        } else {
            alert('Payment initialization failed: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

### 4. Payment Verification Example

```javascript
async function verifyPayment(txRef) {
    try {
        const token = localStorage.getItem('auth_token');
        
        const response = await fetch(`/api/chapa/verify/${txRef}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const result = await response.json();

        if (result.status === 'success') {
            if (result.data.transaction_status === 'success') {
                // Update UI to reflect successful payment
                updatePaymentStatus('success');
            } else if (result.data.transaction_status === 'pending') {
                alert('Payment is still processing...');
            } else {
                alert('Payment failed');
                updatePaymentStatus('failed');
            }
        }
    } catch (error) {
        console.error('Error verifying payment:', error);
    }
}

// Usage after redirect from Chapa
const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');
const txRef = localStorage.getItem('pending_tx_ref');

if (status && txRef) {
    verifyPayment(txRef);
    localStorage.removeItem('pending_tx_ref');
}
```

## PHP/Laravel Backend Examples

### 1. Custom Payment Controller (Alternative approach)

```php
<?php

namespace App\Http\Controllers;

use App\Services\ChapaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomPaymentController extends Controller
{
    private $chapaService;

    public function __construct(ChapaService $chapaService)
    {
        $this->chapaService = $chapaService;
    }

    public function processWalletTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000'
        ]);

        $user = Auth::user();
        
        // Initialize payment
        $paymentData = [
            'user_id' => $user->id,
            'amount' => $request->amount,
            'email' => $user->email,
            'first_name' => explode(' ', $user->name)[0],
            'last_name' => explode(' ', $user->name, 2)[1] ?? '',
            'description' => 'Wallet Top-up',
        ];

        $result = $this->chapaService->initializePayment($paymentData);

        if ($result['status'] === 'success') {
            return redirect($result['checkout_url']);
        }

        return back()->with('error', 'Payment initialization failed');
    }
}
```

### 2. Artisan Command for Payment Verification

```php
<?php

namespace App\Console\Commands;

use App\Models\ChapaTransaction;
use App\Services\ChapaService;
use Illuminate\Console\Command;

class VerifyPendingPayments extends Command
{
    protected $signature = 'chapa:verify-pending';
    protected $description = 'Verify pending Chapa payments';

    private $chapaService;

    public function __construct(ChapaService $chapaService)
    {
        parent::__construct();
        $this->chapaService = $chapaService;
    }

    public function handle()
    {
        $pendingTransactions = ChapaTransaction::where('status', 'pending')
            ->where('created_at', '>', now()->subHours(24))
            ->get();

        foreach ($pendingTransactions as $transaction) {
            $this->info("Verifying transaction: {$transaction->tx_ref}");
            
            $result = $this->chapaService->verifyPayment($transaction->tx_ref);
            
            if ($result['status'] === 'success') {
                $this->info("Transaction {$transaction->tx_ref} verified successfully");
            } else {
                $this->warn("Failed to verify transaction {$transaction->tx_ref}");
            }
        }

        $this->info('Verification completed');
    }
}
```

## Testing Examples

### 1. Unit Test Example

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ChapaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChapaPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_topup_initialization()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'api')
            ->postJson('/api/chapa/wallet/topup', [
                'amount' => 100,
                'phone_number' => '+251911123456'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'checkout_url',
                    'tx_ref',
                    'amount'
                ]
            ]);
    }

    public function test_reservation_payment_initialization()
    {
        $user = User::factory()->create();
        $reservation = $user->reservations()->create([
            'parking_spot_id' => 1,
            'status' => 'reserved',
            'total_cost' => 50.00
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/chapa/reservation/payment', [
                'reservation_id' => $reservation->id,
                'phone_number' => '+251911123456'
            ]);

        $response->assertStatus(200);
    }
}
```

### 2. Postman/cURL Examples

```bash
# Wallet top-up
curl -X POST "http://localhost:8000/api/chapa/wallet/topup" \
-H "Authorization: Bearer your_jwt_token" \
-H "Content-Type: application/json" \
-d '{
    "amount": 100,
    "phone_number": "+251911123456"
}'

# Verify payment
curl -X GET "http://localhost:8000/api/chapa/verify/TX_ABCD123456_1640995200" \
-H "Authorization: Bearer your_jwt_token"

# Get transaction history
curl -X GET "http://localhost:8000/api/chapa/transactions?per_page=5" \
-H "Authorization: Bearer your_jwt_token"
```

## Mobile App Integration (React Native Example)

```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Linking } from 'react-native';

const ChapaPayment = {
    async initializePayment(amount, phoneNumber) {
        try {
            const token = await AsyncStorage.getItem('auth_token');
            
            const response = await fetch('http://your-api-url/api/chapa/wallet/topup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    amount: amount,
                    phone_number: phoneNumber
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                // Store transaction reference
                await AsyncStorage.setItem('pending_tx_ref', result.data.tx_ref);
                
                // Open checkout URL in browser
                Linking.openURL(result.data.checkout_url);
                
                return result.data.tx_ref;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            throw error;
        }
    },

    async verifyPayment(txRef) {
        try {
            const token = await AsyncStorage.getItem('auth_token');
            
            const response = await fetch(`http://your-api-url/api/chapa/verify/${txRef}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const result = await response.json();
            return result;
        } catch (error) {
            throw error;
        }
    }
};

export default ChapaPayment;
```

## Error Handling Best Practices

```javascript
const PaymentErrorHandler = {
    handlePaymentError(error) {
        if (error.response) {
            // Server responded with error status
            const { status, data } = error.response;
            
            switch (status) {
                case 400:
                    return 'Invalid payment data. Please check your input.';
                case 401:
                    return 'Authentication required. Please log in again.';
                case 403:
                    return 'Access denied. You cannot perform this action.';
                case 404:
                    return 'Transaction not found.';
                case 422:
                    return data.message || 'Validation failed.';
                case 500:
                    return 'Server error. Please try again later.';
                default:
                    return 'An unexpected error occurred.';
            }
        } else if (error.request) {
            // Network error
            return 'Network error. Please check your connection.';
        } else {
            // Other error
            return error.message || 'An unknown error occurred.';
        }
    }
};
```

These examples provide a comprehensive guide on how to integrate and use the Chapa payment system in various scenarios. Remember to always test thoroughly in the test environment before going live.