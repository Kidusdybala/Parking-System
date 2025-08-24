<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Figtree', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#0f172a; color:#e2e8f0; margin:0; }
        .container { max-width: 880px; margin: 40px auto; padding: 24px; }
        .card { background:#111827; border:1px solid #1f2937; border-radius: 12px; padding: 24px; box-shadow: 0 10px 20px rgba(0,0,0,.25); }
        .title { font-size: 24px; font-weight: 600; margin: 0 0 8px; }
        .subtitle { color:#9ca3af; margin: 0 0 16px; }
        .row { display:flex; gap:16px; flex-wrap:wrap; margin-top: 16px; }
        .pill { padding:6px 10px; border-radius:999px; font-size: 12px; display:inline-flex; gap:8px; align-items:center; border:1px solid #374151; color:#d1d5db }
        .pill.success { border-color:#059669; color:#34d399 }
        .pill.pending { border-color:#f59e0b; color:#fbbf24 }
        .pill.failed { border-color:#dc2626; color:#fca5a5 }
        .grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px; }
        .item { background:#0b1220; border:1px solid #1f2937; border-radius:10px; padding:14px; }
        .label { font-size:12px; color:#9ca3af; text-transform: uppercase; letter-spacing: .06em; }
        .value { font-size: 16px; margin-top:6px; color:#e5e7eb }
        .actions { display:flex; gap:12px; margin-top: 20px; flex-wrap:wrap }
        .btn { border-radius: 8px; padding: 10px 14px; border:1px solid #374151; color:#e5e7eb; background:#0b1220; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px }
        .btn.primary { background:#16a34a; border-color:#16a34a; color:white }
        .btn.success { background:#059669; border-color:#059669; color:white; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3); }
        .btn.warn { background:#1f2937; border-color:#374151 }
        .note { color:#9ca3af; font-size: 14px; margin-top: 8px }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr } }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap">
                <div>
                    <h1 class="title">Payment Receipt</h1>
                    <p class="subtitle">Thank you! Here are the details of your transaction.</p>
                </div>
                @php
                    $statusClass = 'pending';
                    if(isset($success) && $success) { $statusClass = 'success'; }
                    elseif(isset($transaction) && method_exists($transaction, 'isFailed') && $transaction->isFailed()) { $statusClass = 'failed'; }
                @endphp
                <span class="pill {{ $statusClass }}">
                    <i class="fa-solid fa-receipt"></i>
                    {{ isset($transaction) ? ucfirst($transaction->status ?? 'pending') : (isset($success) && $success ? 'Success' : 'Pending') }}
                </span>
            </div>

            <div class="row">
                <div class="grid" style="flex:1">
                    <div class="item">
                        <div class="label">Amount</div>
                        <div class="value">
                            @if(isset($transaction) && $transaction)
                                {{ number_format((float)($transaction->amount ?? 0), 2) }} {{ $transaction->currency ?? 'ETB' }}
                            @else
                                — ETB
                            @endif
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">Transaction Ref</div>
                        <div class="value">{{ $transaction->tx_ref ?? request('tx_ref') ?? request('trx') ?? '—' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Type</div>
                        <div class="value">{{ ucfirst($type ?? ($transaction && $transaction->reservation_id ? 'reservation' : 'wallet')) }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Paid At</div>
                        <div class="value">{{ optional($transaction->paid_at ?? null)->format('Y-m-d H:i') ?? '—' }}</div>
                    </div>
                    @if(isset($transaction) && $transaction->reservation_id)
                        <div class="item">
                            <div class="label">Reservation</div>
                            <div class="value">#{{ $transaction->reservation_id }} @if(optional($transaction->reservation->parkingSpot ?? null)) (Spot {{ $transaction->reservation->parkingSpot->spot_number }}) @endif</div>
                        </div>
                    @endif
                    <div class="item" style="grid-column: 1 / -1">
                        <div class="label">Account</div>
                        <div class="value">{{ optional($user ?? null)->name ?? '—' }} ({{ optional($user ?? null)->email ?? '—' }})</div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a class="btn primary" href="/" id="goNow">
                    <i class="fa-solid fa-gauge"></i> Go to Dashboard
                </a>

                <a class="btn success" href="/profile" title="Go to your profile page" style="font-weight: 600;">
                    <i class="fa-solid fa-user"></i> Go to Profile
                </a>

                @if(isset($transaction) && $transaction->tx_ref)
                    <a class="btn" href="{{ route('receipt.download', ['transaction_id' => $transaction->tx_ref]) }}">
                        <i class="fa-solid fa-download"></i> Download PDF Receipt
                    </a>
                @endif

                @if(isset($transaction) && $transaction->status === 'pending')
                    <button class="btn" id="verifyBtn" onclick="verifyPayment('{{ $transaction->tx_ref }}')">
                        <i class="fa-solid fa-sync"></i> Verify Payment
                    </button>
                @endif

                <button class="btn warn" id="stayBtn" title="Prevent auto-redirect">
                    <i class="fa-regular fa-clock"></i> Stay on this page
                </button>

                <button class="btn primary" id="continueBtn" style="display: none; opacity: 0; transition: opacity 0.3s ease;" title="Continue to dashboard now">
                    <i class="fa-solid fa-arrow-right"></i> Continue to Dashboard
                </button>
            </div>

            <p class="note" id="redirectNote">You will be redirected to the dashboard in <span id="count">60</span> seconds. Click "Stay on this page" to cancel.</p>
            <p class="note">If the page came from Chapa, you can safely close the Chapa tab now.</p>
        </div>
    </div>

    <script>
        // Manual payment verification function
        function verifyPayment(txRef) {
            var verifyBtn = document.getElementById('verifyBtn');
            if (verifyBtn) {
                verifyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';
                verifyBtn.disabled = true;
            }

            fetch('/api/chapa/verify/' + txRef + '/force', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + (localStorage.getItem('token') || ''),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload the page to show updated status
                    window.location.reload();
                } else {
                    alert('Verification failed: ' + (data.message || 'Unknown error'));
                    if (verifyBtn) {
                        verifyBtn.innerHTML = '<i class="fa-solid fa-sync"></i> Verify Payment';
                        verifyBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Verification error:', error);
                alert('Verification failed. Please try again.');
                if (verifyBtn) {
                    verifyBtn.innerHTML = '<i class="fa-solid fa-sync"></i> Verify Payment';
                    verifyBtn.disabled = false;
                }
            });
        }

        // Simple countdown and optional auto-redirect
        (function(){
            var seconds = 60; // 60 seconds for viewing the receipt
            var timer = setInterval(function(){
                seconds -= 1;
                var countEl = document.getElementById('count');
                if (countEl) countEl.textContent = String(seconds);
                
                // Show continue button after 10 seconds
                if (seconds === 50) {
                    var continueBtn = document.getElementById('continueBtn');
                    if (continueBtn) {
                        continueBtn.style.display = 'inline-flex';
                        setTimeout(function() {
                            continueBtn.style.opacity = '1'; // Fade in
                        }, 10);
                    }
                }
                
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = '/';
                }
            }, 1000);

            document.getElementById('stayBtn').addEventListener('click', function(){
                clearInterval(timer);
                var note = document.getElementById('redirectNote');
                if (note) note.textContent = 'Auto-redirect cancelled. You can stay on this page as long as you like.';
            });

            document.getElementById('continueBtn').addEventListener('click', function(){
                clearInterval(timer);
                window.location.href = '/';
            });
        })();
    </script>
</body>
</html>