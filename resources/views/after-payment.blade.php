<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>After Payment - MikiPark</title>
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
            max-width: 600px; 
            width: 100%;
            text-align: center;
        }
        .card { 
            background: #111827; 
            border: 1px solid #1f2937; 
            border-radius: 16px; 
            padding: 40px 32px; 
            box-shadow: 0 20px 40px rgba(0,0,0,.3);
            margin-bottom: 20px;
        }
        .icon {
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
            font-size: 32px; 
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
        .steps {
            text-align: left;
            background: #0f1629;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            border-left: 4px solid #059669;
        }
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 16px;
            font-size: 16px;
        }
        .step:last-child {
            margin-bottom: 0;
        }
        .step-number {
            background: #059669;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .actions { 
            display: flex; 
            gap: 16px; 
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 32px;
        }
        .btn { 
            border-radius: 12px; 
            padding: 16px 32px; 
            border: none;
            cursor: pointer; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s ease;
            min-width: 160px;
            justify-content: center;
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
        .url-box {
            background: #0f1629;
            border: 1px solid #374151;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            color: #34d399;
            word-break: break-all;
        }
        .bookmark-tip {
            background: #1e293b;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
            font-size: 14px;
            color: #94a3b8;
        }
        @media (max-width: 640px) { 
            .actions { flex-direction: column; }
            .btn { width: 100%; }
            .title { font-size: 24px; }
            .subtitle { font-size: 16px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">
                <i class="fa-solid fa-check"></i>
            </div>
            
            <h1 class="title">Payment Completed!</h1>
            <p class="subtitle">
                Your payment was successful on Chapa. Here's what to do next to access your MikiPark account.
            </p>

            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div>Close the Chapa tab (you can safely close it now)</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div>Click the "Go to Profile" button below, or visit the URL directly</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div>Your wallet balance will be updated automatically</div>
                </div>
            </div>

            <div class="url-box">
                {{ config('app.url') }}/profile
            </div>

            <div class="actions">
                <a class="btn primary" href="/profile">
                    <i class="fa-solid fa-user"></i> Go to Profile
                </a>
                
                <a class="btn secondary" href="/dashboard">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
            </div>

            <div class="bookmark-tip">
                <i class="fa-solid fa-bookmark"></i>
                <strong>Tip:</strong> Bookmark this page ({{ config('app.url') }}/after-payment) for easy access after future payments.
            </div>
        </div>
    </div>
</body>
</html>