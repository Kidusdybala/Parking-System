# Chapa Payment Gateway Integration

This document explains how to integrate and use the Chapa payment gateway in the Parking System application.

## Overview

Chapa is Ethiopia's leading payment gateway that allows businesses to accept payments from customers through various methods including:
- Mobile money (CBE Birr, Amole, M-Birr)
- Bank cards
- Online banking

## Configuration

### Environment Variables

Add the following variables to your `.env` file:

```env
# Chapa Payment Gateway Configuration
CHAPA_SECRET_KEY=your_chapa_secret_key_here
CHAPA_PUBLIC_KEY=your_chapa_public_key_here
CHAPA_BASE_URL=https://api.chapa.co/v1
```

### Test Environment Setup

For testing purposes (which is recommended since you don't have SSL), use Chapa's test credentials:

1. Visit [Chapa Dashboard](https://dashboard.chapa.co)
2. Create a test account
3. Get your test secret and public keys
4. Use the test API endpoint: `https://api.chapa.co/v1`

**Test Credentials Example:**
```env
CHAPA_SECRET_KEY=CHASECK_TEST-xxxxxxxxxxxxxxxxxxxxxxxxx
CHAPA_PUBLIC_KEY=CHAPUBK_TEST-xxxxxxxxxxxxxxxxxxxxxxxxx
CHAPA_BASE_URL=https://api.chapa.co/v1
```

## Database Migration

Run the migration to create the chapa_transactions table:

```bash
php artisan migrate
```

## API Endpoints

### 1. Wallet Top-up

Initialize a payment for wallet top-up:

**Endpoint:** `POST /api/chapa/wallet/topup`

**Headers:**
```
Authorization: Bearer {jwt_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "amount": 100.00,
    "phone_number": "+251911123456"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Payment initialized successfully",
    "data": {
        "checkout_url": "https://checkout.chapa.co/checkout/payment/xxxxxxxxxx",
        "tx_ref": "TX_ABCD123456_1640995200",
        "amount": 100.00
    }
}
```

### 2. Reservation Payment

Initialize a payment for parking reservation:

**Endpoint:** `POST /api/chapa/reservation/payment`

**Headers:**
```
Authorization: Bearer {jwt_token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "reservation_id": 123,
    "phone_number": "+251911123456"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Payment initialized successfully",
    "data": {
        "checkout_url": "https://checkout.chapa.co/checkout/payment/xxxxxxxxxx",
        "tx_ref": "TX_ABCD123456_1640995200",
        "amount": 50.00,
        "reservation_id": 123
    }
}
```

### 3. Verify Payment

Verify the status of a payment:

**Endpoint:** `GET /api/chapa/verify/{txRef}`

**Headers:**
```
Authorization: Bearer {jwt_token}
```

**Response:**
```json
{
    "status": "success",
    "message": "Payment verification successful",
    "data": {
        "transaction_status": "success",
        "amount": 100.00,
        "paid_at": "2024-01-28T10:30:00.000000Z",
        "chapa_response": {
            "status": "success",
            "message": "Payment completed",
            "data": {
                "tx_ref": "TX_ABCD123456_1640995200",
                "amount": 100,
                "currency": "ETB"
            }
        }
    }
}
```

### 4. Transaction History

Get user's transaction history:

**Endpoint:** `GET /api/chapa/transactions`

**Headers:**
```
Authorization: Bearer {jwt_token}
```

**Query Parameters:**
- `per_page` (optional): Number of transactions per page (default: 10)

**Response:**
```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "tx_ref": "TX_ABCD123456_1640995200",
                "amount": "100.00",
                "currency": "ETB",
                "status": "success",
                "description": "Wallet Top-up - Parking System",
                "created_at": "2024-01-28T10:00:00.000000Z",
                "paid_at": "2024-01-28T10:30:00.000000Z",
                "reservation": null
            }
        ],
        "total": 1,
        "per_page": 10
    }
}
```

### 5. Transaction Details

Get specific transaction details:

**Endpoint:** `GET /api/chapa/transactions/{txRef}`

**Headers:**
```
Authorization: Bearer {jwt_token}
```

### 6. Cancel Transaction

Cancel a pending transaction:

**Endpoint:** `POST /api/chapa/transactions/{txRef}/cancel`

**Headers:**
```
Authorization: Bearer {jwt_token}
```

## Payment Flow

### Wallet Top-up Flow

1. User initiates wallet top-up via API
2. System creates a pending transaction record
3. System returns Chapa checkout URL
4. User is redirected to Chapa checkout page
5. User completes payment on Chapa
6. Chapa sends webhook to `/api/chapa/callback`
7. System verifies payment with Chapa API
8. If successful, user's balance is updated
9. Receipt is generated

### Reservation Payment Flow

1. User completes parking session
2. System calculates total cost
3. User initiates payment via API
4. System creates pending transaction record
5. System returns Chapa checkout URL
6. User completes payment on Chapa
7. Chapa sends webhook notification
8. System verifies payment and updates reservation as paid
9. Receipt is generated

## Security Features

- All transactions are logged with detailed information
- Webhook signatures are verified (when implemented)
- Failed payments are tracked and can be retried
- Transactions can be cancelled if still pending
- All sensitive data is encrypted in database

## Error Handling

The integration includes comprehensive error handling:

- Invalid payment amounts
- Insufficient funds
- Network connectivity issues
- Invalid transaction references
- Unauthorized access attempts

## Testing

### Test Payment Methods

When using test environment, you can use these test scenarios:

1. **Successful Payment:** Use test phone numbers provided by Chapa
2. **Failed Payment:** Use specific test scenarios to simulate failures
3. **Cancelled Payment:** Close the checkout page to simulate cancellation

### Test Cards

Chapa provides test card numbers for different scenarios:
- Success: `4000000000000002`
- Declined: `4000000000000010`
- Insufficient funds: `4000000000000019`

## Production Setup

When ready for production:

1. Get production API keys from Chapa dashboard
2. Update environment variables with production keys
3. Ensure your webhook endpoint is accessible via HTTPS
4. Update return URLs to use HTTPS
5. Test thoroughly with small amounts first

## Troubleshooting

### Common Issues

1. **Webhook not received:** Ensure your callback URL is publicly accessible
2. **Payment verification fails:** Check your secret key configuration
3. **Invalid amount:** Ensure amount is between minimum and maximum limits
4. **User not found:** Verify JWT token is valid and user exists

### Logs

Payment activities are logged in Laravel's log files:
- Payment initializations
- Webhook callbacks
- Verification attempts
- Errors and exceptions

Check `storage/logs/laravel.log` for detailed information.

## Support

For technical support:
- Chapa API Documentation: [https://developer.chapa.co/docs](https://developer.chapa.co/docs)
- Chapa Support: support@chapa.co
- Developer Dashboard: [https://dashboard.chapa.co](https://dashboard.chapa.co)