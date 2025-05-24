  <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - Smart Parking System</title>
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col">
  <!-- Noise Background -->
  <div class="fixed inset-0 noise-bg pointer-events-none"></div>
  <!-- Add this near the top of your body, but after the noise background div -->
  @if(session('error'))
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-4 py-3 rounded shadow-lg z-50 max-w-md">
        {{ session('error') }}
    </div>
  @endif
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
                 <a href="{{ route('client.profile.manage') }}" class="nav-link flex items-center w-full">
                <i class="fas fa-history w-5"></i>
                <span class="ml-3">Manage profile</span>
              </a>
              <a href="{{ route('client.parking.manage') }}" class="nav-link active flex items-center w-full">
                <i class="fas fa-ticket-alt w-5"></i>
                <span class="ml-3">Parking</span>
              </a>
              <a href="{{ route('client.history.manage') }}" class="nav-link flex items-center w-full">
                <i class="fas fa-history w-5"></i>
                <span class="ml-3">Booking History</span>
              </a>
            </nav>
          </div>


        </div>
      </div>

      <!-- Main Content Area -->
      <div class="lg:col-span-3 space-y-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="glass-card p-4">
            <div class="flex items-center">
              <div class="mr-4 h-12 w-12 bg-primary/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-car text-primary"></i>
              </div>
              <div>
                <p class="text-muted-foreground text-sm">Available Slots</p>
                <p class="text-2xl font-bold" id="available-count">{{ $spots->where('is_reserved', false)->count() }}</p>
              </div>
            </div>
          </div>

          <div class="glass-card p-4">
            <div class="flex items-center">
              <div class="mr-4 h-12 w-12 bg-parkRed/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-car-alt text-parkRed"></i>
              </div>
              <div>
                <p class="text-muted-foreground text-sm">Occupied Slots</p>
                <p class="text-2xl font-bold" id="occupied-count">
                  {{ $spots->filter(function($spot) {
                    return $spot->is_reserved && $spot->reservation && $spot->reservation->parked_at;
                  })->count() }}
                </p>
              </div>
            </div>
          </div>

          <div class="glass-card p-4">
            <div class="flex items-center">
              <div class="mr-4 h-12 w-12 bg-[#f59e0b]/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-bookmark text-[#f59e0b]"></i>
              </div>
              <div>
                <p class="text-muted-foreground text-sm">Reserved Slots</p>
                <p class="text-2xl font-bold" id="reserved-count">
                  {{ $spots->filter(function($spot) {
                    return $spot->is_reserved && $spot->reservation && !$spot->reservation->parked_at;
                  })->count() }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Parking Map Section -->
        <div class="card-container">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Available Parking Spots</h2>
            <div class="flex items-center space-x-4">
              <div class="flex items-center space-x-2">
                <div class="h-3 w-3 rounded-full bg-parkGreen"></div>
                <span class="text-sm text-muted-foreground">Available</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="h-3 w-3 rounded-full bg-parkRed"></div>
                <span class="text-sm text-muted-foreground">Occupied</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="h-3 w-3 rounded-full bg-[#f59e0b]"></div>
                <span class="text-sm text-muted-foreground">Reserved</span>
              </div>
            </div>
          </div>

          <div class="glass-card p-6">
            <div class="flex space-x-2 mb-6">
              <button class="btn btn-sm section-btn active" data-section="1">Section A</button>

            </div>

            <!-- Parking Slots Grid -->
            <div class="parking-section active" id="section-1">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($spots as $spot)
                  <div class="glass-card p-4">
                    <div class="card-header bg-light text-center py-2">
                      <h5 class="card-title">{{ $spot->name }}</h5>
                    </div>
                    <div class="card-body">
                      <p class="card-text">Place ID: {{ $spot->id }}</p>
                      <p class="card-text"><strong>Price: {{ $spot->price_per_hour }} ETB/Hour</strong></p>

                      @if($spot->is_reserved && $spot->reservation)
                        @if(auth()->user() && auth()->user()->id == $spot->reservation->user_id)
                          @if($spot->reservation->parked_at)
                            <!-- Only show Finish Parking Button when parked -->
                            <form action="{{ route('parking.finish', $spot->reservation->id) }}" method="POST" class="d-inline">
                              @csrf
                              <button class="btn btn-danger w-full">Finish Parking</button>
                            </form>
                          @else
                            <!-- Show countdown timer -->
                            <div id="countdown-{{ $spot->id }}" class="mb-2 text-danger fw-bold"></div>

                            <!-- Hidden form for auto-cancel -->
                            <form id="autoCancelForm-{{ $spot->id }}" action="{{ route('parking.cancel', $spot->reservation->id) }}" method="POST" style="display:none;">
                              @csrf
                            </form>

                            <!-- Cancel Reservation -->
                            <form action="{{ route('parking.cancel', $spot->reservation->id) }}" method="POST" class="d-inline mb-2">
                              @csrf
                              <button class="btn btn-secondary w-full">Cancel Reservation</button>
                            </form>

                            <!-- Park Now Button -->
                            <form action="{{ route('parking.park', $spot->reservation->id) }}" method="POST" class="d-inline">
                              @csrf
                              <button class="btn btn-success w-full">Park Now</button>
                            </form>
                          @endif
                        @else
                          <span class="badge bg-danger">Reserved</span>
                        @endif
                      @else
                        <!-- Reserve Now -->
                        <form action="{{ route('parking.reserve', $spot->id) }}" method="POST" class="reserve-form">
                          @csrf
                          <button type="submit" class="btn btn-primary w-auto mx-auto px-3 reserve-button">
                            Reserve Now
                          </button>
                        </form>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Reservation Modal -->
  <div id="reservation-modal" class="modal-overlay hidden">
    <div class="modal-container">
      <button type="button" class="absolute top-4 right-4 text-muted-foreground hover:text-foreground" id="close-reservation-modal">
        <i class="fas fa-times"></i>
      </button>

      <h2 class="text-xl font-bold mb-6">Reserve Parking Spot</h2>

      <form id="reservation-form" action="{{ route('parking.reserve', 0) }}" method="POST">
        @csrf

        <div class="space-y-4">
          <div>
            <label for="parking-slot" class="block text-sm font-medium mb-1">Parking Slot</label>
            <select id="parking-slot" name="parking_slot" class="input w-full" required>
              <option value="">Select a parking spot</option>
              @foreach($spots->where('is_reserved', false) as $spot)
                <option value="{{ $spot->id }}">{{ $spot->name }} - {{ $spot->price_per_hour }} ETB/hour</option>
              @endforeach
            </select>
          </div>

          <div class="pt-4">
            <button type="submit" class="btn btn-primary w-full">
              <i class="fas fa-check-circle mr-2"></i> Confirm Reservation
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- JavaScript for reservation form -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Handle reservation form submission
      const reservationForm = document.getElementById('reservation-form');
      if (reservationForm) {
        reservationForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const spotId = document.getElementById('parking-slot').value;
          if (!spotId) {
            alert('Please select a parking spot');
            return;
          }

          // Update the form action with the selected spot ID
          this.action = "{{ route('parking.reserve', '') }}/" + spotId;
          console.log("Submitting form to: " + this.action); // Debug
          this.submit();
        });
      }

      // Also handle the "Reserve Now" buttons directly on parking spots
      const directReserveButtons = document.querySelectorAll('form[action*="parking.reserve"] button');
      directReserveButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          console.log("Direct reserve button clicked"); // Debug
        });
      });
    });
  </script>

  <!-- Countdown Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      @foreach($spots as $spot)
        @if($spot->is_reserved && $spot->reservation && auth()->user() && auth()->user()->id == $spot->reservation->user_id && is_null($spot->reservation->parked_at))
          startCountdown({{ $spot->id }}, 60); // 1 minute = 60 seconds
        @endif
      @endforeach

      function startCountdown(spotId, seconds) {
        const display = document.getElementById(`countdown-${spotId}`);
        const cancelForm = document.getElementById(`autoCancelForm-${spotId}`);

        const interval = setInterval(() => {
          let mins = Math.floor(seconds / 60);
          let secs = seconds % 60;
          if (display) {
            display.textContent = `Auto-cancel in: ${mins}:${secs < 10 ? '0' + secs : secs}`;
          }
          seconds--;

          if (seconds < 0) {
            clearInterval(interval);
            if (cancelForm) cancelForm.submit(); // auto cancel
          }
        }, 1000);
      }

      // Add click event to section buttons
      const sectionBtns = document.querySelectorAll('.section-btn');
      const parkingSections = document.querySelectorAll('.parking-section');

      sectionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const sectionNum = this.getAttribute('data-section');

          // Remove active class from all buttons and add to clicked button
          sectionBtns.forEach(b => b.classList.remove('active'));
          this.classList.add('active');

          // Hide all sections and show the selected one
          parkingSections.forEach(section => section.classList.add('hidden'));
          document.getElementById(`section-${sectionNum}`).classList.remove('hidden');
        });
      });

      // New reservation button
      const newReservationBtn = document.getElementById('new-reservation-btn');
      const reservationModal = document.getElementById('reservation-modal');
      const closeReservationModal = document.getElementById('close-reservation-modal');

      if (newReservationBtn) {
        newReservationBtn.addEventListener('click', function() {
          reservationModal.classList.remove('hidden');
        });
      }

      if (closeReservationModal) {
        closeReservationModal.addEventListener('click', function() {
          reservationModal.classList.add('hidden');
        });
      }

      // Handle reservation form submission
      const reservationForm = document.getElementById('reservation-form');
      if (reservationForm) {
        reservationForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const spotId = document.getElementById('parking-slot').value;
          if (!spotId) {
            alert('Please select a parking spot');
            return;
          }

          // Update the form action with the selected spot ID
          this.action = "{{ route('parking.reserve', '') }}/" + spotId;
          this.submit();
        });
      }
    });
  </script>


