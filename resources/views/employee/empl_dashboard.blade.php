@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('/css/dashboard.css') }}">
<style>
    /* Layout */
    .sidebar {
        width: 220px;
        background: #0c5726;
        color: white;
        height: 100vh;
        float: left;
        padding: 20px;
        position: fixed;
    }
    .sidebar h1 { 
        font-size: 22px; 
        margin-bottom: 20px; 
        text-align: center;
    }
    .sidebar ul { 
        list-style: none; 
        padding: 0; 
    }
    .sidebar ul li { 
        margin-bottom: 15px; 
        padding: 10px;
        border-radius: 5px;
        transition: background 0.3s ease;
    }
    .sidebar ul li:hover { 
        background: rgba(255,255,255,0.1);
    }
    .sidebar ul li.active { 
        background: rgba(255,255,255,0.2);
    }
    .sidebar ul li a { 
        color: white; 
        text-decoration: none; 
        font-weight: bold; 
        display: block;
    }
    
    .main { 
        margin-left: 240px; 
        padding: 20px;
        min-height: 100vh;
        background: #f8f9fa;
    }

    /* Chat Container */
    .chat-container {
        height: 700px;
        border: 1px solid #ddd;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* Chat Box */
    #chatBox, #ticketChatBox {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8f9fa;
    }

    /* Chat Rows */
    .chat-row {
        display: flex;
        margin-bottom: 15px;
        width: 100%;
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
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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

    /* Special Message Types */
    .chat-bubble.hr-reply {
        background: #e8f5e8 !important;
        border: 1px solid #c8e6c9 !important;
        color: #155724 !important;
    }

    .chat-bubble.employee-followup {
        background: #e3f2fd !important;
        border: 1px solid #bbdefb !important;
        color: #1565c0 !important;
    }

    .chat-bubble.error {
        background: #f8d7da !important;
        color: #721c24 !important;
        border-color: #f5c6cb !important;
    }

    .chat-bubble.success {
        background: #d4edda !important;
        color: #155724 !important;
        border-color: #c3e6cb !important;
    }

    .chat-bubble.info {
        background: #fff3cd !important;
        color: #856404 !important;
        border-color: #ffeaa7 !important;
    }

    /* Message Time */
    .message-time {
        font-size: 11px;
        color: #666;
        margin-top: 5px;
        opacity: 0.8;
        font-style: italic;
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
        border-radius: 0 0 10px 10px;
    }

    .chat-input {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .chat-input input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 25px;
        padding: 12px 20px;
        outline: none;
        font-size: 14px;
    }

    .chat-input input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .chat-input button {
        background: #007bff;
        color: white;
        border: none;
        border-radius: 25px;
        padding: 12px 25px;
        cursor: pointer;
        transition: background 0.3s ease;
        font-weight: 500;
    }

    .chat-input button:hover {
        background: #0056b3;
        transform: translateY(-1px);
    }

    /* Chat Tabs */
    .chat-tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
        margin-bottom: 20px;
        background: white;
        border-radius: 10px 10px 0 0;
        padding: 0 10px;
    }

    .tab-button {
        background: none;
        border: none;
        padding: 12px 24px;
        cursor: pointer;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #666;
    }

    .tab-button.active {
        background: #007bff;
        color: white;
    }

    .tab-button:hover:not(.active) {
        background: #e9ecef;
        color: #333;
    }

    .chat-tab {
        display: none;
    }

    .chat-tab.active {
        display: block;
    }

    /* Ticket History Styles */
    .history-container {
        height: 600px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .ticket-list {
        height: 200px;
        overflow-y: auto;
        border-bottom: 1px solid #ddd;
        padding: 15px;
        background: white;
    }

    .ticket-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    #selectedTicketInfo {
        padding: 15px;
        background: white;
        border-bottom: 1px solid #ddd;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Ticket Items */
    .ticket-item-history {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .ticket-item-history:hover {
        background: #e3f2fd;
        border-color: #007bff;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }

    .ticket-item-history.active {
        background: #e3f2fd;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .ticket-meta-history {
        font-size: 12px;
        color: #666;
        margin-top: 8px;
    }

    /* Badges */
    .ticket-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
        margin-right: 8px;
    }

    .ticket-badge.open { background: #fff3cd; color: #856404; }
    .ticket-badge.replied { background: #d4edda; color: #155724; }
    .ticket-badge.resolved { background: #e2e3e5; color: #383d41; }
    .ticket-badge.pending { background: #cce7ff; color: #004085; }
    .ticket-badge.escalated { background: #f8d7da; color: #721c24; }

    /* Sections */
    .section {
        display: none;
    }

    .table-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* Account Section */
    .account {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.2);
    }

    .account img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #fff;
        margin-bottom: 10px;
        border: 3px solid rgba(255,255,255,0.3);
    }

    .logout {
        color: white;
        text-decoration: none;
        display: block;
        padding: 8px;
        border-radius: 5px;
        background: rgba(255,255,255,0.1);
        transition: background 0.3s ease;
    }

    .logout:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Feedback Form */
    .feedback-form {
        max-width: 600px;
    }

    .feedback-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
    }

    .feedback-form textarea {
        width: 100%;
        height: 120px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
        resize: vertical;
    }

    .feedback-form textarea:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .star-rating {
        display: flex;
        gap: 5px;
        margin-bottom: 20px;
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

    .btn.review {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.3s ease;
    }

    .btn.review:hover {
        background: #218838;
    }

    /* Restart Button */
    #restartChatBtn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #6c757d;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 15px;
        cursor: pointer;
        font-size: 12px;
        z-index: 1000;
        transition: background 0.3s ease;
    }

    /* New Conversation Button */
    #newConversationBtn {
        position: absolute;
        top: 10px;
        right: 140px; /* move further left to avoid overlap with restart */
        background: #6c757d;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 12px;
        z-index: 1000;
        transition: background 0.3s ease;
    }

    #newConversationBtn:hover { background: #5a6268; }

    #restartChatBtn:hover {
        background: #5a6268;
    }

    /* Close Ticket Button */
    .close-ticket-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .close-ticket-btn:hover {
        background: #5a6268;
    }

    /* Loading States */
    .loading {
        text-align: center;
        color: #666;
        padding: 20px;
    }

    .loading::after {
        content: '...';
        animation: dots 1.5s steps(4, end) infinite;
    }

    @keyframes dots {
        0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
        40% { color: #666; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
        60% { text-shadow: .25em 0 0 #666, .5em 0 0 rgba(0,0,0,0); }
        80%, 100% { text-shadow: .25em 0 0 #666, .5em 0 0 #666; }
    }
</style>
@endsection

@section('content')
<!-- Sidebar -->
<div class="sidebar">
    <h1>AIHRA</h1>
    <ul>
        <li class="active"><a href="#home" onclick="showSection('home')">🏠 Home</a></li>
        <li><a href="#chat" onclick="showSection('chat')">💬 Chat</a></li>
        <li><a href="#feedback" onclick="showSection('feedback')">⭐ Feedback</a></li>
        <li><a href="#account" onclick="showSection('account')">👤 Account</a></li>
    </ul>
    <div class="account">
        <img src="{{ asset('uploads/' . Auth::user()->profile_picture) }}" alt="Profile Picture">
        <a href="{{ route('logout') }}" class="logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           🚪 Log Out
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="main">
    <!-- Home Section -->
    <div id="home" class="table-container section">
        <h2>📢 Announcements</h2>
        @forelse($announcements as $announcement)
            <div class='card' style='margin-bottom:15px; padding:15px; border:1px solid #eee; border-radius:6px; background:#f8f9fa;'>
                <h3 style='margin:0 0 10px 0; color:#333;'>{{ $announcement->title }}</h3>
                <p style='margin:0; color:#666;'>{{ $announcement->description }}</p>
            </div>
        @empty
            <p style='text-align:center; color:#666;'>No announcements yet.</p>
        @endforelse
    </div>

    <!-- Chat Section -->
    <div id="chat" class="table-container section" style="display:none;">
        <h2>🤖 AI HR Assistant</h2>
        
        <!-- Chat Tabs -->
        <div class="chat-tabs">
            <button class="tab-button active" onclick="showChatTab('new-chat')">💬 New Chat</button>
            <button class="tab-button" onclick="showChatTab('chat-history')">� Chat History</button>
            <button class="tab-button" onclick="showChatTab('support-tickets')">�📋 Support Tickets</button>
        </div>

        <!-- New Chat Tab -->
        <div id="new-chat" class="chat-tab active">
            <div class="chat-container">
                <div id="chatBox" class="chat-box">
                    <div class="chat-row bot">
                        <div class="chat-bubble">
                            👋 Hello! I'm Aihra - Your AI Human Resource Assistant. 
                            <div class="message-time">How can I help you today?</div>
                        </div>
                    </div>
                </div>
                
                <div class="chat-input-container">
                    <div class="chat-input">
                        <input type="text" id="userMessage" placeholder="Type your message here..." />
                        <button onclick="sendMessage()">📤 Send</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chat History Tab -->
        <div id="chat-history" class="chat-tab">
            <div class="history-container">
                <div class="ticket-list">
                    <h4 style="margin: 0 0 10px 0;">💬 Chat Conversations</h4>
                    <div id="conversationList">
                        <p class="loading">Loading your conversations</p>
                    </div>
                </div>

                <div class="ticket-chat">
                    <div id="selectedTicketInfo">
                        <p style="text-align: center; color: #666; margin: 0;">Select a conversation to view messages</p>
                    </div>
                    
                    <div id="ticketChatBox" class="chat-box">
                        <!-- Conversation messages will appear here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Tickets Tab -->
        <div id="support-tickets" class="chat-tab">
            <div class="history-container">
                <div class="ticket-list">
                    <h4 style="margin: 0 0 15px 0;">🎫 Your Support Tickets</h4>
                    <div id="ticketList">
                        <p class="loading">Loading your tickets</p>
                    </div>
                </div>

                <div class="ticket-chat">
                    <div id="selectedTicketInfo">
                        <p style="text-align: center; color: #666; margin: 0;">Select a ticket to view conversation</p>
                    </div>
                    
                    <div id="ticketChatBox" class="chat-box">
                        <!-- Ticket messages will appear here -->
                    </div>
                    
                    <!-- Reply Section -->
                    <div id="ticketReplySection" style="display: none; padding: 15px; background: white; border-top: 1px solid #ddd;">
                        <div class="chat-input">
                            <input type="text" id="ticketReplyMessage" placeholder="Type your reply to HR..." />
                            <button onclick="sendTicketReply()">📤 Send Reply</button>
                        </div>
                        <p style="font-size: 12px; color: #666; margin-top: 8px; text-align: center;">
                            💡 Your reply will be sent to HR and added to this support ticket.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Section -->
    <div id="feedback" class="table-container section" style="display:none;">
        <h2>⭐ Give Feedback</h2>
        <form method="POST" action="{{ route('feedback.store') }}" class="feedback-form">
            @csrf

            <label for="ratingValue">Rating:</label>
            <div class="star-rating">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star" data-value="{{ $i }}">★</span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="ratingValue" required>

            <label for="suggestion">Your Feedback:</label>
            <textarea name="suggestion" id="suggestion" placeholder="Please share your thoughts, suggestions, or any issues you've encountered..." required></textarea>

            <button type="submit" class="btn review">✅ Submit Feedback</button>
        </form>
    </div>

    <!-- Account Section -->
    <div id="account" class="table-container section" style="display:none;">
        <h2>👤 Account Information</h2>
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('uploads/' . Auth::user()->profile_picture) }}" alt="Profile Picture" 
                 style="width:120px;height:120px;border-radius:50%;object-fit:cover; border: 3px solid #007bff;">
            <br>
            <a href="{{ route('profile.edit') }}" style="display: inline-block; margin-top: 10px; color: #007bff; text-decoration: none;">
                ✏️ Edit Profile
            </a>
        </div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
            <p><strong>About Me:</strong></p>
            <p style="color: #666; line-height: 1.6;">{{ Auth::user()->about ?: 'No information provided yet.' }}</p>
        </div>
    </div>
</div>

<script>
// Global variables
let currentLevel = 0;
let conversationPath = [];
let currentSelectedTicket = null;
let currentSessionId = '{{ session()->getId() }}';
let currentConversationId = null;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeStarRating();
    initializeChat();
    requestNotificationPermission();
});

// Section Navigation
function showSection(id) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(id).style.display = 'block';
    
    // Update active nav item
    document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
    event.target.closest('li').classList.add('active');
    
    // Initialize chat if chat section is opened
    if (id === 'chat') {
        setTimeout(() => {
            if (conversationPath.length === 0) {
                startGuidedFlow();
            }
            startReplyChecker();
        }, 300);
    }
}

