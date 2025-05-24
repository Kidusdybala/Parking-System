<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System</title>
  <!-- In your Blade file (e.g., welcome.blade.php or layout.blade.php) -->
<link href="{{ asset('css/styles.css') }}" rel="stylesheet">
<link href="{{ asset('css/tailwind.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col">
  <!-- Noise Background -->
  <div class="fixed inset-0 noise-bg pointer-events-none"></div>

  <!-- Header/Navigation -->
  <header class="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
    <div class="container flex items-center justify-between h-16">
      <div class="flex items-center gap-2">
        <i class="fas fa-parking text-primary text-2xl"></i>
        <span class="font-bold text-xl">Miki<span class="text-primary">Park</span></span>
      </div>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center space-x-1">
        <a href="/" class="nav-link active">Home</a>
        @if (Route::has('login'))
          @auth
            <a href="{{ url('/client/parking/manage') }}" class="nav-link">Dashboard</a>
            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
              @csrf
            </form>
          @else
            <a href="{{ route('login') }}" class="nav-link">Login</a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="nav-link">Register</a>
            @endif
          @endauth
        @endif
      </nav>

      <!-- Mobile Navigation Button -->
      <button class="md:hidden p-2 rounded-md hover:bg-accent" id="mobile-menu-button">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="md:hidden hidden" id="mobile-menu">
      <div class="container py-2 space-y-1 border-t border-white/10">
        <a href="/" class="nav-link active block">Home</a>
        @if (Route::has('login'))
          @auth
            <a href="{{ url('/dashboard') }}" class="nav-link block">Dashboard</a>
            <a href="{{ route('logout') }}" class="nav-link block" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              Logout
            </a>
          @else
            <a href="{{ route('login') }}" class="nav-link block">Login</a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="nav-link block">Register</a>
            @endif
          @endauth
        @endif
      </div>
    </div>
  </header>

  <main class="flex-1">
    <!-- Hero Section -->
    <section class="relative py-20 overflow-hidden">
      <div class="container flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="relative w-full max-w-xl">
          <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Modern Parking <span class="text-primary">Simplified</span>
          </h1>
          <p class="text-muted-foreground text-lg mb-8">
            Find and reserve parking spots in real-time. Save time, reduce stress, and never worry about parking again.
          </p>

        <p class="text-muted-foreground text-lg mb-8">note that this parking system only accepts the middle  and small sized vehicle</p>
          <div class="flex flex-wrap gap-4">
            @if (Route::has('login'))
              @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                  Go to Dashboard
                  <i class="fas fa-arrow-right ml-2"></i>
                </a>
              @else
                <a href="{{ route('register') }}" class="btn btn-primary">
                  Get Started
                  <i class="fas fa-arrow-right ml-2"></i>
                </a>
              @endauth
            @endif
            <a href="#how-it-works" class="btn btn-outline">
              How It Works
            </a>
          </div>
        </div>
        <div class="relative w-full max-w-2xl">
          <img src="{{ asset('images/inside-parking.jpg') }}" alt="Parking Garage" class="rounded-lg shadow-xl">
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20">
      <div class="container">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">How It Works</h2>
          <p class="text-muted-foreground text-lg">
            Our smart parking system makes finding and reserving parking spots quick and easy.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Step 1 -->
          <div class="card-container relative">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center absolute -top-5 -left-5">
              <span class="font-bold">1</span>
            </div>
            <div class="mb-4 text-center">
              <i class="fas fa-search text-4xl text-primary mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Find a Spot</h3>
              <p class="text-muted-foreground">
                Browse available parking spots in real-time on our interactive map
              </p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="card-container relative">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center absolute -top-5 -left-5">
              <span class="font-bold">2</span>
            </div>
            <div class="mb-4 text-center">
              <i class="fas fa-calendar-check text-4xl text-primary mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Reserve It</h3>
              <p class="text-muted-foreground">
                Book your spot in advance with just a few clicks
              </p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="card-container relative">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center absolute -top-5 -left-5">
              <span class="font-bold">3</span>
            </div>
            <div class="mb-4 text-center">
              <i class="fas fa-car text-4xl text-primary mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Park & Go</h3>
              <p class="text-muted-foreground">
                Arrive at your reserved spot and enjoy hassle-free parking
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-parkBlue-800/50">
      <div class="container">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">Smart Features</h2>
          <p class="text-muted-foreground text-lg">
            Our system is packed with features to make your parking experience seamless.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Feature 1 -->
          <div class="card-container">
            <div class="mb-4">
              <i class="fas fa-clock text-3xl text-primary mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Real-time Availability</h3>
              <p class="text-muted-foreground">
                See available spots updated in real-time
              </p>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="card-container">
            <div class="mb-4">
              <i class="fas fa-mobile-alt text-3xl text-primary mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Mobile Friendly</h3>
              <p class="text-muted-foreground">
                Book and manage reservations from any device
              </p>
            </div>
          </div>

          <!-- Feature 3 -->
          <div class="card-container">
            <div class="mb-4">
              <i class="fas fa-credit-card text-3xl text-primary mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Secure Payments</h3>
              <p class="text-muted-foreground">
                Pay safely with our encrypted payment system
              </p>
            </div>
          </div>

          <!-- Feature 4 -->
          <div class="card-container">
            <div class="mb-4">
              <i class="fas fa-history text-3xl text-primary mb-4"></i>
              <h3 class="text-xl font-bold mb-2">Booking History</h3>
              <p class="text-muted-foreground">
                Track all your past and upcoming reservations
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20">
      <div class="container">
        <div class="glass-card relative overflow-hidden p-8 md:p-12">
          <div class="relative z-10 max-w-2xl">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to simplify your parking?</h2>
            <p class="text-muted-foreground mb-8">
              Join thousands of drivers who have made parking stress-free with our smart system.
            </p>
            <div class="flex flex-wrap gap-4">
              @if (Route::has('login'))
                @auth
                  <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                    Go to Dashboard
                  </a>
                @else
                  <a href="{{ route('register') }}" class="btn btn-primary">
                    Create Account
                  </a>
                  <a href="{{ route('login') }}" class="btn btn-outline">
                    Sign In
                  </a>
                @endauth
              @endif
            </div>
          </div>
          <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
          <div class="absolute -top-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="border-t border-white/10 py-8">
    <div class="container">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="flex items-center gap-2 mb-4 md:mb-0">
          <i class="fas fa-parking text-primary text-xl"></i>
          <span class="font-bold text-lg">Miki<span class="text-primary">Park</span></span>
        </div>
        <div class="text-sm text-muted-foreground">
          &copy; {{ date('Y') }} MikiPark. All rights reserved.
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Simple JavaScript for mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('hidden');
    });
  </script>
</body>
</html>