<script>
    function sendMessage() {
        const input = document.getElementById('userInput');
        const message = input.value.trim();
        const chatBox = document.getElementById('chat-messages');

        if (!message) return;

        // Display user message
        chatBox.innerHTML += `<div class="user-message"><strong>You:</strong> ${escapeHtml(message)}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

        input.value = '';
        input.disabled = true;

        // Show typing indicator
        const typingId = 'typing-'+Date.now();
        chatBox.innerHTML += `<div id="${typingId}" class="bot-message"><strong>Bot:</strong> <em>Typing...</em></div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

        // For debugging
        console.log('Sending message:', message);

        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('Error response:', text);
                    throw new Error(`Server error: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            // Remove typing indicator
            const typingElement = document.getElementById(typingId);
            if (typingElement) typingElement.remove();

            // Display bot response
            const reply = data.reply || 'Sorry, I didn\'t understand that.';
            chatBox.innerHTML += `<div class="bot-message"><strong>Bot:</strong> ${escapeHtml(reply)}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(err => {
            console.error('Chat error:', err);
            const typingElement = document.getElementById(typingId);
            if (typingElement) typingElement.remove();
            chatBox.innerHTML += `<div class="bot-message error"><strong>Bot:</strong> Something went wrong. Please try again.</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .finally(() => {
            input.disabled = false;
            input.focus();
        });
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Add event listener for Enter key
    document.getElementById('userInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
</script>












<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all reserve forms
    const reserveForms = document.querySelectorAll('.reserve-form');

    reserveForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Prevent default form submission temporarily to handle the UI updates
            e.preventDefault();

            console.log('Reserve form submitted:', form.action);

            // Disable the button to prevent double submission
            const button = form.querySelector('button');
            if (button) {
                button.disabled = true;
                button.textContent = 'Reserving...';
            }

            // Now manually submit the form
            this.submit();
        });
    });

    // For the modal reservation form
    const modalReservationForm = document.getElementById('reservation-form');
    if (modalReservationForm) {
        modalReservationForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const spotId = document.getElementById('parking-slot').value;
            if (!spotId) {
                alert('Please select a parking spot');
                return;
            }

            // Update the form action with the selected spot ID
            this.action = "{{ route('parking.reserve', '') }}/" + spotId;
            console.log('Submitting modal form to:', this.action);

            // Disable the button
            const button = this.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.textContent = 'Reserving...';
            }

            // Submit the form
            this.submit();
        });
    }

    // Check for JavaScript alert in session
    @if(session('js_alert'))
        alert("{{ session('js_alert') }}");
    @endif

});
</script>





<script>
document.addEventListener('DOMContentLoaded', function() {
    // Find all alert messages
    const alerts = document.querySelectorAll('[role="alert"]');

    if (alerts.length > 0) {
        // Scroll to the first alert
        alerts[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Add a subtle animation
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

            setTimeout(() => {
                alert.style.opacity = '1';
                alert.style.transform = 'translateY(0)';
            }, 100);

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';

                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    }
});
</script>





<!-- Add this at the top of your content section -->
@if(session('error'))
    <div class="bg-red-600 text-white px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<script>

// Close warning popup

// Add event listeners to all reserve buttons
document.addEventListener('DOMContentLoaded', function() {
  const reserveForms = document.querySelectorAll('.reserve-form');

  reserveForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      // Get the spot ID from the form action URL
      const spotId = this.action.split('/').pop();

      // Check if user already has a reservation in this section
      // This is a client-side check that will be duplicated on the server
      const hasReservation = checkUserReservation(spotId);

    });
  });

  // Check for JavaScript alert in session
  @if(session('js_alert'))
    showWarning("{{ session('js_alert') }}");
  @endif
});

// Mock function to check if user has reservation
// In a real app, you would use AJAX to check with the server
function checkUserReservation(spotId) {
  // This is just a placeholder - in a real app you'd check with the server
  // For now, we'll rely on the server-side check
  return false;
}

// Add this to handle form submissions with AJAX
document.querySelectorAll('.reserve-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    // We'll still let the form submit normally, but we'll also
    // add an event listener to catch errors from the server

    // If you want to implement AJAX form submission instead:
    /*
    e.preventDefault();

    fetch(this.action, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        _token: document.querySelector('input[name="_token"]').value
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        showWarning(data.error);
      } else if (data.success) {
        // Reload the page or update the UI
        window.location.reload();
      }
    })
    .catch(error => {
      showWarning('An error occurred. Please try again.');
      console.error('Error:', error);
    });
    */
  });
});
</script>

<!-- For Bootstrap -->
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<script>
// For Bootstrap alerts
document.addEventListener('DOMContentLoaded', function() {
    // Find all alert messages
    const alerts = document.querySelectorAll('.alert');

    if (alerts.length > 0) {
        // Scroll to the first alert
        alerts[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alerts.forEach(alert => {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                }
            });
        }, 5000);
    }
});
</script>

<script>
    function sendMessage() {
        const input = document.getElementById('userInput');
        const message = input.value.trim();
        const chatBox = document.getElementById('chat-messages');

        if (!message) return;

        // Display user message
        chatBox.innerHTML += `<div class="user-message"><strong>You:</strong> ${escapeHtml(message)}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

        input.value = '';
        input.disabled = true;

        // Show typing indicator
        const typingId = 'typing-'+Date.now();
        chatBox.innerHTML += `<div id="${typingId}" class="bot-message"><strong>Bot:</strong> <em>Typing...</em></div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

        // For debugging
        console.log('Sending message:', message);

        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('Error response:', text);
                    throw new Error(`Server error: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            // Remove typing indicator
            const typingElement = document.getElementById(typingId);
            if (typingElement) typingElement.remove();

            // Display bot response
            const reply = data.reply || 'Sorry, I didn\'t understand that.';
            chatBox.innerHTML += `<div class="bot-message"><strong>Bot:</strong> ${escapeHtml(reply)}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(err => {
            console.error('Chat error:', err);
            const typingElement = document.getElementById(typingId);
            if (typingElement) typingElement.remove();
            chatBox.innerHTML += `<div class="bot-message error"><strong>Bot:</strong> Something went wrong. Please try again.</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .finally(() => {
            input.disabled = false;
            input.focus();
        });
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Add event listener for Enter key
    document.getElementById('userInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
</script>








<!-- Add this right before the closing </body> tag -->

<!-- Simple Chatbot Implementation -->
<style>
    /* Basic chatbot styling */
    #chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    #chat-icon {
        width: 50px;
        height: 50px;
        background-color: #000;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    #chat-icon img {
        width: 30px;
        height: 30px;
        filter: invert(1);
    }

    #chat-window {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        height: 400px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
    }

    #chat-header {
        background-color: #000;
        color: #fff;
        padding: 10px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        display: flex;
        justify-content: space-between;
    }

    #chat-close {
        cursor: pointer;
    }

    #chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
        background-color: #f5f5f5;
    }

    .message {
        margin-bottom: 10px;
        padding: 8px 12px;
        border-radius: 10px;
        max-width: 80%;
        word-wrap: break-word;
    }

    .user-message {
        background-color: #e6e6e6;
        color: #000;
        margin-left: auto;
        text-align: right;
    }

    .bot-message {
        background-color: #333; /* Changed from black to dark gray */
        color: #fff; /* White text for contrast */
        margin-right: auto;
        text-align: left;
    }

    #chat-form {
        display: flex;
        border-top: 1px solid #ccc;
        background-color: #fff; /* Ensure white background */
    }

    #chat-input {
        flex: 1;
        border: none;
        padding: 10px;
        outline: none;
        color: #000; /* Ensure black text */
        background-color: #fff; /* Ensure white background */
    }

    #chat-submit {
        background-color: #000;
        color: #fff;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
    }