// Chat Tab Management
function showChatTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.chat-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab and activate button
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
    
    // Load data when switching to history tab
    if (tabName === 'chat-history') {
        // Load both conversations and tickets
        loadConversations();
        loadEmployeeTickets();
    }
}

// Initialize Star Rating
function initializeStarRating() {
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
}

// Initialize Chat
function initializeChat() {
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer && !document.getElementById('restartChatBtn')) {
        const restartBtn = document.createElement('button');
        restartBtn.id = 'restartChatBtn';
        restartBtn.innerHTML = '🔄 Restart Chat';
        restartBtn.onclick = restartChat;
        chatContainer.style.position = 'relative';
        chatContainer.appendChild(restartBtn);

        // New conversation button next to restart
        if (!document.getElementById('newConversationBtn')) {
            const newBtn = document.createElement('button');
            newBtn.id = 'newConversationBtn';
            newBtn.innerHTML = '➕ New';
            newBtn.onclick = async function() { await startNewConversation(); };
            chatContainer.appendChild(newBtn);
        }
    }

    // Enter key support
    document.getElementById('userMessage')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    document.getElementById('ticketReplyMessage')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendTicketReply();
    });
}

// Scroll chat to bottom
function scrollChat(chatBoxId = 'chatBox') {
    const chatBox = document.getElementById(chatBoxId);
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

// Send Message to AI
// 🎯 FIXED: Send Message to Dialogflow with proper response handling
async function sendMessage() {
    const msgInput = document.getElementById('userMessage');
    const msg = msgInput.value.trim();
    if (!msg) return;

    const chatBox = document.getElementById('chatBox');
    
    // Add user message
    addMessageToChat(chatBox, 'user', msg);
    msgInput.value = '';
    conversationPath.push({ type: 'user', message: msg });

    try {
        console.log('Sending to Dialogflow:', msg);
        
        const res = await fetch('{{ url("dialogflow-webhook") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
                body: JSON.stringify({ 
                message: msg, // 🆕 FIXED: Use correct parameter name
                sessionId: currentSessionId
            })
        });

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        const data = await res.json();
        console.log('🔍 Dialogflow RAW Response:', data);

        // 🆕 FIXED: Handle different response formats
        if (data.fulfillmentText) {
            // Standard Dialogflow response
            addMessageToChat(chatBox, 'bot', data.fulfillmentText);
            conversationPath.push({ type: 'bot', message: data.fulfillmentText });
        } 
        else if (data.response) {
            // Custom backend response format
            handleCustomResponse(data.response, chatBox);
        }
        else if (data.message) {
            // Alternative response format
            addMessageToChat(chatBox, 'bot', data.message);
            conversationPath.push({ type: 'bot', message: data.message });
        }
        else {
            // Fallback to guided questions
            console.warn('No valid response from Dialogflow, falling back to guided questions');
            await loadGuidedQuestions();
        }

    } catch (err) {
        console.error('❌ Chat error:', err);
        addMessageToChat(chatBox, 'bot', 
            "I'm having trouble connecting right now. Please try the guided questions below or contact HR directly.", 
            'error'
        );
        await loadGuidedQuestions();
    }
}

