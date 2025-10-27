@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('/css/dashboard.css') }}">
<style>
    
/* Chat Container */
.chat-container {
    height: 700px;
    border: 1px solid #ddd;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
}

/* Chat Box */
#chatBox {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
}

/* Chat Rows */
.chat-row {
    display: flex;
    margin-bottom: 15px;
}

.chat-row.user {
    justify-content: flex-end;
}

.chat-row.bot {
    justify-content: flex-start;
}

/* Chat Bubbles */
.chat-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 18px;
    position: relative;
    word-wrap: break-word;
}

.chat-row.user .chat-bubble {
    background: #007bff;
    color: white;
    border-bottom-right-radius: 5px;
}

.chat-row.bot .chat-bubble {
    background: white;
    color: #333;
    border: 1px solid #ddd;
    border-bottom-left-radius: 5px;
}

.chat-bubble.error {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

/* Suggestion Box */
.suggestion-box {
    margin-top: 10px;
}

.suggestion {
    display: inline-block;
    background: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 8px 16px;
    margin: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.suggestion:hover {
    background: #007bff;
    color: white;
    transform: translateY(-2px);
}

/* Input Area */
.chat-input-container {
    border-top: 1px solid #ddd;
    padding: 15px;
    background: white;
}

.chat-input {
    display: flex;
    gap: 10px;
}

.chat-input input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 25px;
    padding: 12px 20px;
    outline: none;
}

.chat-input input:focus {
    border-color: #007bff;
}

.chat-input button {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 25px;
    padding: 12px 25px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.chat-input button:hover {
    background: #0056b3;
}

/* Star Rating */
.star-rating {
    display: flex;
    gap: 5px;
}

.star {
    font-size: 24px;
    cursor: pointer;
    color: #ccc;
    transition: color 0.2s ease;
}

.star:hover,
.star.active {
    color: #ffc107;
}

    .sidebar {
        width: 220px;
        background: #0c5726;
        color: white;
        height: 100vh;
        float: left;
        padding: 20px;
    }
    .sidebar h1 { font-size: 22px; margin-bottom: 20px; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar ul li { margin-bottom: 15px; }
    .sidebar ul li a { color: white; text-decoration: none; font-weight: bold; }
    .sidebar ul li.active a { text-decoration: underline; }

    .main { margin-left: 240px; padding: 20px; }
</style>
@endsection

@section('content')
<!-- Sidebar -->
<div class="sidebar">
    <h1>AIHRA</h1>
    <ul>
        <li class="active"><a href="#home" onclick="showSection('home')">Home</a></li>
        <li><a href="#chat" onclick="showSection('chat')">Chat</a></li>
        <li><a href="#feedback" onclick="showSection('feedback')">Feedback</a></li>
        <li><a href="#account" onclick="showSection('account')">Account</a></li>
    </ul>
    <div class="account">
        <img src="{{ asset('uploads/' . Auth::user()->profile_picture) }}"
             alt="Employee" style="width:80px;height:80px;border-radius:50%;object-fit:cover;background:#fff;">
        <a href="{{ route('logout') }}" class="logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</div>

<!-- Main -->
<div class="main">
    <!-- Home Section -->
    <div id="home" class="table-container section">
        <h2>Announcements</h2>
        @forelse($announcements as $announcement)
            <div class='card' style='margin-bottom:15px; padding:10px; border:1px solid #eee; border-radius:6px;'>
                <h3>{{ $announcement->title }}</h3>
            </div>
        @empty
            <p>No announcements yet.</p>
        @endforelse
    </div>

    <!-- Chat Section -->
    <!-- Chat Section -->
<div id="chat" class="table-container section" style="display:none;">
    <h2>Chatbot</h2>
    <div class="chat-container">
        <div id="chatBox" class="chat-box">
            <!-- Initial message - questions will be loaded from database via JavaScript -->
            <div class="chat-row bot">
                <div class="chat-bubble">
                    👋 Hi! I'm Aihra - Your AI Human Resource Assistant. How can I help you?
                </div>
            </div>
        </div>
        
        <div class="chat-input-container">
            <div class="chat-input">
                <input type="text" id="userMessage" placeholder="Type your message..." />
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>
</div>

        <!-- Feedback Section -->
    <div id="feedback" class="table-container section" style="display:none;">
        <h2>Give Feedback</h2>
        <form method="POST" action="{{ route('feedback.store') }}" class="feedback-form">
            @csrf

            <label for="ratingValue">Rating:</label>
            <div class="star-rating">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star" data-value="{{ $i }}">★</span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="ratingValue" required>

            <label for="suggestion">Suggestion:</label>
            <textarea name="suggestion" id="suggestion" placeholder="Write your feedback here..." required></textarea>

            <button type="submit" class="btn review">✅ Submit Feedback</button>
        </form>
    </div>

    <!-- Account Section -->
    <div id="account" class="table-container section" style="display:none;">
        <h2>Account</h2>
        <div>
            <img src="{{ asset('uploads/' . Auth::user()->profile_picture) }}" alt="Profile Picture" style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
            <a href="{{ route('profile.edit') }}">✏️ Edit Profile</a>
        </div>
        <p><strong>About Me:</strong></p>
        <p>{{ Auth::user()->about }}</p>
    </div>
</div>

<script>
let currentLevel = 0;
let conversationPath = [];

function showSection(id) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(id).style.display = 'block';
    
    // Auto-start guided flow when chat section is opened
    if (id === 'chat' && conversationPath.length === 0) {
        setTimeout(startGuidedFlow, 300);
    }
}

function scrollChat() {
    const chatBox = document.getElementById('chatBox');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

// 🧠 SEND MESSAGE
async function sendMessage() {
    const msgInput = document.getElementById('userMessage');
    const msg = msgInput.value.trim();
    if (!msg) return;

    const chatBox = document.getElementById('chatBox');
    
    // Add user message
    chatBox.innerHTML += `<div class="chat-row user"><div class="chat-bubble">${msg}</div></div>`;
    scrollChat();

    msgInput.value = '';
    conversationPath.push({ type: 'user', message: msg });

    try {
        const res = await fetch('{{ url("dialogflow-webhook") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                queryResult: { queryText: msg }, 
                employeeNum: '{{ Auth::user()->employeeNum }}' 
            })
        });

        // Check if response is JSON
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await res.text();
            console.error('Server returned HTML:', text.substring(0, 200));
            throw new Error('Server error. Please try again.');
        }

        const data = await res.json();

        if (data.status === 'success') {
            // Direct answer from Dialogflow
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble">${data.fulfillmentText}</div>
                </div>`;
            conversationPath.push({ type: 'bot', message: data.fulfillmentText });
        }
        else if (data.status === 'guided_flow') {
            // Start guided questioning from database
            await loadGuidedQuestions();
        }

        scrollChat();

    } catch (err) {
        console.error('Chat error:', err);
        const chatBox = document.getElementById('chatBox');
        chatBox.innerHTML += `<div class="chat-row bot">
            <div class="chat-bubble error">Sorry, I'm having trouble connecting. Please try the guided questions below.</div>
        </div>`;
        // Fallback to guided questions
        await loadGuidedQuestions();
        scrollChat();
    }
}

// 💬 LOAD GUIDED QUESTIONS FROM DATABASE
// 💬 LOAD GUIDED QUESTIONS FROM DATABASE
async function loadGuidedQuestions(parentId = null) {
    const chatBox = document.getElementById('chatBox');
    
    try {
        let url;
        if (parentId) {
            url = `{{ url('guided') }}/${parentId}`;
        } else {
            url = `{{ url('guided') }}`;
        }

        console.log('Loading questions from:', url);

        const res = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!res.ok) {
            const errorText = await res.text();
            console.error('HTTP Error:', res.status, errorText);
            
            // If it's a 404 or 500 error, show specific message
            if (res.status === 404) {
                throw new Error('No questions found in database');
            } else if (res.status === 500) {
                throw new Error('Server error - database issue');
            } else {
                throw new Error(`Server error: ${res.status}`);
            }
        }

        const data = await res.json();
        console.log('Questions loaded:', data);

        // Check if API returned an error
        if (data.type === 'error') {
            throw new Error(data.message || 'API returned error');
        }

        // Handle final answer
        if (data.type === 'final') {
            const answer = data.data?.[0]?.answer || data.answer || "Thank you for your question!";
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble">${answer}</div>
                </div>`;
            conversationPath.push({ type: 'bot', message: answer });
            scrollChat();
            return;
        }

        // Handle escalation to HR
        if (data.type === 'escalate') {
            const answer = data.data?.[0]?.answer || "I've forwarded your question to HR.";
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble">${answer}</div>
                </div>`;
            conversationPath.push({ type: 'bot', message: answer });
            scrollChat();
            return;
        }

        // Show question buttons from database
        const message = data.message || 'Please choose a topic:';
        
        if (!data.data || !Array.isArray(data.data) || data.data.length === 0) {
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble">No questions available at the moment. Please try again later.</div>
                </div>`;
            scrollChat();
            return;
        }

        const options = data.data.map(q => `
            <div class="suggestion" onclick="handleQuestionClick(${q.gq_id}, '${escapeHtml(q.question_text)}')">
                ${q.question_text}
            </div>`).join('');

        chatBox.innerHTML += `
            <div class="chat-row bot">
                <div class="chat-bubble">
                    <strong>${message}</strong>
                    <div class="suggestion-box">${options}</div>
                </div>
            </div>`;
        
        conversationPath.push({ 
            type: 'bot', 
            message: message,
            options: data.data.map(q => q.question_text)
        });
        
        scrollChat();
        
    } catch (error) {
        console.error('Error loading guided questions:', error);
        
        let errorMessage = 'Sorry, I cannot load the questions right now. ';
        
        if (error.message.includes('database') || error.message.includes('No questions')) {
            errorMessage += 'The question database is currently unavailable. ';
        }
        
        // Show fallback options if initial load fails
        if (!parentId) {
            errorMessage += 'Here are some common topics:';
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble error">${errorMessage}</div>
                </div>
                <div class="chat-row bot">
                    <div class="chat-bubble">
                        <div class="suggestion-box">
                            <div class="suggestion" onclick="sendQuick('Employment questions')">Employment</div>
                            <div class="suggestion" onclick="sendQuick('Benefits information')">Benefits</div>
                            <div class="suggestion" onclick="sendQuick('Promotion requirements')">Promotion</div>
                            <div class="suggestion" onclick="sendQuick('Training and development')">Development</div>
                        </div>
                    </div>
                </div>`;
        } else {
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble error">${errorMessage} Please try selecting a different topic.</div>
                </div>`;
        }
        
        scrollChat();
    }
}

