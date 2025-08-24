<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Complete - MikiPark</title>
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
        }
        .container { 
            max-width: 500px; 
            margin: 40px auto; 
            padding: 24px; 
            text-align: center;
        }
        .card { 
            background: #111827; 
            border: 1px solid #1f2937; 
            border-radius: 16px; 
            padding: 40px 32px; 
            box-shadow: 0 20px 40px rgba(0,0,0,.3);
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
            color: white;
        }
        .title { 
            font-size: 28px; 
            font-weight: 600; 
            margin: 0 0 12px;
            color: #f8fafc;
        }
        .subtitle { 
            color: #94a3b8; 
            margin: 0 0 32px; 
            font-size: 16px;
            line-height: 1.5;
        }
        .actions { 
            display: flex; 
            gap: 16px; 
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn { 
            border-radius: 12px; 
            padding: 14px 24px; 
            border: 1px solid #374151; 
            color: #e5e7eb; 
            background: #0b1220; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
            min-width: 140px;
            justify-content: center;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,.2);
        }
        .btn.primary { 
            background: #16a34a; 
            border-color: #16a34a; 
            color: white;
        }
        .btn.primary:hover {
            background: #15803d;
        }
        .btn.success { 
            background: #059669; 
            border-color: #059669; 
            color: white;
        }
        .btn.success:hover {
            background: #047857;
        }
        .btn.secondary {
            background: #1f2937;
            border-color: #374151;
        }
        .btn.secondary:hover {
            background: #374151;
        }
        .note { 
            color: #64748b; 
            font-size: 14px; 
            margin-top: 24px;
            padding: 16px;
            background: #0f1629;
            border-radius: 8px;
            border-left: 4px solid #059669;
        }
        @media (max-width: 640px) { 
            .actions { flex-direction: column; }
            .btn { width: 100%; }
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
                Your payment has been processed successfully. You can now close the Chapa tab and continue using MikiPark.
            </p>

            <div class="actions">
                <a class="btn success" href="/profile">
                    <i class="fa-solid fa-user"></i> Go to Profile
                </a>
                
                <a class="btn primary" href="/dashboard">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                
                <a class="btn secondary" href="/parking">
                    <i class="fa-solid fa-car"></i> Find Parking
                </a>
            </div>

            <div class="note">
                <i class="fa-solid fa-info-circle"></i>
                <strong>Next Steps:</strong> Your wallet has been updated and you can now make reservations or check your transaction history in your profile.
            </div>
        </div>
    </div>

    <script>
        // Auto-close Chapa tab if this page was opened from Chapa
        if (document.referrer.includes('chapa.co')) {
            // Add a small delay to ensure user sees the success message
            setTimeout(() => {
                if (window.opener) {
                    window.close();
                }
            }, 3000);
        }
    </script>
</body>
</html>