<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking History - Smart Parking System</title>
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen flex flex-col bg-parkBlue-900">
  <!-- Noise Background -->
  <div class="fixed inset-0 noise-bg pointer-events-none"></div>

  <!-- Header/Navigation -->
  <header class="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
    <div class="container flex items-center justify-between h-16">
      <div class="flex items-center gap-2">

          <i class="fas fa-parking text-primary text-2xl"></i>
          <span class="font-bold text-xl">Miki<span class="text-primary">Park</span></span>
        </a>
      </div>

      <!-- Desktop Navigation -->
      <div class="hidden md:flex items-center space-x-4">
        <span class="text-muted-foreground">Welcome, <span class="text-foreground">{{ Auth::user()->name }}</span></span>
        <div class="relative group">
          <button class="h-8 w-8 rounded-full bg-parkBlue-700 flex items-center justify-center hover:bg-primary/80 transition-colors">
            <i class="fas fa-user"></i>
          </button>
          <div class="absolute right-0 mt-2 w-48 py-2 bg-card rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
            <a href="{{ route('client.profile.manage') }}" class="block px-4 py-2 hover:bg-accent text-sm">
              <i class="fas fa-user-circle mr-2"></i> Profile
            </a>

            <div class="border-t border-white/10 my-1"></div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 hover:bg-accent text-sm text-parkRed">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
          </div>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <button class="md:hidden p-2 rounded-md hover:bg-accent" id="mobile-menu-button">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="md:hidden hidden" id="mobile-menu">
      <div class="container py-2 space-y-1 border-t border-white/10">
        <div class="flex items-center justify-between py-2">
          <span class="text-muted-foreground">Welcome, <span class="text-foreground">{{ Auth::user()->name }}</span></span>
        </div>
        <a href="{{ route('client.profile.manage') }}" class="nav-link block">
          <i class="fas fa-user-circle mr-2"></i> Profile
        </a>

        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();" class="nav-link block text-parkRed">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
        <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="flex-1 container py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <div class="sticky top-24">
          <div class="glass-card p-4 mb-4">
            <!-- User Info -->
            <div class="flex items-center space-x-4 mb-6">
              <div class="h-14 w-14 rounded-full bg-parkBlue-700 flex items-center justify-center text-xl">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <h2 class="font-bold">{{ Auth::user()->name }}</h2>
                <p class="text-sm text-muted-foreground">{{ Auth::user()->email }}</p>
              </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-1">


                      <a href="{{ route('client.profile.manage') }}" class="nav-link  flex items-center w-full">
                            <i class="fas fa-user-circle w-5"></i>
                            <span class="ml-3">Manage Profile</span>

                        </a>
                           <a href="{{ route('client.parking.manage') }}" class="nav-link  flex items-center w-full">
                            <i class="fas fa-user-circle w-5"></i>
                            <span class="ml-3">Manage Parking</span>
                        </a>
              <a href="{{ route('client.history.manage') }}" class="nav-link active flex items-center w-full">
                <i class="fas fa-history w-5"></i>
                <span class="ml-3">Booking History</span>
              </a>

            </nav>
          </div>

          <!-- Stats Card -->
          <div class="glass-card p-4">
            <h3 class="font-bold mb-4">Parking Stats</h3>
            <div class="space-y-4">
              @php
                $totalBookings = App\Models\Reservation::where('user_id', Auth::id())->count();
                $thisMonthBookings = App\Models\Reservation::where('user_id', Auth::id())
                  ->whereMonth('created_at', now()->month)
                  ->count();
                $totalSpent = App\Models\Reservation::where('user_id', Auth::id())
                  ->where('is_paid', true)
                  ->sum('total_price');

                // Get favorite location (most used parking spot)
                $favoriteSpot = App\Models\Reservation::where('user_id', Auth::id())
                  ->select('parking_spot_id', DB::raw('count(*) as total'))
                  ->groupBy('parking_spot_id')
                  ->orderBy('total', 'desc')
                  ->first();
              @endphp

              <div class="bg-parkBlue-800/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-muted-foreground">Total Bookings</span>
                  <span class="font-bold text-lg">{{ $totalBookings }}</span>
                </div>
                <div class="w-full bg-muted/20 h-1.5 rounded-full overflow-hidden">
                  <div class="bg-primary h-full" style="width: 100%"></div>
                </div>
              </div>

              <div class="bg-parkBlue-800/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-muted-foreground">This Month</span>
                  <span class="font-bold text-lg">{{ $thisMonthBookings }}</span>
                </div>
                <div class="w-full bg-muted/20 h-1.5 rounded-full overflow-hidden">
                  <div class="bg-primary h-full" style="width: {{ $totalBookings > 0 ? ($thisMonthBookings / $totalBookings) * 100 : 0 }}%"></div>
                </div>
              </div>

              <div class="bg-parkBlue-800/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-muted-foreground">Total Spent</span>
                  <span class="font-bold text-lg">{{ number_format($totalSpent, 2) }} Birr</span>
                </div>
                <div class="flex justify-between text-xs text-muted-foreground mt-1">
                  <span>Average: {{ $totalBookings > 0 ? number_format($totalSpent / $totalBookings, 2) : 0 }} Birr/booking</span>
                </div>
              </div>

              @if($favoriteSpot && $favoriteSpot->parkingSpot)
              <div class="bg-parkBlue-800/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-muted-foreground">Favorite Location</span>
                  <span class="font-bold">{{ $favoriteSpot->parkingSpot->name }}</span>
                </div>
                <div class="flex justify-between text-xs text-muted-foreground mt-1">
                  <span>Used {{ $favoriteSpot->total }} times</span>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="lg:col-span-3 space-y-8">
        <!-- Page Title -->
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold">Booking History</h1>
          <div class="flex space-x-2">
            <div class="relative">
              <select class="input text-sm py-1 pr-8 appearance-none" id="time-filter">
                <option value="all">All Time</option>
                <option value="month">This Month</option>
                <option value="last-month">Last Month</option>
                <option value="3-months">Last 3 Months</option>
                <option value="6-months">Last 6 Months</option>
              </select>
              <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                <i class="fas fa-chevron-down text-xs text-muted-foreground"></i>
              </div>
            </div>

          </div>
        </div>

        <!-- Booking History Table -->
        <div class="glass-card overflow-hidden">
          @if($history->isEmpty())
            <div class="p-8 text-center">
              <div class="mb-4 text-muted-foreground">
                <i class="fas fa-history text-5xl"></i>
              </div>
              <h3 class="text-xl font-bold mb-2">No Booking History</h3>
              <p class="text-muted-foreground">You haven't completed any parking sessions yet.</p>
            </div>
          @else
            <div class="table-container">
              <table class="table w-full">
                <thead>
                  <tr>
                    <th>
                      <div class="flex items-center">
                        Booking ID
                        <button class="ml-1 text-muted-foreground sort-btn" data-sort="id">
                          <i class="fas fa-sort"></i>
                        </button>
                      </div>
                    </th>
                    <th>Parking Spot</th>
                    <th>Place ID</th>
                    <th>
                      <div class="flex items-center">
                        Parked At
                        <button class="ml-1 text-muted-foreground sort-btn" data-sort="parked_at">
                          <i class="fas fa-sort-down"></i>
                        </button>
                      </div>
                    </th>
                    <th>Left At</th>
                    <th>Duration</th>
                    <th>Total Paid (ETB)</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($history as $reservation)
                    <tr>
                      <td>BK-{{ $reservation->id }}</td>
                      <td>{{ $reservation->parkingSpot->name ?? 'Unknown' }}</td>
                      <td>{{ $reservation->parking_spot_id }}</td>
                      <td>{{ \Carbon\Carbon::parse($reservation->parked_at)->format('M d, Y h:i A') }}</td>
                      <td>{{ \Carbon\Carbon::parse($reservation->left_at)->format('M d, Y h:i A') }}</td>
                      <td>{{ \Carbon\Carbon::parse($reservation->parked_at)->diffInMinutes($reservation->left_at) }} minutes</td>
                      <td>
                        <span class="badge badge-available">{{ number_format($reservation->total_price, 2) }} Birr</span>
                      </td>
                      <td>
                        <button class="btn-icon text-primary view-receipt" data-id="{{ $reservation->id }}">
                          <i class="fas fa-receipt"></i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Receipt Modal -->
  <div id="receipt-modal" class="modal-overlay hidden">
    <div class="modal-container">
      <button type="button" class="absolute top-4 right-4 text-muted-foreground hover:text-foreground" id="close-receipt-modal">
        <i class="fas fa-times"></i>
      </button>

      <h2 class="text-xl font-bold mb-6">Parking Receipt</h2>

      <div class="bg-parkBlue-800/50 rounded-lg p-6 mb-6">
        <div class="


