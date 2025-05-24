<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Upload Payment Proof - Smart Parking System</title>
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container mx-auto px-4 py-8">
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


                      <a href="{{ route('client.profile.manage') }}" class="nav-link active flex items-center w-full">
                            <i class="fas fa-user-circle w-5"></i>
                            <span class="ml-3">Manage Profile</span>

                        </a>
                           <a href="{{ route('client.parking.manage') }}" class="nav-link  flex items-center w-full">
                            <i class="fas fa-user-circle w-5"></i>
                            <span class="ml-3">Manage Parking</span>
                        </a>

                          <a href="{{ route('client.history.manage') }}" class="nav-link flex items-center w-full">
                            <i class="fas fa-history w-5"></i>
                            <span class="ml-3">Booking History</span>
                        </a>
                    </nav>
                </div>

                <!-- Balance Card -->
                <div class="glass-card p-4">
                    <h3 class="font-bold mb-4">Account Balance</h3>
                    <div class="bg-parkBlue-800/50 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-primary mb-2">{{ number_format(Auth::user()->balance, 2) }} Birr</div>
                        <p class="text-sm text-muted-foreground mb-4">Available Balance</p>
                        <a href="{{ route('client.profile.topup') }}" class="btn btn-primary w-full inline-flex items-center justify-center">
                            <i class="fas fa-plus-circle mr-2"></i> Top Up Balance
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3 space-y-8">
            <!-- Page Title -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Manage Profile</h1>
            </div>

            @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/50 text-green-200 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <!-- Profile Information Card -->
            <div class="glass-card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Profile Information</h2>
                    <button id="edit-profile-btn" class="btn btn-outline btn-sm inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </button>
                </div>


                <!-- Profile Edit Mode (Initially Hidden) -->

            </div>

            <!-- Password Change Card -->
            <div class="glass-card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Change Password</h2>
                </div>

                <form id="password-form" action="{{ route('client.profile.update-password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="current_password" class="block text-sm font-medium mb-1">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="input w-full" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium mb-1">New Password</label>
                        <input type="password" id="password" name="password" class="input w-full" required>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="input w-full" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-full inline-flex items-center justify-center">
                        Update Password
                    </button>
                </form>
            </div>


        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        const profileView = document.getElementById('profile-view');
        const profileEdit = document.getElementById('profile-edit');

        editProfileBtn.addEventListener('click', function() {
            profileView.classList.add('hidden');
            profileEdit.classList.remove('hidden');
        });

        cancelEditBtn.addEventListener('click', function() {
            profileEdit.classList.add('hidden');
            profileView.classList.remove('hidden');
        });
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


