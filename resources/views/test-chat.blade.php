<!DOCTYPE html>
<html>
<head>
    <title>Chat Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Chat Test</h1>

    <div id="chat-container">
        <div id="chat-messages" style="height: 300px; border: 1px solid #ccc; padding: 10px; overflow-y: scroll;"></div>
        <div style="margin-top: 10px;">
            <input type="text" id="userInput" placeholder="Type a message..." style="width: 80%;">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            const input = document.getElementById('userInput');
            const message = input.value.trim();
            const chatBox = document.getElementById('chat-messages');

            if (!message) return;

            // Display user message
            chatBox.innerHTML += `<div><strong>You:</strong> ${escapeHtml(message)}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;

            input.value = '';
            input.disabled = true;

            // Show typing indicator
            const typingId = 'typing-'+Date.now();
            chatBox.innerHTML += `<div id="${typingId}"><strong>Bot:</strong> <em>Typing...</em></div>`;
            chatBox.scrollTop = chatBox.scrollHeight;

            // For debugging
            console.log('Sending message:', message);

            fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
                document.getElementById(typingId).remove();

                // Display bot response
                const reply = data.reply || 'Sorry, I didn\'t understand that.';
                chatBox.innerHTML += `<div><strong>Bot:</strong> ${escapeHtml(reply)}</div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(err => {
                console.error('Chat error:', err);
                document.getElementById(typingId).remove();
                chatBox.innerHTML += `<div><strong>Bot:</strong> Something went wrong. Please try again.</div>`;
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
        document.getElementById('userInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