// 🆕 NEW: Handle custom backend responses
function handleCustomResponse(response, chatBox) {
    console.log('🔄 Handling custom response:', response);
    
    const { message, ticket_no, escalated, needs_hr, message_type } = response;
    
    if (escalated && ticket_no) {
        // 🎯 FIXED: Handle ticket escalation properly
        handleTicketCreation(ticket_no, message || "Your query has been escalated to HR.");
    } 
    else if (needs_hr) {
        // Suggest escalation
        addMessageToChat(chatBox, 'bot', message);
        addMessageToChat(chatBox, 'bot', 
            "Would you like me to escalate this to HR for further assistance?",
            'info'
        );
        
        // Add escalation buttons
        const escalationButtons = `
            <div class="suggestion-box">
                <div class="suggestion" onclick="escalateToHR('${message}')">✅ Yes, escalate to HR</div>
                <div class="suggestion" onclick="continueChat()">❌ No, continue chatting</div>
            </div>
        `;
        
        chatBox.innerHTML += `
            <div class="chat-row bot">
                <div class="chat-bubble">${escalationButtons}</div>
            </div>
        `;
    }
    else if (message_type === 'hr_reply') {
        // HR response in existing ticket
        addMessageToChat(chatBox, 'bot', message, 'hr-reply');
    }
    else {
        // Regular AI response
        addMessageToChat(chatBox, 'bot', message);
    }
    
    conversationPath.push({ type: 'bot', message: message });
    scrollChat();
}

