<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Verify Email - Smart Parking System</title>
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

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center space-x-1">
        <a href="{{ url('/') }}" class="nav-link">Home</a>
        <a href="{{ route('login') }}" class="nav-link">Login</a>
        <a href="{{ route('register') }}" class="nav-link">Register</a>
      </nav>

      <!-- Mobile Navigation Button -->
      <button class="md:hidden p-2 rounded-md hover:bg-accent" id="mobile-menu-button">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="md:hidden hidden" id="mobile-menu">
      <div class="container py-2 space-y-1 border-t border-white/10">
        <a href="{{ url('/') }}" class="nav-link block">Home</a>
        <a href="{{ route('login') }}" class="nav-link block">Login</a>
        <a href="{{ route('register') }}" class="nav-link block">Register</a>
      </div>
    </div>
  </header>

  <main class="flex-1 flex items-center justify-center py-12 px-4">
    <div class="glass-card w-full max-w-md p-8 relative overflow-hidden">
      <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>

      <div class="relative z-10">
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-envelope-open-text text-primary text-xl"></i>
          </div>
          <h1 class="text-2xl font-bold">Verify Your Email</h1>
          <p class="text-muted-foreground mt-2">
            {{ __('A verification code has been sent to your email. Please enter the code below to verify your account.') }}
          </p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-md text-green-500 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-md text-red-500 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Verification Form -->
        <form method="POST" action="{{ route('verify-email') }}" id="verification-form">
          @csrf

          <!-- Email Input -->
          <div class="space-y-4">
            <div>
              <label for="email" class="block text-sm font-medium mb-1">
                {{ __('Email') }}
              </label>
              <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                     class="input w-full" placeholder="your@email.com">
            </div>

            <!-- Verification Code Input -->
            <div>
              <label for="verification_code" class="block text-sm font-medium mb-1">
                {{ __('Verification Code') }}
              </label>
              <div class="grid grid-cols-6 gap-2">
                @for ($i = 0; $i < 6; $i++)
                <input type="text" class="input w-full text-center text-lg font-mono"
                       maxlength="1" pattern="[0-9]" inputmode="numeric"
                       data-verification-input required>
                @endfor
              </div>
              <input type="hidden" id="verification-code-full" name="verification_code">
            </div>

            <div class="flex justify-center">
              <div id="countdown" class="text-sm text-muted-foreground">
                Code expires in <span id="timer" class="font-medium text-primary">5:00</span>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-full">
              {{ __('Verify Account') }}
              <i class="fas fa-check-circle ml-2"></i>
            </button>
          </div>
        </form>

        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4">
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-outline w-full md:w-auto">
              {{ __('Resend Code') }}
              <i class="fas fa-paper-plane ml-2"></i>
            </button>
          </form>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-muted-foreground hover:text-primary hover:underline">
              {{ __('Log Out') }}
              <i class="fas fa-sign-out-alt ml-1"></i>
            </button>
          </form>
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

    // Verification code input handling
    const verificationInputs = document.querySelectorAll('[data-verification-input]');
    const hiddenInput = document.getElementById('verification-code-full');
    const form = document.getElementById('verification-form');

    // Handle input for verification code fields
    verificationInputs.forEach((input, index) => {
      // Auto-focus next input on keyup
      input.addEventListener('keyup', function(e) {
        const key = e.key;

        // Check if input is a number
        if (/^[0-9]$/.test(key)) {
          // Move to next input if available
          if (index < verificationInputs.length - 1) {
            verificationInputs[index + 1].focus();
          }
        } else if (key === 'Backspace') {
          // Clear current input and move to previous on backspace
          this.value = '';
          if (index > 0) {
            verificationInputs[index - 1].focus();
          }
        }

        // Update hidden input with full verification code
        updateHiddenInput();
      });

      // Handle paste event
      input.addEventListener('paste', function(e) {
        e.preventDefault();

        // Get pasted content
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');

        // If we have digit(s) in the pasted content
        if (/\d/.test(pastedText)) {
          // Fill inputs with pasted digits
          const digits = pastedText.match(/\d/g);

          for (let i = 0; i < Math.min(digits.length, verificationInputs.length); i++) {
            verificationInputs[i].value = digits[i];

            // Focus last filled input or next empty one
            if (i === digits.length - 1 && i < verificationInputs.length - 1) {
              verificationInputs[i + 1].focus();
            }
          }

          // Update hidden input
          updateHiddenInput();
        }
      });
    });

    // Update the hidden input with combined verification code
    function updateHiddenInput() {
      hiddenInput.value = Array.from(verificationInputs).map(input => input.value).join('');
    }

    // Countdown timer for code expiration
    function startExpirationTimer() {
      let minutes = 5;
      let seconds = 0;

      const timerEl = document.getElementById('timer');

      const timerInterval = setInterval(() => {
        if (seconds === 0) {
          if (minutes === 0) {
            clearInterval(timerInterval);
            timerEl.textContent = 'Expired';
            timerEl.classList.remove('text-primary');
            timerEl.classList.add('text-red-500');
            return;
          }
          minutes--;
          seconds = 59;
        } else {
          seconds--;
        }

        timerEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
      }, 1000);
    }

    // Start timer when page loads
    document.addEventListener('DOMContentLoaded', function() {
      startExpirationTimer();

      // Focus first input
      verificationInputs[0].focus();
    });

    // Form submission validation
    form.addEventListener('submit', function(event) {
      const code = hiddenInput.value;

      // Check if code is complete
      if (code.length !== 6) {
        event.preventDefault();
        alert('Please enter the complete 6-digit verification code');
      }
    });
  </script>
</body>
</html>

