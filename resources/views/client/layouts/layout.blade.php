<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/pages-blank.html" />
	<title>@yield('client_page_title')</title>
	<link href="{{asset('admin_asset/css/app.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
		<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">

                <a class="sidebar-brand" href="">
          <span class="align-middle">Parking System</span>
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-item{{request()->routeIs('client.home.manage')?'active':''}}">
                <a class="sidebar-link" href="{{route('client.home.manage')}}">
      <i class="align-middle" data-feather="home"></i> <span class="align-middle">Home page</span>
    </a>
            </li>
				<ul class="sidebar-nav">
					<li class="sidebar-item{{request()->routeIs('client.profile.manage')?'active':''}}">
						<a class="sidebar-link" href="{{route('client.profile.manage')}}">
              <i class="align-middle" data-feather="user"></i> <span class="align-middle">Profile</span>
            </a>
					</li>
                    <ul class="sidebar-nav">
                        <li class="sidebar-item{{request()->routeIs('client.map.manage')?'active':''}}">
                            <a class="sidebar-link" href="{{route('client.map.manage')}}">
                  <i class="align-middle" data-feather="map"></i> <span class="align-middle">Map</span>
                </a>
                        </li>
                    <ul class="sidebar-nav">
                        <li class="sidebar-header">
                            Booking
                        </li>

                        <li class="sidebar-item{{request()->routeIs('client.parking.manage')?'active':''}}">
                            <a class="sidebar-link" href="{{route('client.parking.manage')}}">
                  <i class="align-middle" data-feather="truck"></i> <span class="align-middle">Car Parking</span>
                </a>
                        </li>
                        <li class="sidebar-item{{request()->routeIs('client.history.manage')?'active':''}}">
                            <a class="sidebar-link" href="{{route('client.history.manage')}}">
                  <i class="align-middle" data-feather="list"></i> <span class="align-middle">Booking History</span>
                </a>
                        </li>

				</div>
		</nav>

		<div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
          <i class="hamburger align-self-center"></i>
        </a>

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
								<div class="position-relative">
									<i class="align-middle" data-feather="bell"></i>
									<span class="indicator">2</span>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
								<div class="dropdown-menu-header">
									2 New Notifications
								</div>
								<div class="list-group">
									<a href="#" class="list-group-item">
										<div class="row g-0 align-items-center">
											<div class="col-2">
												<i class="text-warning" data-feather="bell"></i>
											</div>
											<div class="col-10">
												<div class="text-dark">car booked </div>
												<div class="text-muted small mt-1">payment made successfully</div>
												<div class="text-muted small mt-1">2m ago</div>
											</div>
										</div>
									</a>
									<a href="#" class="list-group-item">
										<div class="row g-0 align-items-center">
											<div class="col-2">
												<i class="text-primary" data-feather="home"></i>
											</div>
											<div class="col-10">
												<div class="text-dark">parked successfully</div>
												<div class="text-muted small mt-1">1m ago</div>
											</div>
										</div>
									</a>
								</div>
								<div class="dropdown-menu-footer">
									<a href="#" class="text-muted">Show all notifications</a>
								</div>
							</div>
						</li>


						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                <i class="align-middle" data-feather="settings"></i>
              </a>
							<div class="dropdown-menu dropdown-menu-end">

								<a class="dropdown-item" href="{{route('client.profile.manage')}}"><i class="align-middle me-1" data-feather="user"></i> Profile</a>

								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="index.html"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>
								<a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
								<div class="dropdown-divider"></div>
								<form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn text-white" style="background-color: #1e1e3d;">Logout</button>
                                </form>

							</div>
						</li>
					</ul>
				</div>
			</nav>

			<main class="content">
				<div class="container-fluid p-0">
               @yield('client_layout')
				</div>
			</main>

			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
							</p>
						</div>

					</div>
				</div>
			</footer>
		</div>
	</div>

	<script src="{{asset('admin_asset/js/app.js')}}"></script>

</body>
<!-- Chatbot CSS -->
<style>
#chatbot-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #0d6efd;
    border-radius: 50%;
    padding: 15px;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