// 🆕 NEW: Handle ticket creation and escalation
async function handleTicketCreation(ticketNo, message) {
    const chatBox = document.getElementById('chatBox');
    
    // Show success message
    addMessageToChat(chatBox, 'bot', 
        `✅ ${message}\n\n🎫 **Ticket Number:** ${ticketNo}`,
        'success'
    );
    
    // Add ticket management options
    const ticketActions = `
        <div class="suggestion-box">
            <div class="suggestion" onclick="viewTicket('${ticketNo}')">📋 View Ticket</div>
            <div class="suggestion" onclick="addMoreInfo('${ticketNo}')">💬 Add More Info</div>
            <div class="suggestion" onclick="continueChat()">💬 Ask Another Question</div>
        </div>
    `;
    
    chatBox.innerHTML += `
        <div class="chat-row bot">
            <div class="chat-bubble info">
                <strong>What would you like to do next?</strong>
                ${ticketActions}
            </div>
        </div>
    `;
    
    conversationPath.push({ 
        type: 'bot', 
        message: `Ticket ${ticketNo} created successfully` 
    });
    
    // Switch to tickets tab after a delay
    setTimeout(() => {
        showChatTab('chat-history');
        loadEmployeeTickets();
    }, 3000);
    
    scrollChat();
}

// 🆕 NEW: Manual escalation function
async function escalateToHR(originalMessage) {
    const chatBox = document.getElementById('chatBox');
    
    try {
        console.log('Escalating to HR:', originalMessage);
        
        const res = await fetch('{{ url("escalate-to-hr") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                message: originalMessage,
                sessionId: currentSessionId
            })
        });

        const data = await res.json();
        console.log('Escalation response:', data);

        if (data.success && data.ticket_no) {
            handleTicketCreation(data.ticket_no, data.message || "Your query has been escalated to HR.");
        } else {
            throw new Error(data.message || 'Escalation failed');
        }

    } catch (err) {
        console.error('Escalation error:', err);
        addMessageToChat(chatBox, 'bot', 
            "Sorry, I couldn't escalate your query right now. Please try again later or contact HR directly.",
            'error'
        );
    }
}

// 🆕 NEW: View specific ticket
function viewTicket(ticketNo) {
    showChatTab('chat-history');
    setTimeout(() => {
        loadTicketConversation(ticketNo);
    }, 500);
}

// 🆕 NEW: Add more information to existing ticket
async function addMoreInfo(ticketNo) {
    showChatTab('chat-history');
    
    setTimeout(async () => {
        await loadTicketConversation(ticketNo);
        
        // Focus on reply input
        const replyInput = document.getElementById('ticketReplyMessage');
        if (replyInput) {
            replyInput.focus();
            replyInput.placeholder = "Add additional information for HR...";
        }
    }, 500);
}

// 🆕 NEW: Continue chatting
function continueChat() {
    showChatTab('new-chat');
    addMessageToChat(
        document.getElementById('chatBox'), 
        'bot', 
        "What else can I help you with?",
        'info'
    );
}

// 🆕 IMPROVED: Load messages with better NLP detection
async function loadTicketMessages(ticketNo) {
    try {
        console.log('Loading messages for ticket:', ticketNo);
        const response = await fetch(`{{ url('employee/messages') }}/${ticketNo}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const messages = await response.json();
        console.log('Loaded messages:', messages);
        
        const chatBox = document.getElementById('ticketChatBox');
        chatBox.innerHTML = '';
        
        if (messages.length === 0) {
            chatBox.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages found for this ticket</p>';
            return;
        }
        
        // 🎯 Render ticket messages: include employee and HR messages (skip guided/button-only entries)
        messages.forEach(msg => {
            if (!msg || msg.is_button) return; // skip guided/button-only records

            const isEmployee = msg.sender === 'employee';
            const isHR = msg.sender === 'hr';

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-row ${isEmployee ? 'user' : 'bot'}`;

            let bubbleClass = 'chat-bubble';
            if (isHR) bubbleClass += ' hr-reply';
            if (isEmployee && (msg.message && (msg.message.includes('Employee Follow-up') || msg.is_followup))) {
                bubbleClass += ' employee-followup';
            }

            const displayMessage = msg.message || '';

            messageDiv.innerHTML = `
                <div class="${bubbleClass}">
                    ${displayMessage}
                    <div class="message-time">
                        ${new Date(msg.created_at).toLocaleString()}${isEmployee ? ' (You)' : (isHR ? ' (HR)' : '')}
                    </div>
                </div>
            `;

            chatBox.appendChild(messageDiv);
        });
        
        scrollChat('ticketChatBox');
        
    } catch (error) {
        console.error('Error loading ticket messages:', error);
        const chatBox = document.getElementById('ticketChatBox');
        chatBox.innerHTML = `
            <div style="text-align: center; color: red; margin-top: 50px;">
                <p>Error loading messages: ${error.message}</p>
            </div>
        `;
    }
}