// 🛠️ DEBUG FUNCTION - Test the database connection
async function debugDatabase() {
    try {
        console.log('Testing guided questions database...');
        
        const response = await fetch('{{ url("guided-debug") }}');
        const data = await response.json();
        
        console.log('Database debug info:', data);
        
        if (data.table_exists) {
            console.log('✅ guidedquery table exists');
            console.log('Total questions:', data.total_questions);
            console.log('Level 1 questions:', data.level1_questions);
            console.log('Level 1 data:', data.level1_data);
        } else {
            console.log('❌ guidedquery table does not exist');
        }
        
    } catch (error) {
        console.error('Debug error:', error);
    }
}

// Run this in browser console to test
// debugDatabase();

// 🧩 HANDLE QUESTION CLICK - Load next level from database
// 🧩 HANDLE QUESTION CLICK - Load next level from database
async function handleQuestionClick(id, text) {
    const chatBox = document.getElementById('chatBox');
    
    // Add user's choice
    chatBox.innerHTML += `<div class="chat-row user"><div class="chat-bubble">${text}</div></div>`;
    conversationPath.push({ type: 'user', message: text });
    
    scrollChat();

    try {
        // Load next level questions from database
        await loadGuidedQuestions(id);
    } catch (error) {
        console.error('Error handling question click:', error);
        // If there's an error, try sending the question directly to Dialogflow
        await sendQuestionToDialogflow(text);
    }
}

