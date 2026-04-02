/**
 * Al Amad School - Smart Chatbot Widget
 */

document.addEventListener('DOMContentLoaded', () => {
    const chatbotContainer = document.getElementById('chatbot-widget');
    if (!chatbotContainer) return;

    // Inject Premium HTML
    chatbotContainer.innerHTML = `
        <div class="chatbot-trigger" id="chatbot-trigger">
            <span class="trigger-icon">💬</span>
            <span class="trigger-badge">1</span>
        </div>
        <div class="chatbot-window" id="chatbot-window">
            <div class="chatbot-header">
                <div class="bot-avatar-header">✨</div>
                <div class="bot-info">
                    <strong>AI Amad Assistant</strong>
                    <span class="status-online">● Online</span>
                </div>
                <div style="display:flex; gap:8px;">
                    <button class="header-action-btn" id="clear-chat" title="Clear Chat"><i class="fas fa-trash-alt"></i></button>
                    <button class="close-bot" id="close-bot" title="Close">&times;</button>
                </div>
            </div>
            <div class="chatbot-messages" id="chatbot-messages">
                <!-- Messages go here -->
            </div>
            <div class="chatbot-suggestions" id="bot-suggestions">
                <button class="sug-btn">How to enroll? 🎒</button>
                <button class="sug-btn">School fees 💰</button>
                <button class="sug-btn">Schedule ⏰</button>
                <button class="sug-btn">Activities 🎨</button>
            </div>
            <div class="chatbot-input">
                <input type="text" id="bot-input-field" placeholder="Ask me anything..." autocomplete="off">
                <button id="send-bot-msg" title="Send">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </div>
    `;

    const trigger = document.getElementById('chatbot-trigger');
    const badge = document.querySelector('.trigger-badge');
    const windowEl = document.getElementById('chatbot-window');
    const closeBtn = document.getElementById('close-bot');
    const clearBtn = document.getElementById('clear-chat');
    const inputField = document.getElementById('bot-input-field');
    const sendBtn = document.getElementById('send-bot-msg');
    const messagesContainer = document.getElementById('chatbot-messages');
    
    let hasOpened = false;

    // Knowledge Base Data
    const knowledgeBase = [
        {
            keywords: ['hello', 'hi', 'hey', 'bonjour', 'salut', 'salam'],
            response: "Hello there! 👋 I'm your Al Amad Assistant. How can I brighten your day today?"
        },
        {
            keywords: ['enroll', 'register', 'inscription', 'admission', 'joining'],
            response: "To join the Al Amad family, you'll need:\n• Birth certificate 📜\n• 4 recent photos 📸\n• Medical certificate 🏥\n• Previous school reports 📚\nYou can start the process by clicking the **Enroll Now** button at the top of our homepage!"
        },
        {
            keywords: ['fee', 'price', 'cost', 'tuition', 'tarif', 'argent', 'pay'],
            response: "Our tuition fees are designed for accessibility:\n• **Primary:** 200 TND/month\n• **Middle:** 250 TND/month\n• **High School:** 300 TND/month\n*Discounts available for siblings!* 👨‍👩‍👧‍👦"
        },
        {
            keywords: ['schedule', 'hours', 'time', 'horaire', 'temps', 'open'],
            response: "⏰ **Operating Hours:**\n• Mon – Fri: 8:00 AM – 4:00 PM\n• Sat: 8:30 AM – 12:00 PM (Admin only)\nLunch break is from 12:00 PM to 1:30 PM. 🍽️"
        },
        {
            keywords: ['uniform', 'wear', 'clothes', 'tenue', 'habits'],
            response: "👕 **Uniform Policy:**\n• White shirt or polo with school logo\n• Navy blue trousers or skirt\n• Blue sports kit for PE days"
        },
        {
            keywords: ['club', 'activity', 'extracurricular', 'sport', 'music', 'chess'],
            response: "🌟 We offer vibrant clubs!\n• 🤖 Robotics & Coding\n• 🎨 Fine Arts\n• ♟️ Chess Champions\n• ⚽ Football & Basketball\n• 🎵 Music & Choir"
        },
        {
            keywords: ['location', 'where', 'address', 'map', 'place'],
            response: "📍 You can find us at **Rue de l'Éducation, Tunis 1000**. We are right next to the municipal park! 🌳"
        },
        {
            keywords: ['contact', 'phone', 'email', 'call', 'reach'],
            response: "📞 **Call us:** +216 71 123 456\n📧 **Email:** contact@alamad.edu.tn"
        },
        {
            keywords: ['thank', 'merci', 'shukran', 'thanks'],
            response: "You're very welcome! Always here to help the Al Amad community! 🌟"
        }
    ];

    // Open / Close / Clear
    trigger.addEventListener('click', () => {
        windowEl.classList.add('active');
        trigger.style.opacity = '0';
        trigger.style.pointerEvents = 'none';
        
        if (!hasOpened) {
            hasOpened = true;
            if(badge) badge.style.display = 'none';
            sendAutoGreeting();
        }
    });

    closeBtn.addEventListener('click', () => {
        windowEl.classList.remove('active');
        trigger.style.opacity = '1';
        trigger.style.pointerEvents = 'auto';
    });

    clearBtn.addEventListener('click', () => {
        messagesContainer.innerHTML = '';
        sendAutoGreeting();
    });

    const sendAutoGreeting = () => {
        showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator();
            addMessage("👋 Welcome! I'm the **AI Amad Assistant**. How can I help you explore our school today?", 'bot');
        }, 800);
    };

    // Formatting Markdown-like text
    const formatText = (text) => {
        return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
    };

    const addMessage = (text, type) => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${type} reveal-msg`;
        
        if (type === 'bot') {
            msgDiv.innerHTML = formatText(text);
        } else {
            msgDiv.textContent = text;
        }
        
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();
    };

    const scrollToBottom = () => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    const showTypingIndicator = () => {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot typing-indicator';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = '<span></span><span></span><span></span>';
        messagesContainer.appendChild(typingDiv);
        scrollToBottom();
    };

    const removeTypingIndicator = () => {
        const typingDiv = document.getElementById('typing-indicator');
        if (typingDiv) typingDiv.remove();
    };

    const handleBotResponse = (userMsg) => {
        const msg = userMsg.toLowerCase().trim();
        let bestMatch = null;

        // Simple but improved multi-keyword matching
        for (const item of knowledgeBase) {
            if (item.keywords.some(kw => msg.includes(kw))) {
                bestMatch = item;
                break;
            }
        }

        const response = bestMatch ? bestMatch.response : "I'm not quite sure about that yet! 🤖 Could you try rephrasing? Or you can reach our human team at **contact@alamad.edu.tn**.";

        showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator();
            addMessage(response, 'bot');
        }, 1000 + Math.random() * 1000);
    };

    const sendMessage = (textValue = null) => {
        const text = textValue !== null ? textValue : inputField.value.trim();
        if (text) {
            addMessage(text, 'user');
            if (textValue === null) inputField.value = '';
            handleBotResponse(text);
        }
    };

    sendBtn.addEventListener('click', () => sendMessage());
    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Delegate suggestion clicks (so they work multiple times)
    document.getElementById('bot-suggestions').addEventListener('click', (e) => {
        if (e.target.classList.contains('sug-btn')) {
            sendMessage(e.target.textContent);
        }
    });
});