// 🆕 NEW: Enhanced response debugging
function debugNLPResponse(data) {
    console.group('🔍 NLP Response Debug');
    console.log('Raw response:', data);
    
    if (data.fulfillmentText) {
        console.log('✅ Dialogflow fulfillmentText:', data.fulfillmentText);
    }
    
    if (data.response) {
        console.log('✅ Custom response:', data.response);
    }
    
    if (data.queryResult) {
        console.log('✅ QueryResult:', data.queryResult);
    }
    
    if (data.intent) {
        console.log('✅ Intent:', data.intent.displayName);
    }
    
    console.log('✅ Parameters:', data.parameters);
    console.groupEnd();
}

// 🆕 NEW: Test NLP endpoints
async function testNLPIntegration() {
    console.group('🧪 Testing NLP Integration');
    
    const testMessages = [
        "I need help with my benefits",
        "How do I request time off?",
        "I want to talk to HR about a personal matter",
        "What is the vacation policy?"
    ];
    
    for (let message of testMessages) {
        console.log(`Testing: "${message}"`);
        
        try {
            const res = await fetch('{{ url("dialogflow-webhook") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    message: message,
                    sessionId: 'test-session'
                })
            });
            
            const data = await res.json();
            console.log('Response:', data);
            
        } catch (error) {
            console.error('Error:', error);
        }
        
        console.log('---');
    }
    
    console.groupEnd();
}

// Update the existing loadTicketConversation to use the improved function
async function loadTicketConversation(ticketNo) {
    try {
        currentSelectedTicket = ticketNo;
        
        // Update UI
        document.querySelectorAll('.ticket-item-history').forEach(item => item.classList.remove('active'));
        const ticketElement = document.getElementById(`history-ticket-${ticketNo}`);
        if (ticketElement) ticketElement.classList.add('active');
        
        document.getElementById('selectedTicketInfo').innerHTML = `
            <strong>🎫 Ticket: ${ticketNo}</strong> 
            - <span id="ticketStatus">Loading...</span>
            <button class="close-ticket-btn" onclick="closeTicket()">✕ Close</button>
        `;
        
        document.getElementById('ticketReplySection').style.display = 'block';
        
        // 🆕 Use improved message loading
        await loadTicketMessages(ticketNo);
        
        // Load ticket status
        try {
            const statusResponse = await fetch(`{{ url('employee/ticket-status') }}/${ticketNo}`);
            if (statusResponse.ok) {
                const statusData = await statusResponse.json();
                document.getElementById('ticketStatus').innerHTML = 
                    `<span class="ticket-badge ${statusData.status.toLowerCase()}">${statusData.status}</span>`;
            }
        } catch (statusError) {
            console.error('Error loading status:', statusError);
        }
        
    } catch (error) {
        console.error('Error loading ticket conversation:', error);
        document.getElementById('ticketChatBox').innerHTML = `
            <div style="text-align: center; color: red; margin-top: 50px;">
                <p>Error loading conversation: ${error.message}</p>
            </div>
        `;
    }
}

// Replace the existing sendMessage function with the fixed version above