// 🆕 SEND QUESTION DIRECTLY TO DIALOGFLOW (fallback)
async function sendQuestionToDialogflow(questionText) {
    const chatBox = document.getElementById('chatBox');
    
    try {
        const res = await fetch('{{ url("dialogflow-webhook") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                queryResult: { queryText: questionText }, 
                employeeNum: '{{ Auth::user()->employeeNum }}' 
            })
        });

        const data = await res.json();

        if (data.status === 'success') {
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble">${data.fulfillmentText}</div>
                </div>`;
        } else {
            // Fallback message
            chatBox.innerHTML += `
                <div class="chat-row bot">
                    <div class="chat-bubble">I understand you're asking about "${questionText}". Let me help you find the right information.</div>
                </div>`;
        }
        
        scrollChat();
    } catch (error) {
        console.error('Error sending to Dialogflow:', error);
        chatBox.innerHTML += `
            <div class="chat-row bot">
                <div class="chat-bubble">I understand you're asking about "${questionText}". Please try rephrasing your question or contact HR for assistance.</div>
            </div>`;
        scrollChat();
    }
}

// 🛡️ HTML ESCAPE FUNCTION
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ✴️ QUICK MESSAGE SEND
function sendQuick(message) {
    document.getElementById('userMessage').value = message;
    sendMessage();
}

// ⌨️ ENTER KEY SUPPORT
document.getElementById('userMessage')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// 🆕 START GUIDED FLOW - Load initial questions from database
function startGuidedFlow() {
    const chatBox = document.getElementById('chatBox');
    if (!chatBox) return;
    
    // Only clear if not already started
    if (conversationPath.length === 0) {
        chatBox.innerHTML = `
            <div class="chat-row bot">
                <div class="chat-bubble">
                    👋 Hi! I'm Aihra - Your AI Human Resource Assistant. How can I help you?
                </div>
            </div>`;
    }
    
    conversationPath = [];
    loadGuidedQuestions(); // This will load level 1 questions from database
}

// 🔄 RESTART CHAT
function restartChat() {
    const chatBox = document.getElementById('chatBox');
    if (chatBox) {
        chatBox.innerHTML = '';
        conversationPath = [];
        startGuidedFlow();
    }
}

// ⭐ Feedback System
document.addEventListener('DOMContentLoaded', function() {
    // Initialize star rating
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('ratingValue');
    let selectedRating = 0;

    stars.forEach((star, index) => {
        const value = index + 1;

        star.addEventListener('mouseover', function() {
            stars.forEach((s, i) => s.style.color = i < value ? '#ffc107' : '#ccc');
        });

        star.addEventListener('mouseout', function() {
            stars.forEach((s, i) => s.style.color = i < selectedRating ? '#ffc107' : '#ccc');
        });

        star.addEventListener('click', function() {
            selectedRating = value;
            if (ratingInput) ratingInput.value = value;
            stars.forEach((s, i) => s.style.color = i < value ? '#ffc107' : '#ccc');
        });
    });

    // Add restart button to chat
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer && !document.getElementById('restartChatBtn')) {
        const restartBtn = document.createElement('button');
        restartBtn.id = 'restartChatBtn';
        restartBtn.innerHTML = '🔄 Restart Chat';
        restartBtn.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: #6c757d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
        `;
        restartBtn.addEventListener('click', restartChat);
        chatContainer.style.position = 'relative';
        chatContainer.appendChild(restartBtn);
    }

    // Auto-start guided flow if chat section is active on page load
    const chatSection = document.getElementById('chat');
    if (chatSection && chatSection.style.display !== 'none') {
        setTimeout(startGuidedFlow, 500);
    }
});

// Debug function to test API
async function testGuidedApi() {
    try {
        const response = await fetch('{{ url("guided") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.text();
        console.log('API Response:', data.substring(0, 500));
    } catch (error) {
        console.error('API Test Error:', error);
    }
}
</script>

@endsection