</style>

<div id="chatbot-container">
    <div id="chat-icon">
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712109.png" alt="Chat">
    </div>

    <div id="chat-window">
        <div id="chat-header">
            <div>Parking Assistant</div>
            <div id="chat-close">✕</div>
        </div>

        <div id="chat-messages">
            <div class="message bot-message">Hello! How can I help you with parking today?</div>
        </div>

        <form id="chat-form">
            <input type="text" id="chat-input" placeholder="Type your message..." style="color: #000; background-color: #fff;">
            <button type="submit" id="chat-submit">Send</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get elements
        const chatIcon = document.getElementById('chat-icon');
        const chatWindow = document.getElementById('chat-window');
        const chatClose = document.getElementById('chat-close');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');

        // Toggle chat window
        chatIcon.addEventListener('click', function() {
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
            if (chatWindow.style.display === 'flex') {
                chatInput.focus();
            }
        });

        // Close chat window
        chatClose.addEventListener('click', function() {
            chatWindow.style.display = 'none';
        });

        // Handle form submission
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const message = chatInput.value.trim();
            if (!message) return;

            // Add user message
            const userDiv = document.createElement('div');
            userDiv.className = 'message user-message';
            userDiv.textContent = message;
            chatMessages.appendChild(userDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Clear input
            chatInput.value = '';

            // Show loading message
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message bot-message';
            loadingDiv.textContent = 'Typing...';
            loadingDiv.id = 'loading-message';
            chatMessages.appendChild(loadingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').content;

            // Send to server
            fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Remove loading message
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    chatMessages.removeChild(loadingMessage);
                }

                // Add bot response
                const botDiv = document.createElement('div');
                botDiv.className = 'message bot-message';

                // Check if we have a bot response in the data
                if (data.bot) {
                    botDiv.textContent = data.bot;
                } else if (data.reply) {
                    botDiv.textContent = data.reply;
                } else if (data.error) {
                    botDiv.textContent = 'Error: ' + data.error;
                } else {
                    botDiv.textContent = 'Sorry, I didn\'t understand that.';
                }

                chatMessages.appendChild(botDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);

                // Remove loading message
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    chatMessages.removeChild(loadingMessage);
                }

                // Add error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'message bot-message';
                errorDiv.textContent = 'Sorry, there was an error. Please try again.';
                chatMessages.appendChild(errorDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        });
    });
</script>

