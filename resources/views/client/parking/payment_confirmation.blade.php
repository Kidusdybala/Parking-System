
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Confirm Payment - Smart Parking System</title>
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
            <i class="fas fa-credit-card text-primary text-xl"></i>
          </div>
          <h1 class="text-2xl font-bold">Confirm Parking Payment</h1>
          <p class="text-muted-foreground mt-2">
            Please review your payment details below
          </p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-md text-red-500 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">
          <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-parkBlue-500/10 rounded-md">
              <span class="text-sm font-medium">Your Balance</span>
              <span class="font-bold">{{ number_format($user->balance, 2) }} ETB</span>
            </div>

            <div class="flex justify-between items-center p-3 bg-parkBlue-500/10 rounded-md">
              <span class="text-sm font-medium">Parking Fee</span>
              <span class="font-bold">{{ number_format($reservation->total_price, 2) }} ETB</span>
            </div>

            <div class="flex justify-between items-center p-3 bg-primary/10 rounded-md">
              <span class="text-sm font-medium">Balance After Payment</span>
              <span class="font-bold">{{ number_format($user->balance - $reservation->total_price, 2) }} ETB</span>
            </div>
          </div>

          @if ($user->balance >= $reservation->total_price)
            <form action="{{ route('parking.confirmPayment', $reservation->id) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary w-full">
                Confirm Payment
                <i class="fas fa-check-circle ml-2"></i>
              </button>
            </form>
          @else
            <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-md text-red-500 text-sm mb-4">
              Insufficient balance. Please top up your account first.
            </div>
            <a href="{{ route('client.profile.topup') }}" class="btn btn-primary w-full">
              Top Up Balance
              <i class="fas fa-plus-circle ml-2"></i>
            </a>
          @endif
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
  </script>
</body>
</html>



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

