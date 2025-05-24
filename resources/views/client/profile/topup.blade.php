<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Smart Parking System</title>
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      <!-- Mobile Navigation Button -->
      <button class="md:hidden p-2 rounded-md hover:bg-accent" id="mobile-menu-button">
        <i class="fas fa-bars"></i>
      </button>
    </div>

  </header>
<main class="flex-1 flex items-center justify-center py-12 px-4">
  <div class="glass-card w-full max-w-2xl p-8 relative overflow-hidden">
    <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>

    <div class="relative z-10">
      <h2 class="text-2xl font-bold mb-4 text-center">Top-Up Instructions</h2>
      <p class="mb-6 text-center text-muted-foreground">
        To add balance, please make a deposit to the following account:
      </p>

      <div class="card mb-6 bg-white/10 p-4 rounded-lg shadow-md">
        <div class="card-body space-y-2">
          <h4 class="text-lg">Company Name: <strong>Miki Parking</strong></h4>
          <h4 class="text-lg">Bank Name: <strong>Commercial Bank of Ethiopia (CBE)</strong></h4>
          <h4 class="text-lg">
            Account Number:
            <strong id="accountNumber">1000356570565</strong>
            <button onclick="copyAccount()" class="ml-2 px-3 py-1 text-sm bg-primary text-white rounded hover:bg-blue-700">
              Copy
            </button>
          </h4>
        </div>
      </div>

      <form action="{{ route('client.profile.payment_upload') }}" method="GET" class="text-center">
        <button type="submit" class="btn btn-primary mt-3">Payment Completed</button>
      </form>

      <div class="mt-6">
        <h4 class="text-lg font-semibold mb-2">How to Top Up</h4>
        <ol class="list-decimal list-inside space-y-1 text-sm text-muted-foreground">
          <li>Open your banking app or visit a nearby CBE branch.</li>
          <li>Transfer the desired amount to account number <strong>1000356570565</strong>.</li>
          <li>Take a screenshot or photo of the payment receipt.</li>
          <li>Click on the <strong>“Payment Completed”</strong> button above.</li>
          <li>Upload the screenshot of your payment on the next page.</li>
          <li>Wait for admin approval. You will be credited once approved.</li>
        </ol>
      </div>
    </div>
  </div>
</main>

<script>
  function copyAccount() {
    const accountNumber = document.getElementById("accountNumber").textContent;
    navigator.clipboard.writeText(accountNumber).then(() => {
      alert("Account Number Copied: " + accountNumber);
    });
  }
</script>

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

    // Password visibility toggle
    document.getElementById('toggle-password').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });

    // Navigation links handler
    document.addEventListener('DOMContentLoaded', function() {
      // Get all navigation links
      const navLinks = document.querySelectorAll('.nav-link');

      // Add click event listeners to each link
      navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          const href = this.getAttribute('href');
          if (href && href !== '#') {
            window.location.href = href;
          }
        });
      });
    });
  </script>
</body>
</html>

