# Chapa Payment Integration - Complete Setup Instructions

This guide will walk you through the complete setup process for integrating Chapa payment gateway into your Laravel parking system.

## Prerequisites

- Laravel 11.x application (✅ Already available)
- PHP 8.2+ (✅ Already available)
- Composer (✅ Already available)
- Internet connection
- Chapa account (free to create)

## Step 1: Create Chapa Account

1. Visit [Chapa Dashboard](https://dashboard.chapa.co)
2. Sign up for a new account
3. Verify your email address
4. Complete your business profile

## Step 2: Get Test API Keys

1. Login to your Chapa dashboard
2. Navigate to "API Keys" section
3. Copy your **Test Secret Key** (starts with `CHASECK_TEST-`)
4. Copy your **Test Public Key** (starts with `CHAPUBK_TEST-`)

## Step 3: Configure Environment Variables

1. Create a copy of `.env.example` as `.env` if you haven't already:
   ```bash
   copy .env.example .env
   ```

2. Add your Chapa credentials to the `.env` file:
   ```env
   # Chapa Payment Gateway Configuration
   CHAPA_SECRET_KEY=CHASECK_TEST-your_secret_key_here
   CHAPA_PUBLIC_KEY=CHAPUBK_TEST-your_public_key_here
   CHAPA_BASE_URL=https://api.chapa.co/v1
   ```

3. Make sure these other settings are configured:
   ```env
   APP_URL=http://localhost:8000
   JWT_SECRET=your_jwt_secret_here
   ```

## Step 4: Install Dependencies and Run Migration

1. Install Composer dependencies (if not already done):
   ```bash
   composer install
   ```

2. Generate application key:
   ```bash
   php artisan key:generate
   ```

3. Run the database migration to create the chapa_transactions table:
   ```bash
   php artisan migrate
   ```

## Step 5: Test the Setup

1. Run the test setup script:
   ```bash
   php chapa-test-setup.php
   ```

2. Start your Laravel development server:
   ```bash
   php artisan serve
   ```

3. Your application should now be running on `http://localhost:8000`

## Step 6: Test API Endpoints

You can test the API endpoints using any HTTP client (Postman, curl, etc.). First, you need a JWT token:

### Get JWT Token
```bash
# Register a new user or login
curl -X POST "http://localhost:8000/api/auth/login" \
-H "Content-Type: application/json" \
-d '{
    "email": "test@example.com",
    "password": "password"
}'
```

### Test Wallet Top-up
```bash
curl -X POST "http://localhost:8000/api/chapa/wallet/topup" \
-H "Authorization: Bearer YOUR_JWT_TOKEN" \
-H "Content-Type: application/json" \
-d '{
    "amount": 100,
    "phone_number": "+251911123456"
}'
```

Expected response:
```json
{
    "status": "success",
    "message": "Payment initialized successfully",
    "data": {
        "checkout_url": "https://checkout.chapa.co/checkout/payment/...",
        "tx_ref": "TX_...",
        "amount": 100.00
    }
}
```

## Step 7: Frontend Integration

Add the Chapa payment functionality to your frontend. Here's a simple example:

```javascript
// Add this to your existing frontend code
async function topupWallet() {
    const amount = document.getElementById('amount').value;
    const phone = document.getElementById('phone').value;
    const token = localStorage.getItem('auth_token');

    try {
        const response = await fetch('/api/chapa/wallet/topup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                amount: parseFloat(amount),
                phone_number: phone
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Redirect user to Chapa checkout
            window.location.href = result.data.checkout_url;
        } else {
            alert('Payment failed: ' + result.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
```

## Step 8: Update Return URLs (Important)

When users complete payment on Chapa, they need to be redirected back to your app. Update the return URLs:

1. For wallet top-up success: `http://localhost:8000/wallet?payment=success`
2. For reservation payment success: `http://localhost:8000/reservations/{id}?payment=success`

Make sure these routes exist in your frontend application.

## Step 9: Handle Payment Success

Add JavaScript to handle successful payments:

```javascript
// Add this to pages where users return after payment
const urlParams = new URLSearchParams(window.location.search);
const paymentStatus = urlParams.get('payment');

if (paymentStatus === 'success') {
    // Get the transaction reference and verify payment
    const txRef = localStorage.getItem('pending_tx_ref');
    if (txRef) {
        verifyPayment(txRef);
    }
}

async function verifyPayment(txRef) {
    const token = localStorage.getItem('auth_token');
    
    try {
        const response = await fetch(`/api/chapa/verify/${txRef}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const result = await response.json();
        
        if (result.data.transaction_status === 'success') {
            alert('Payment successful! Your balance has been updated.');
            // Refresh the page or update UI
            location.reload();
        }
    } catch (error) {
        console.error('Verification failed:', error);
    }
}
```

## Step 10: Production Setup (When Ready)

When you're ready to go live:

1. Get production API keys from Chapa dashboard
2. Update environment variables with production keys
3. Set up SSL certificate for your domain
4. Update callback URLs to use HTTPS
5. Test with small amounts first

## Troubleshooting

### Common Issues and Solutions

1. **Migration Error**: Make sure your database is properly configured in `.env`
2. **JWT Token Error**: Ensure JWT is properly configured with `JWT_SECRET`
3. **API Connection Error**: Check your internet connection and API keys
4. **Webhook Not Working**: Ensure your callback URL is publicly accessible

### Debug Commands

```bash
# Check Laravel configuration
php artisan config:clear
php artisan cache:clear

# Check database connection
php artisan migrate --dry-run

# View logs
tail -f storage/logs/laravel.log
```

### Test Cards for Chapa Test Environment

Use these test card numbers in the Chapa test environment:

- **Successful Payment**: 4000000000000002
- **Declined Payment**: 4000000000000010
- **Insufficient Funds**: 4000000000000019

## Security Notes

- Never commit your actual API keys to version control
- Use test keys for development and testing
- Always validate payments on the backend
- Log all payment activities for audit purposes

## Support Resources

- **Documentation**: Check `CHAPA-INTEGRATION.md` for detailed API documentation
- **Examples**: Check `CHAPA-USAGE-EXAMPLES.md` for code examples
- **Chapa Support**: support@chapa.co
- **Chapa Developer Docs**: https://developer.chapa.co/docs

## Next Steps

After completing this setup:

1. Test all payment flows thoroughly
2. Implement proper error handling in your frontend
3. Add loading states and user feedback
4. Set up monitoring and logging
5. Plan for production deployment

🎉 **Congratulations!** Your Chapa payment integration is now ready for testing. The new system is much more secure and automated compared to the manual payment proof upload system you had before.