
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Parking Receipt - Smart Parking System</title>
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .receipt-container {
      max-width: 400px;
      margin: auto;
      background: #fff;
      border-radius: 10px;
    }
    .receipt-details p {
      font-size: 16px;
      margin-bottom: 5px;
    }
    @media print {
      body * {
        visibility: hidden;
      }
      #receipt, #receipt * {
        visibility: visible;
      }
      #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">
  <!-- Noise Background -->
  <div class="fixed inset-0 noise-bg pointer-events-none"></div>

  <!-- Header/Navigation -->
  <header class="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
    <div class="container flex items-center justify-between h-16">
      <div class="flex items-center gap-2">
        <a href="{{ url('/') }}">
          <i class="fas fa-parking text-primary text-2xl"></i>
          <span class="font-bold text-xl">Miki<span class="text-primary">Park</span></span>
        </a>
      </div>

      <!-- Desktop Navigation -->


      <!-- Mobile Navigation Button -->
      <button class="md:hidden p-2 rounded-md hover:bg-accent" id="mobile-menu-button">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Mobile Navigation Menu -->

  </header>

  <main class="flex-1 flex items-center justify-center py-12 px-4">
    <div class="glass-card w-full max-w-md p-8 relative overflow-hidden" id="receipt">
      <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>

      <div class="relative z-10">
        <div class="text-center mb-6">
          <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-receipt text-primary text-xl"></i>
          </div>
          <h1 class="text-2xl font-bold">🚗 Parking Receipt</h1>
        </div>

        <hr class="border-t border-white/10 my-4">

        <div class="receipt-details space-y-3">
          <div class="flex justify-between">
            <span class="font-medium">Receipt No:</span>
            <span>#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-medium">Date:</span>
            <span>{{ now()->format('Y-m-d H:i:s') }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-medium">Parking Spot:</span>
            <span>{{ $reservation->parkingSpot->name }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-medium">Place ID:</span>
            <span>{{ $reservation->parking_spot_id }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-medium">Parked At:</span>
            <span>{{ $reservation->parked_at }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-medium">Left At:</span>
            <span>{{ $reservation->left_at }}</span>
          </div>

          <div class="flex justify-between">
            <span class="font-medium">Duration:</span>
            <span>{{ \Carbon\Carbon::parse($reservation->parked_at)->diffInMinutes($reservation->left_at) }} minutes</span>
          </div>

          <div class="flex justify-between p-3 bg-primary/10 rounded-md mt-4">
            <span class="font-bold">Total Paid:</span>
            <span class="font-bold text-success">{{ number_format($reservation->total_price, 2) }} ETB</span>
          </div>
        </div>

        <hr class="border-t border-white/10 my-4">

        <div class="flex justify-center space-x-4 mt-6">
          <button onclick="printReceipt()" class="btn btn-primary">
            <i class="fas fa-print mr-2"></i> Print Receipt
          </button>
          <a href="{{ route('client.parking.manage') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Parking
          </a>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="border-t border-white/10 py-6">
    <div class="container">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="flex items-center gap-2 mb-4 md:mb-0">
          <i class="fas fa-parking text-primary text-xl"></i>
          <span class="font-bold">Smart<span class="text-primary">Park</span></span>
        </div>

        <p class="text-muted-foreground text-sm">
          © {{ date('Y') }} SmartPark. All rights reserved.
        </p>
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('hidden');
    });

    // Print receipt function
    function printReceipt() {
      window.print();
    }
  </script>
</body>
</html>

