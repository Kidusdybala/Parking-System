<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Upload Payment Proof - Smart Parking System</title>
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col">
  <div class="fixed inset-0 noise-bg pointer-events-none"></div>

  <header class="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
    <div class="container flex items-center justify-between h-16">
      <div class="flex items-center gap-2">
        <a href="{{ url('/') }}">
          <i class="fas fa-parking text-primary text-2xl"></i>
          <span class="font-bold text-xl">Miki<span class="text-primary">Park</span></span>
        </a>
      </div>
      
      <button class="md:hidden p-2 rounded-md hover:bg-accent" id="mobile-menu-button">
        <i class="fas fa-bars"></i>
      </button>
    </div>
    
  </header>

  <main class="flex-1 flex items-center justify-center py-12 px-4">
    <div class="glass-card w-full max-w-xl p-8 relative overflow-hidden">
      <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>
      <div class="relative z-10">
        <h2 class="text-2xl font-bold mb-6 text-center">Upload Payment Proof</h2>

        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('payment.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="payment_screenshot" class="fw-bold mb-2 block text-sm font-medium">Upload Payment Screenshot</label>
                <input type="file" class="form-control w-full @error('payment_screenshot') is-invalid @enderror"
                       name="payment_screenshot" id="payment_screenshot" required accept="image/*">

                @error('payment_screenshot')
                    <div class="invalid-feedback text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror

                <small class="form-text text-muted mt-2 block text-sm text-gray-500">
                    <i class="fas fa-info-circle"></i> Only image files (JPEG, PNG, JPG) are allowed. Max file size: 2MB.
                </small>
            </div>

            <button type="submit" class="btn btn-success mt-4 w-full">
                <i class="fas fa-upload"></i> Submit Payment Proof
            </button>
        </form>

        <div class="card mt-6 border border-info rounded-lg p-4 bg-info/10">
            <div class="card-header font-bold text-info mb-2">
                <i class="fas fa-info-circle"></i> Instructions
            </div>
            <div class="card-body text-sm">
                <ol class="list-decimal list-inside space-y-1">
                    <li>Take a clear screenshot of your payment receipt/confirmation.</li>
                    <li>Ensure all payment details are visible.</li>
                    <li>Upload the image using the form above.</li>
                    <li>Wait for admin approval.</li>
                </ol>
            </div>
        </div>
      </div>
    </div>
  </main>

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
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('hidden');
    });
  </script>
</body>
</html>
