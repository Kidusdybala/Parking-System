<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Register - Smart Parking System</title>
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
        <a href="{{ route('register') }}" class="nav-link active">Register</a>
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
        <a href="{{ route('register') }}" class="nav-link active block">Register</a>
      </div>
    </div>
  </header>

  <main class="flex-1 flex items-center justify-center py-12 px-4">
    <div class="glass-card w-full max-w-md p-8 relative overflow-hidden">
      <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>

      <div class="relative z-10">
        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold">Create Your Account</h1>
          <p class="text-muted-foreground mt-2">Join SmartPark and start reserving parking spots</p>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}">
          @csrf
          <div class="space-y-4">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium mb-1">Full Name</label>
              <input type="text" id="name" name="name" class="input w-full" placeholder="John Doe" value="{{ old('name') }}" required autofocus autocomplete="name">
              @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Email Address -->
            <div>
              <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
              <input type="email" id="email" name="email" class="input w-full" placeholder="your@email.com" value="{{ old('email') }}" required autocomplete="username">
              @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium mb-1">Password</label>
              <div class="relative">
                <input type="password" id="password" name="password" class="input w-full pr-10" placeholder="••••••••" required autocomplete="new-password">
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground" id="toggle-password">
                  <i class="far fa-eye"></i>
                </button>
              </div>
              <p class="text-xs text-muted-foreground mt-1">Password must be at least 8 characters</p>
              @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm Password</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="input w-full" placeholder="••••••••" required autocomplete="new-password">
              @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="flex items-center space-x-2">
              <input type="checkbox" id="terms" class="accent-primary h-4 w-4" required>
              <label for="terms" class="text-sm text-muted-foreground">
                I agree to the <a href="#" class="text-primary hover:underline">Terms of Service</a> and <a href="#" class="text-primary hover:underline">Privacy Policy</a>
              </label>
            </div>

            <button type="submit" class="btn btn-primary w-full">
              Create Account
              <i class="fas fa-user-plus ml-2"></i>
            </button>
          </div>
        </form>

        <div class="mt-6 text-center">
          <p class="text-muted-foreground">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary hover:underline">Sign in</a>
          </p>
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

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form');
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('password_confirmation');

      form.addEventListener('submit', function(event) {
        if (password.value !== confirmPassword.value) {
          event.preventDefault();
          alert('Passwords do not match');
        }

        if (password.value.length < 8) {
          event.preventDefault();
          alert('Password must be at least 8 characters');
        }
      });
    });
  </script>
</body>
</html>

