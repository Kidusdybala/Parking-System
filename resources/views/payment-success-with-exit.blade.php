<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - MikiPark</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Figtree', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0; 
            margin: 0; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { 
            max-width: 700px; 
            width: 100%;
        }
        .card { 
            background: #111827; 
            border: 1px solid #1f2937; 
            border-radius: 16px; 
            padding: 40px 32px; 
            box-shadow: 0 20px 40px rgba(0,0,0,.3);
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 48px;
            color: white;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .title { 
            font-size: 36px; 
            font-weight: 600; 
            margin: 0 0 16px;
            color: #f8fafc;
        }
        .subtitle { 
            color: #94a3b8; 
            margin: 0 0 32px; 
            font-size: 18px;
            line-height: 1.6;
        }
        .receipt-info {
            background: #0f1629;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            text-align: left;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #1f2937;
        }
        .receipt-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 18px;
            color: #34d399;
        }
        .receipt-label {
            color: #94a3b8;
        }
        .receipt-value {
            color: #e2e8f0;
            font-weight: 500;
        }
        .actions { 
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px; 
            margin-top: 32px;
        }
        .btn { 
            border-radius: 12px; 
            padding: 16px 24px; 
            border: none;
            cursor: pointer; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            gap: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,.3);
        }
        .btn.primary { 
            background: #059669; 
            color: white;
        }
        .btn.primary:hover {
            background: #047857;
        }
        .btn.secondary {
            background: #1f2937;
            color: #e5e7eb;
            border: 1px solid #374151;
        }
        .btn.secondary:hover {
            background: #374151;
        }
        .btn.exit {
            background: #dc2626;
            color: white;
            grid-column: 1 / -1;
            font-size: 18px;
            padding: 20px;
            margin-top: 16px;
        }
        .btn.exit:hover {
            background: #b91c1c;
        }
        .receipt-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin: 24px 0;
        }
        .btn.receipt {
            background: #374151;
            color: #e5e7eb;
            border: 1px solid #4b5563;
            padding: 12px 20px;
            font-size: 14px;
        }
        .btn.receipt:hover {
            background: #4b5563;
        }
        .note {
            background: #1e293b;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
            font-size: 14px;
            color: #94a3b8;
            text-align: center;
        }
        @media (max-width: 640px) { 
            .actions { 
                grid-template-columns: 1fr;
            }
            .title { font-size: 28px; }
            .subtitle { font-size: 16px; }
            .receipt-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            
            <h1 class="title">Payment Successful!</h1>
            <p class="subtitle">
                Your payment has been processed successfully. Your wallet balance has been updated.
            </p>

            @if(isset($transaction))
            <div class="receipt-info">
                <div class="receipt-row">
                    <span class="receipt-label">Transaction ID</span>
                    <span class="receipt-value">{{ $transaction->tx_ref }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Amount</span>
                    <span class="receipt-value">{{ $transaction->amount }} {{ $transaction->currency }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Status</span>
                    <span class="receipt-value">{{ ucfirst($transaction->status) }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Date</span>
                    <span class="receipt-value">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">New Balance</span>
                    <span class="receipt-value">{{ optional($user ?? null)->balance ?? '—' }} ETB</span>
                </div>
            </div>

            <div class="receipt-actions">
                <button class="btn receipt" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Print Receipt
                </button>
                <a class="btn receipt" href="{{ route('receipt.download', ['transaction_id' => $transaction->tx_ref]) }}">
                    <i class="fa-solid fa-download"></i> Download Receipt
                </a>
            </div>
            @endif

            <div class="actions">
                <a class="btn primary" href="/profile">
                    <i class="fa-solid fa-user"></i> Go to Profile
                </a>
                
                <a class="btn secondary" href="/dashboard">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
            </div>

            <!-- PROMINENT EXIT BUTTON -->
            <button class="btn exit" onclick="closeWindow()">
                <i class="fa-solid fa-times-circle"></i> Exit & Close Tab
            </button>

            <div class="note">
                <i class="fa-solid fa-info-circle"></i>
                You can safely close this tab now. Your payment has been processed and your balance updated.
            </div>
        </div>
    </div>

    <script>
        function closeWindow() {
            // Try to close the window/tab
            if (window.opener) {
                // If opened by another window, close this one
                window.close();
            } else {
                // If it's the main tab, try to close it
                window.close();
                
                // If close doesn't work (some browsers block it), show alternative
                setTimeout(() => {
                    if (!window.closed) {
                        alert('Please close this tab manually or navigate to your profile page.');
                        window.location.href = '/profile';
                    }
                }, 100);
            }
        }

        // Auto-close after 60 seconds if user doesn't interact
        let autoCloseTimer = setTimeout(() => {
            if (confirm('Would you like to close this tab and go to your profile?')) {
                window.location.href = '/profile';
            }
        }, 60000);

        // Cancel auto-close if user interacts with the page
        document.addEventListener('click', () => {
            clearTimeout(autoCloseTimer);
        });

        document.addEventListener('keydown', () => {
            clearTimeout(autoCloseTimer);
        });
    </script>
</body>
</html>