// Add message to chat with proper formatting
function addMessageToChat(chatBox, sender, message, type = 'normal') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-row ${sender}`;
    
    let bubbleClass = 'chat-bubble';
    if (type === 'error') bubbleClass += ' error';
    else if (type === 'success') bubbleClass += ' success';
    else if (type === 'info') bubbleClass += ' info';
    else if (sender === 'bot' && message.includes('HR')) bubbleClass += ' hr-reply';
    
    messageDiv.innerHTML = `
        <div class="${bubbleClass}">
            ${message}
            <div class="message-time">${new Date().toLocaleString()}</div>
        </div>
    `;
    
    chatBox.appendChild(messageDiv);
    scrollChat(chatBox.id);
}

// Start Guided Flow
function startGuidedFlow() {
    const chatBox = document.getElementById('chatBox');
    if (conversationPath.length === 0) {
        chatBox.innerHTML = `
            <div class="chat-row bot">
                <div class="chat-bubble">
                    👋 Hello! I'm Aihra - Your AI Human Resource Assistant. 
                    <div class="message-time">How can I help you today?</div>
                </div>
            </div>`;
    }
    conversationPath = [];
    loadGuidedQuestions();
}

// Load Guided Questions
async function loadGuidedQuestions(parentId = null) {
    const chatBox = document.getElementById('chatBox');
    
    try {
        const url = parentId ? `{{ url('guided') }}/${parentId}` : `{{ url('guided') }}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });

        if (!res.ok) throw new Error(`Server error: ${res.status}`);
        const data = await res.json();

        if (data.type === 'error') throw new Error(data.message);
        if (data.type === 'final' || data.type === 'escalate') {
            const answer = data.data?.[0]?.answer || data.answer || "Thank you for your question!";
            addMessageToChat(chatBox, 'bot', answer);
            return;
        }

        if (!data.data || data.data.length === 0) {
            addMessageToChat(chatBox, 'bot', "No questions available. Please try rephrasing your question.");
            return;
        }

        const options = data.data.map(q => `
            <div class="suggestion" onclick="handleQuestionClick(${q.gq_id}, '${escapeHtml(q.question_text)}')">
                ${q.question_text}
            </div>`).join('');

        chatBox.innerHTML += `
            <div class="chat-row bot">
                <div class="chat-bubble">
                    <strong>${data.message || 'Please choose a topic:'}</strong>
                    <div class="suggestion-box">${options}</div>
                </div>
            </div>`;
        
        scrollChat();
        
    } catch (error) {
        console.error('Error loading questions:', error);
        addMessageToChat(chatBox, 'bot', 
            "Sorry, I cannot load the questions right now. Here are some common topics:", 
            'error'
        );
        
        if (!parentId) {
            chatBox.innerHTML += `
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
        }
        scrollChat();
    }
}

// Handle Question Click
async function handleQuestionClick(id, text) {
    const chatBox = document.getElementById('chatBox');
    addMessageToChat(chatBox, 'user', text);
    conversationPath.push({ type: 'user', message: text });

    try {
        // First query the guided endpoint to see if this selection has children
        const guidedRes = await fetch(`{{ url('guided') }}/${id}`, { headers: { 'Accept': 'application/json' } });
        if (!guidedRes.ok) throw new Error('Failed to load guided question');
        const guidedData = await guidedRes.json();

        // If there are children (type like 'level2'/'level1'), render them
        if (guidedData.type && guidedData.type.startsWith('level') && guidedData.type !== 'final') {
            // Reuse existing renderer to show next-level options
            await loadGuidedQuestions(id);
            return;
        }

        // If this is a final node (LEVEL 3) or an escalate/final response, send the selected text
        // to the dialogflow webhook so the backend will persist both the user's selection and the bot reply.
        const dfRes = await fetch('{{ url("dialogflow-webhook") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text, sessionId: currentSessionId })
        });

        if (!dfRes.ok) {
            // Fallback: show the answer from guidedData if provided
            if (guidedData.type === 'final' && guidedData.data && guidedData.data[0] && guidedData.data[0].answer) {
                addMessageToChat(chatBox, 'bot', guidedData.data[0].answer);
                conversationPath.push({ type: 'bot', message: guidedData.data[0].answer });
            }
            return;
        }

        const dfData = await dfRes.json();

        // Display Dialogflow/backend reply (DialogflowController persists messages server-side)
        if (dfData.fulfillmentText) {
            addMessageToChat(chatBox, 'bot', dfData.fulfillmentText);
            conversationPath.push({ type: 'bot', message: dfData.fulfillmentText });
        } else if (dfData.response) {
            handleCustomResponse(dfData.response, chatBox);
        } else if (dfData.message) {
            addMessageToChat(chatBox, 'bot', dfData.message);
            conversationPath.push({ type: 'bot', message: dfData.message });
        } else if (guidedData.type === 'final' && guidedData.data && guidedData.data[0] && guidedData.data[0].answer) {
            // Last-resort: show guided answer
            addMessageToChat(chatBox, 'bot', guidedData.data[0].answer);
            conversationPath.push({ type: 'bot', message: guidedData.data[0].answer });
        }

    } catch (err) {
        console.error('Error handling question click:', err);
        // Fallback: still try to load guided children/UI
        await loadGuidedQuestions(id);
    }
}

// Load Employee Tickets
async function loadEmployeeTickets() {
    try {
        const ticketList = document.getElementById('ticketList');
        ticketList.innerHTML = '<p class="loading">Loading your tickets</p>';
        
        const response = await fetch('{{ route("employee.tickets") }}');
        const tickets = await response.json();
        
        if (tickets.length === 0) {
            ticketList.innerHTML = '<p style="text-align: center; color: #666;">No support tickets found.</p>';
            return;
        }
        
        let ticketsHtml = '';
        tickets.forEach(ticket => {
            const statusClass = ticket.status ? ticket.status.toLowerCase().replace(' ', '-') : 'open';
            const shortMessage = ticket.message ? 
                (ticket.message.length > 50 ? ticket.message.substring(0, 50) + '...' : ticket.message) : 
                'No message';
                
            ticketsHtml += `
                <div class="ticket-item-history" onclick="loadTicketConversation('${ticket.ticket_no}')" id="history-ticket-${ticket.ticket_no}">
                    <strong>🎫 ${ticket.ticket_no}</strong>
                    <div class="ticket-meta-history">
                        <span class="ticket-badge ${statusClass}">${ticket.status || 'Open'}</span>
                        <span>${new Date(ticket.created_at).toLocaleDateString()}</span>
                        <div style="margin-top: 5px; font-size: 13px; color: #555;">${shortMessage}</div>
                    </div>
                </div>
            `;
        });
        
        ticketList.innerHTML = ticketsHtml;
        
    } catch (error) {
        console.error('Error loading tickets:', error);
        document.getElementById('ticketList').innerHTML = '<p style="color: red; text-align: center;">Error loading tickets</p>';
    }
}

// Load Chat Conversations
async function loadConversations() {
    try {
        const list = document.getElementById('conversationList');
        list.innerHTML = '<p class="loading">Loading your conversations</p>';

        const response = await fetch('{{ route("employee.conversations") }}');
        const convos = await response.json();

        if (!convos || convos.length === 0) {
            list.innerHTML = '<p style="text-align: center; color: #666;">No conversations found.</p>';
            return;
        }

        let html = '';
        convos.forEach(c => {
            const title = c.title ? c.title : (c.first_message ? (new Date(c.created_at).toLocaleDateString() + ' - ' + c.first_message.substring(0,60) + '...') : 'Conversation');
                html += `
                    <div class="ticket-item-history" id="history-convo-${c.id}">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div style="flex:1; cursor:pointer;" onclick="viewConversation('${c.id}')">
                                <strong>💬 ${escapeHtml(title)}</strong>
                                <div class="ticket-meta-history">
                                    <span>${new Date(c.created_at).toLocaleDateString()}</span>
                                    <div style="margin-top: 5px; font-size: 13px; color: #555;">${escapeHtml(c.first_message ? (c.first_message.length > 80 ? c.first_message.substring(0,80) + '...' : c.first_message) : 'No message')}</div>
                                </div>
                            </div>
                            <div style="margin-left:10px;">
                                <button class="close-ticket-btn" onclick="event.stopPropagation(); deleteConversation('${c.id}')">🗑️</button>
                            </div>
                        </div>
                    </div>
                `;
        });

        list.innerHTML = html;
    } catch (err) {
        console.error('Error loading conversations:', err);
        document.getElementById('conversationList').innerHTML = '<p style="color: red; text-align: center;">Error loading conversations</p>';
    }
}

// View specific conversation messages
async function viewConversation(convoId) {
    try {
        // mark active UI
        document.querySelectorAll('#conversationList .ticket-item-history').forEach(item => item.classList.remove('active'));
        const el = document.getElementById(`history-convo-${convoId}`);
        if (el) el.classList.add('active');

        document.getElementById('selectedTicketInfo').innerHTML = `<strong>💬 Conversation</strong> - <span id="ticketStatus"></span> <button class="close-ticket-btn" onclick="closeTicket()">✕ Close</button>`;
        document.getElementById('ticketReplySection').style.display = 'none';

        const chatBox = document.getElementById('ticketChatBox');
        chatBox.innerHTML = '<p class="loading">Loading conversation...</p>';

        const response = await fetch(`{{ url('employee/conversations') }}/${convoId}/messages`);
        const messages = await response.json();

        if (!messages || messages.length === 0) {
            chatBox.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages in this conversation</p>';
            return;
        }

        chatBox.innerHTML = '';
        messages.forEach(msg => {
            if (!msg || msg.is_button) return; // skip guided/button-only entries

            const isEmployee = msg.sender === 'employee';
            const isHR = msg.sender === 'hr';
            const isBot = msg.sender === 'bot' || (!isEmployee && !isHR);

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-row ${isEmployee ? 'user' : 'bot'}`;

            let bubbleClass = 'chat-bubble';
            if (isHR) bubbleClass += ' hr-reply';
            if (isEmployee && msg.message && msg.message.includes('Employee Follow-up')) bubbleClass += ' employee-followup';

            const senderLabel = isEmployee ? ' (You)' : (isHR ? ' (HR)' : '');

            messageDiv.innerHTML = `
                <div class="${bubbleClass}">
                    ${msg.message}
                    <div class="message-time">${new Date(msg.created_at).toLocaleString()}${senderLabel}</div>
                </div>
            `;
            chatBox.appendChild(messageDiv);
        });

        scrollChat('ticketChatBox');

    } catch (err) {
        console.error('Error loading conversation messages:', err);
        document.getElementById('ticketChatBox').innerHTML = '<p style="color: red; text-align: center;">Error loading conversation messages</p>';
    }
}

