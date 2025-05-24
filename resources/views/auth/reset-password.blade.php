<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reset Password - Smart Parking System</title>
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
            <i class="fas fa-lock-open text-primary text-xl"></i>
          </div>
          <h1 class="text-2xl font-bold">Reset Password</h1>
          <p class="text-muted-foreground mt-2">
            {{ __('Create a new password for your account.') }}
          </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-md text-red-500 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Reset Password Form -->
        <form method="POST" action="{{ route('password.store') }}">
          @csrf

          <!-- Password Reset Token -->
          <input type="hidden" name="token" value="{{ $request->route('token') }}">

          <div class="space-y-4">
            <!-- Email Address -->
            <div>
              <label for="email" class="block text-sm font-medium mb-1">
                {{ __('Email') }}
              </label>
              <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                     class="input w-full" autocomplete="username" readonly>
              @error('email')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
              @enderror
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium mb-1">
                {{ __('New Password') }}
              </label>
              <div class="relative">
                <input id="password" type="password" name="password" required
                       class="input w-full pr-10" autocomplete="new-password">
                <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-foreground"
                        onclick="togglePasswordVisibility('password')">
                  <i class="fas fa-eye" id="password-toggle-icon"></i>
                </button>
              </div>
              @error('password')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
              @enderror
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium mb-1">
                {{ __('Confirm Password') }}
              </label>
              <div class="relative">
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="input w-full pr-10" autocomplete="new-password">
                <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-foreground"
                        onclick="togglePasswordVisibility('password_confirmation')">
                  <i class="fas fa-eye" id="password_confirmation-toggle-icon"></i>
                </button>
              </div>
              @error('password_confirmation')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
              @enderror
            </div>

            <button type="submit" class="btn btn-primary w-full">
              {{ __('Reset Password') }}
              <i class="fas fa-check-circle ml-2"></i>
            </button>
          </div>
        </form>
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

    // Toggle password visibility
    function togglePasswordVisibility(inputId) {
      const passwordInput = document.getElementById(inputId);
      const toggleIcon = document.getElementById(inputId + '-toggle-icon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }
  </script>
</body>
</html>

