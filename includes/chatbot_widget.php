<?php
// Fetch questions for quick reply buttons
$faq_res = $conn->query("SELECT id, question FROM chatbot_faq ORDER BY id ASC LIMIT 5");
$quick_replies = [];
if ($faq_res && $faq_res->num_rows > 0) {
    while ($row = $faq_res->fetch_assoc()) {
        $quick_replies[] = $row;
    }
}
?>

<style>
/* Chatbot Widget Styles */
.chatbot-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'Outfit', sans-serif;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--accent-gold, #f6a821);
    color: var(--primary-blue, #1a365d);
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 28px;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(246, 168, 33, 0.4);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    animation: pulse-bot 2s infinite;
}

@keyframes pulse-bot {
    0% { box-shadow: 0 0 0 0 rgba(246, 168, 33, 0.7); }
    70% { box-shadow: 0 0 0 15px rgba(246, 168, 33, 0); }
    100% { box-shadow: 0 0 0 0 rgba(246, 168, 33, 0); }
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    animation: none;
}

.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(20px);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
}

.chatbot-widget.active .chatbot-window {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
}

.chatbot-header {
    background: var(--primary-blue, #1a365d);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header h3 {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chatbot-close {
    cursor: pointer;
    font-size: 1.2rem;
    opacity: 0.8;
}
.chatbot-close:hover { opacity: 1; }

.chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.chat-bubble {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 15px;
    font-size: 0.95rem;
    line-height: 1.4;
    word-wrap: break-word;
}

.chat-bubble.bot {
    background: white;
    border: 1px solid #eee;
    color: #333;
    align-self: flex-start;
    border-bottom-left-radius: 5px;
}

.chat-bubble.user {
    background: var(--accent-gold, #f6a821);
    color: #fff;
    align-self: flex-end;
    border-bottom-right-radius: 5px;
}

.quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 5px;
    padding: 0 15px 10px 15px;
    background: #f8f9fa;
}

.quick-reply-btn {
    background: white;
    border: 1px solid var(--primary-blue, #1a365d);
    color: var(--primary-blue, #1a365d);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    font-family: 'Outfit', sans-serif;
}

.quick-reply-btn:hover {
    background: var(--primary-blue, #1a365d);
    color: white;
}

.chatbot-input {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}

.chatbot-input input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 10px 15px;
    font-family: 'Outfit', sans-serif;
    outline: none;
}

.chatbot-input input:focus {
    border-color: var(--primary-blue, #1a365d);
}

.chatbot-send {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-blue, #1a365d);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background 0.2s;
}

.chatbot-send:hover {
    background: var(--secondary-blue, #2d4a77);
}

/* Typing indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 5px;
}
.typing-indicator span {
    width: 6px;
    height: 6px;
    background: #bbb;
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}
.typing-indicator span:nth-child(1) { animation-delay: 0s; }
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 1; }
}

@media (max-width: 480px) {
    .chatbot-window {
        width: 300px;
        height: 400px;
        right: -15px;
        bottom: 70px;
    }
}
</style>

<div class="chatbot-widget" id="chatbotWidget">
    <div class="chatbot-toggle" id="chatbotToggle" onclick="toggleChatbot()">
        <i class="fa-solid fa-robot"></i>
    </div>
    
    <div class="chatbot-window">
        <div class="chatbot-header">
            <h3><i class="fa-solid fa-robot"></i> Pustabots</h3>
            <span class="chatbot-close" onclick="toggleChatbot()"><i class="fa-solid fa-times"></i></span>
        </div>
        
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chat-bubble bot">
                Halo! Saya Pustabots 🤖.<br>Ada yang bisa saya bantu hari ini?
            </div>
        </div>
        
        <?php if (!empty($quick_replies)): ?>
        <div class="quick-replies" id="quickReplies">
            <?php foreach ($quick_replies as $qr): ?>
                <button class="quick-reply-btn" onclick="sendQuickReply(<?php echo $qr['id']; ?>, '<?php echo htmlspecialchars(addslashes($qr['question'])); ?>')">
                    <?php echo htmlspecialchars($qr['question']); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="chatbot-input">
            <input type="text" id="chatbotInput" placeholder="Ketik pesan Anda di sini..." onkeypress="handleKeyPress(event)">
            <button class="chatbot-send" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<script>
function toggleChatbot() {
    const widget = document.getElementById('chatbotWidget');
    widget.classList.toggle('active');
    
    if (widget.classList.contains('active')) {
        document.getElementById('chatbotInput').focus();
    }
}

function addMessage(text, sender) {
    const messages = document.getElementById('chatbotMessages');
    const bubble = document.createElement('div');
    bubble.className = `chat-bubble ${sender}`;
    bubble.innerHTML = text;
    messages.appendChild(bubble);
    messages.scrollTop = messages.scrollHeight;
}

function showTyping() {
    const messages = document.getElementById('chatbotMessages');
    const bubble = document.createElement('div');
    bubble.className = `chat-bubble bot typing-indicator-container`;
    bubble.id = 'typingIndicator';
    bubble.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
    messages.appendChild(bubble);
    messages.scrollTop = messages.scrollHeight;
}

function removeTyping() {
    const typing = document.getElementById('typingIndicator');
    if (typing) {
        typing.remove();
    }
}

function handleKeyPress(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
}

function sendMessage() {
    const input = document.getElementById('chatbotInput');
    const message = input.value.trim();
    
    if (message === '') return;
    
    // Hide quick replies when typing starts
    const qrContainer = document.getElementById('quickReplies');
    if (qrContainer) qrContainer.style.display = 'none';
    
    // Add user message
    addMessage(message, 'user');
    input.value = '';
    
    // Show typing
    showTyping();
    
    // Send to backend
    fetch('ajax_chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Pustabots response parse error:', text);
            data = { answer: 'Maaf, Pustabots sedang menyinkronkan data. Silakan coba kirim pesan Anda sekali lagi.' };
        }
        setTimeout(() => {
            removeTyping();
            addMessage(data.answer, 'bot');
        }, 400);
    })
    .catch(error => {
        console.error('Pustabots fetch error:', error);
        removeTyping();
        addMessage('Maaf, terjadi kesalahan jaringan. Silakan coba lagi nanti.', 'bot');
    });
}

function sendQuickReply(id, text) {
    // Hide quick replies
    const qrContainer = document.getElementById('quickReplies');
    if (qrContainer) qrContainer.style.display = 'none';
    
    // Add user message
    addMessage(text, 'user');
    
    // Show typing
    showTyping();
    
    // Send to backend
    fetch('ajax_chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_quick_reply: true, faq_id: id })
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Pustabots response parse error:', text);
            data = { answer: 'Maaf, Pustabots sedang menyinkronkan data. Silakan coba kirim pesan Anda sekali lagi.' };
        }
        setTimeout(() => {
            removeTyping();
            addMessage(data.answer, 'bot');
        }, 400);
    })
    .catch(error => {
        console.error('Pustabots fetch error:', error);
        removeTyping();
        addMessage('Maaf, terjadi kesalahan jaringan. Silakan coba lagi nanti.', 'bot');
    });
}
</script>