#chatbot-icon img {
    width: 25px;
    height: 25px;
}
#chatbot-window {
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 320px;
    height: 450px;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    display: none;
    flex-direction: column;
    overflow: hidden;
}
#chat-messages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    font-size: 14px;
    background-color: #f9f9f9;
}
.user-message {
    margin-bottom: 8px;
    text-align: right;
    color: #0d6efd;
}
.bot-message {
    margin-bottom: 8px;
    text-align: left;
    color: #333;
}
.typing {
    color: #666;
    font-style: italic;
}
.error {
    color: #dc3545;
}
#chat-input {
    display: flex;
    border-top: 1px solid #ccc;
}
#chat-input input {
    flex: 1;
    border: none;
    padding: 10px;
    outline: none;
    font-size: 14px;
}
#chat-input button {
    border: none;
    background-color: #0d6efd;
    color: white;
    padding: 0 16px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
}
#chat-input button:hover {
    background-color: #0b5ed7;
}
</style>

<!-- Chatbot HTML -->
<div id="chatbot-icon">
    <img src="https://cdn-icons-png.flaticon.com/512/4712/4712109.png" alt="Bot Icon">
</div>

<div id="chatbot-window">
    <div id="chat-messages"></div>
    <div id="chat-input">
        <input type="text" id="userInput" placeholder="Ask me about parking..." autocomplete="off" />
        <button id="sendButton">Send</button>
    </div>
</div>

<!-- Chatbot JavaScript -->
<script>
// Initialize chat when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const chatIcon = document.getElementById('chatbot-icon');
    const chatWindow = document.getElementById('chatbot-window');
    const userInput = document.getElementById('userInput');
    const sendButton = document.getElementById('sendButton');

    // Toggle chat window visibility
    function toggleChat() {
        const isVisible = chatWindow.style.display === 'flex';
        chatWindow.style.display = isVisible ? 'none' : 'flex';
        if (!isVisible) {
            userInput.focus();
        }
    }

    // Attach event listeners
    chatIcon.addEventListener('click', toggleChat);
    sendButton.addEventListener('click', sendMessage);
    userInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendMessage();
        }
    });

    // Make functions globally available if needed
    window.toggleChat = toggleChat;
    window.sendMessage = sendMessage;
});

function sendMessage() {
    const input = document.getElementById('userInput');
    const message = input.value.trim();
    const chatBox = document.getElementById('chat-messages');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!message) return;
    if (!csrfToken) {
        console.error('CSRF token not found');
        chatBox.innerHTML += `<div class="bot-message error"><strong>Bot:</strong> System error. Please refresh the page.</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
        return;
    }

    // Display user message
    chatBox.innerHTML += `<div class="user-message"><strong>You:</strong> ${escapeHtml(message)}</div>`;
    chatBox.scrollTop = chatBox.scrollHeight;

    input.value = '';
    input.disabled = true;

    // Show typing indicator
    const typingId = 'typing-'+Date.now();
    chatBox.innerHTML += `<div id="${typingId}" class="bot-message typing"><strong>Bot:</strong> <em>Typing...</em></div>`;
    chatBox.scrollTop = chatBox.scrollHeight;

    fetch('/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        // Remove typing indicator
        document.getElementById(typingId)?.remove();

        // Use data.bot if available, otherwise fallback
        const reply = data.bot || data.reply || 'Sorry, I didn\'t understand that.';
        chatBox.innerHTML += `<div class="bot-message"><strong>Bot:</strong> ${escapeHtml(reply)}</div>`;
    })
    .catch(err => {
        console.error('Chat error:', err);
        document.getElementById(typingId)?.remove();
        chatBox.innerHTML += `<div class="bot-message error"><strong>Bot:</strong> Error: ${escapeHtml(err.message)}</div>`;
    })
    .finally(() => {
        chatBox.scrollTop = chatBox.scrollHeight;
        input.disabled = false;
        input.focus();
    });
}

// Helper function to prevent XSS
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>
