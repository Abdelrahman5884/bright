<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini AI Chatbot</title>
    <style>
        :root {
            --primary-color: #4285f4;
            --secondary-color: #34a853;
            --accent-color: #ea4335;
            --light-bg: #f8f9fa;
            --dark-text: #202124;
            --light-text: #5f6368;
            --user-bubble: #e3f2fd;
            --bot-bubble: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--dark-text);
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .chat-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .chat-header {
            background-color: #732d91;
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            max-width: 80%;
            padding: 0.8rem 1.2rem;
            border-radius: 1.2rem;
            line-height: 1.5;
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-message {
            align-self: flex-end;
            background-color: var(--user-bubble);
            color: var(--dark-text);
            border-bottom-right-radius: 0.2rem;
        }

        .bot-message {
            align-self: flex-start;
            background-color: var(--bot-bubble);
            color: var(--dark-text);
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 0.2rem;
        }

        .message-time {
            font-size: 0.7rem;
            color: var(--light-text);
            margin-top: 0.3rem;
            text-align: right;
        }

        .input-area {
            display: flex;
            padding: 1rem;
            border-top: 1px solid #732d91;
            background-color: white;
        }

        #user-input {
            flex: 1;
            padding: 0.8rem 1rem;
            border: 1px solid #732d91;
            border-radius: 2rem;
            font-size: 1rem;
            outline: none;
            transition: border 0.3s;
        }

        #user-input:focus {
            border-color: #732d91;
        }

        #send-button {
            background-color: #732d91;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-left: 0.8rem;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #send-button:hover {
            background-color: #3367d6;
        }

        #send-button svg {
            width: 24px;
            height: 24px;
        }

        .typing-indicator {
            align-self: flex-start;
            background-color: var(--bot-bubble);
            padding: 0.8rem 1.2rem;
            border-radius: 1.2rem;
            display: inline-flex;
            border: 1px solid #e0e0e0;
            margin-bottom: 0.5rem;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: var(--light-text);
            border-radius: 50%;
            margin: 0 2px;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) {
            animation-delay: 0s;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }

        @media (max-width: 768px) {
            .chat-container {
                height: 100vh;
                max-width: 100%;
            }
            
            .message {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
           BrightBot
        </div>
        <div class="chat-messages" id="chat-messages">
            <img src="images/contact-img.svg" alt="" width="630" height="620" style="margin-left: 10%;">
        </div>
        <div class="input-area">
            <input type="text" id="user-input" placeholder="Type your message here..." autocomplete="off">
            <button id="send-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chat-messages');
        const userInput = document.getElementById('user-input');
        const sendButton = document.getElementById('send-button');
        let isTyping = false;

        function addMessage(text, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = isUser ? 'message user-message' : 'message bot-message';
            
            const messageContent = document.createElement('div');
            messageContent.textContent = text;
            
            const timeSpan = document.createElement('div');
            timeSpan.className = 'message-time';
            timeSpan.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            messageDiv.appendChild(messageContent);
            messageDiv.appendChild(timeSpan);
            chatMessages.appendChild(messageDiv);
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTypingIndicator() {
            if (isTyping) return;
            
            isTyping = true;
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.id = 'typing-indicator';
            
            for (let i = 0; i < 3; i++) {
                const dot = document.createElement('div');
                dot.className = 'typing-dot';
                typingDiv.appendChild(dot);
            }
            
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function hideTypingIndicator() {
            isTyping = false;
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;
            
            addMessage(message, true);
            userInput.value = '';
            
            showTypingIndicator();
            
            try {
                const response = await fetch('http://localhost:5000/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                });
                
                const data = await response.json();
                
                hideTypingIndicator();
                
                if (data.status === 'success') {
                    addMessage(data.response);
                } else {
                    addMessage(`Error: ${data.error}`);
                }
            } catch (error) {
                hideTypingIndicator();
                addMessage('Failed to connect to the chatbot server.');
                console.error('Error:', error);
            }
        }

        sendButton.addEventListener('click', sendMessage);
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        userInput.focus();
    </script>
</body>
</html>