// Delete a conversation
async function deleteConversation(convoId) {
    if (!confirm('Are you sure you want to remove this conversation from history? This cannot be undone.')) return;

    try {
        const res = await fetch(`{{ url('employee/conversations') }}/${convoId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showNotification('🗑️ Conversation removed');
            // Refresh list
            await loadConversations();

            // If the deleted conversation is currently displayed, clear it
            const chatBox = document.getElementById('ticketChatBox');
            if (chatBox && document.getElementById(`history-convo-${convoId}`) === null) {
                chatBox.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">Conversation removed.</p>';
            }
        } else {
            alert('Failed to delete conversation: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        console.error('Error deleting conversation:', err);
        alert('Failed to delete conversation');
    }
}

// Load Ticket Conversation
async function loadTicketConversation(ticketNo) {
    try {
        currentSelectedTicket = ticketNo;
        
        // Update UI
        document.querySelectorAll('.ticket-item-history').forEach(item => item.classList.remove('active'));
        const ticketElement = document.getElementById(`history-ticket-${ticketNo}`);
        if (ticketElement) ticketElement.classList.add('active');
        
        document.getElementById('selectedTicketInfo').innerHTML = `
            <strong>🎫 Ticket: ${ticketNo}</strong> 
            - <span id="ticketStatus">Loading...</span>
            <button class="close-ticket-btn" onclick="closeTicket()">✕ Close</button>
        `;
        
        document.getElementById('ticketReplySection').style.display = 'block';
        
        // Load messages
        const response = await fetch(`{{ url('employee/messages') }}/${ticketNo}`);
        const messages = await response.json();
        const chatBox = document.getElementById('ticketChatBox');
        chatBox.innerHTML = '';
        
        if (messages.length === 0) {
            chatBox.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages in this ticket</p>';
            return;
        }
        
        // Display messages (tickets include employee and HR replies)
        messages.forEach(msg => {
            if (!msg || msg.is_button) return; // skip guided/button-only entries

            const isEmployee = msg.sender === 'employee';
            const isHR = msg.sender === 'hr';

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-row ${isEmployee ? 'user' : 'bot'}`;

            let bubbleClass = 'chat-bubble';
            if (isHR) bubbleClass += ' hr-reply';
            else if (isEmployee && msg.message && msg.message.includes('Employee Follow-up')) bubbleClass += ' employee-followup';

            const displayMessage = isEmployee && msg.message.includes('Employee Follow-up:') ? msg.message.replace('Employee Follow-up: ', '') : msg.message;

            messageDiv.innerHTML = `
                <div class="${bubbleClass}">
                    ${displayMessage}
                    <div class="message-time">
                        ${new Date(msg.created_at).toLocaleString()}${isEmployee ? ' (You)' : (isHR ? ' (HR)' : '')}
                        ${msg.message && msg.message.includes('Employee Follow-up') ? ' (Follow-up)' : ''}
                    </div>
                </div>
            `;
            chatBox.appendChild(messageDiv);
        });
        
        // Load ticket status
        try {
            const statusResponse = await fetch(`{{ url('employee/ticket-status') }}/${ticketNo}`);
            if (statusResponse.ok) {
                const statusData = await statusResponse.json();
                document.getElementById('ticketStatus').innerHTML = 
                    `<span class="ticket-badge ${statusData.status.toLowerCase()}">${statusData.status}</span>`;
            }
        } catch (statusError) {
            console.error('Error loading status:', statusError);
        }
        
        scrollChat('ticketChatBox');
        
    } catch (error) {
        console.error('Error loading conversation:', error);
        document.getElementById('ticketChatBox').innerHTML = `
            <div style="text-align: center; color: red; margin-top: 50px;">
                <p>Error loading conversation</p>
            </div>
        `;
    }
}

// Send Ticket Reply
async function sendTicketReply() {
    if (!currentSelectedTicket) {
        alert('Please select a ticket first');
        return;
    }
    
    const messageInput = document.getElementById('ticketReplyMessage');
    const message = messageInput.value.trim();
    
    if (!message) {
        alert('Please enter a message');
        return;
    }
    
    try {
        const response = await fetch('{{ route("employee.reply-to-ticket") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ticket_no: currentSelectedTicket,
                message: message
            })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            messageInput.value = '';
            
            // Add message to chat
            const chatBox = document.getElementById('ticketChatBox');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-row user';
            messageDiv.innerHTML = `
                <div class="chat-bubble employee-followup">
                    ${message}
                    <div class="message-time">
                        ${new Date().toLocaleString()} (Follow-up)
                    </div>
                </div>
            `;
            chatBox.appendChild(messageDiv);
            
            document.getElementById('ticketStatus').innerHTML = 
                '<span class="ticket-badge replied">Waiting for HR</span>';
            
            scrollChat('ticketChatBox');
            showNotification('✅ Your reply has been sent to HR!');
            
        } else {
            alert('Failed to send reply: ' + (result.message || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('Error sending reply:', error);
        alert('Failed to send reply. Please try again.');
    }
}

// Handle Escalation Success
function handleEscalationSuccess(ticketNo, fulfillmentText) {
    const chatBox = document.getElementById('chatBox');
    const message = fulfillmentText || "✅ I've escalated your query to our HR team. They'll get back to you soon.";
    
    chatBox.innerHTML += `
        <div class="chat-row bot">
            <div class="chat-bubble success">
                ${message}
            </div>
        </div>
        <div class="chat-row bot">
            <div class="chat-bubble info">
                📋 <strong>Ticket Number:</strong> ${ticketNo}
                <div class="message-time">Keep this number for reference</div>
            </div>
        </div>
    `;
    
    conversationPath.push({ type: 'bot', message: message });
    
    // Switch to history tab
    setTimeout(() => {
        showChatTab('chat-history');
        loadEmployeeTickets();
    }, 2000);
    
    scrollChat();
}

// Utility Functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function sendQuick(message) {
    document.getElementById('userMessage').value = message;
    sendMessage();
}

function restartChat() {
    const chatBox = document.getElementById('chatBox');
    if (chatBox) {
        chatBox.innerHTML = '';
        conversationPath = [];
        startGuidedFlow();
    }
}

// Start a brand new conversation (server creates a new conversation/session)
async function startNewConversation() {
    try {
        const res = await fetch('{{ route("employee.conversations.start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        });

        const data = await res.json();
        if (res.ok && data.success) {
            currentSessionId = data.session_id;
            currentConversationId = data.id;

            // Clear the chat and start guided flow
            const chatBox = document.getElementById('chatBox');
            if (chatBox) {
                chatBox.innerHTML = '';
                conversationPath = [];
                addMessageToChat(chatBox, 'bot', '🔄 Started a new conversation. How can I help you?', 'info');
                // Load guided questions immediately for the new conversation
                await loadGuidedQuestions();
            }
        } else {
            alert('Failed to start a new conversation');
        }
    } catch (err) {
        console.error('Error starting conversation:', err);
        alert('Failed to start a new conversation');
    }
}

function closeTicket() {
    currentSelectedTicket = null;
    document.getElementById('ticketReplySection').style.display = 'none';
    document.getElementById('selectedTicketInfo').innerHTML = 
        '<p style="text-align: center; color: #666; margin: 0;">Select a ticket to view conversation</p>';
    document.getElementById('ticketChatBox').innerHTML = '';
    document.querySelectorAll('.ticket-item-history').forEach(item => item.classList.remove('active'));
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px; background: #28a745; color: white; 
        padding: 12px 20px; border-radius: 5px; z-index: 10000; box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    `;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

function startReplyChecker() {
    setInterval(async () => {
        try {
            const response = await fetch('{{ route("employee.latestTicket") }}');
            const data = await response.json();
            
            if (data.ticket_no && data.has_new_reply) {
                if (currentSelectedTicket === data.ticket_no) {
                    await loadTicketConversation(data.ticket_no);
                }
                showNotification('💌 New reply from HR!');
            }
        } catch (error) {
            console.error('Error checking replies:', error);
        }
    }, 15000); // Check every 15 seconds
}
</script>
@endsection