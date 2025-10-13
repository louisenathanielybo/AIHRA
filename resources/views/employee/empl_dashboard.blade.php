@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('/css/dashboard.css') }}">
<style>
    .chat-box {
        width: 100%;
        height: 400px;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 10px;
        overflow-y: auto;
        background: #f9f9f9;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: 10px;
    }

    .chat-row {
        display: flex;
        align-items: flex-end;
    }
    .chat-row.user { justify-content: flex-end; }
    .chat-row.bot { justify-content: flex-start; }

    .chat-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 20px;
        line-height: 1.4;
        word-wrap: break-word;
    }
    .chat-row.user .chat-bubble {
        background: #d4f7c5;
        border-bottom-right-radius: 5px;
    }
    .chat-row.bot .chat-bubble {
        background: #e5e5ea;
        border-bottom-left-radius: 5px;
    }

    .suggestion-box {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .suggestion {
        background: #f0f0f0;
        padding: 15px;
        border-radius: 8px;
        flex: 1 1 calc(50% - 10px);
        text-align: center;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .suggestion:hover { background: #0c5726; color: #fff; }

    .sub-questions {
        margin-top: 10px;
    }
    .sub-questions .suggestion {
        flex: 1 1 100%;
        text-align: left;
        font-size: 14px;
        padding: 10px;
    }

    .chat-input {
        margin-top: 10px;
        display: flex;
        gap: 10px;
    }
    .chat-input input {
        flex: 1;
        padding: 10px;
        border-radius: 20px;
        border: 1px solid #ccc;
    }
    .chat-input button {
        padding: 0 20px;
        background: #0c5726;
        color: white;
        border: none;
        border-radius: 20px;
        cursor: pointer;
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
    <div id="chat" class="table-container section" style="display:none;">
    <h2>Chatbot</h2>
    <div id="chatBox" class="chat-box">
        <div class="chat-row bot">
            <div class="chat-bubble">
                Hi! I’m Aihra - Your AI Human Resource Assistant. How can I help you?<br>
                Please choose a topic below:
            </div>
        </div>

        <!-- Topics -->
        <div class="chat-row bot" id="topics">
            <div class="suggestion-box">
                <div class="suggestion" onclick="showSubQuestions('employment')">Employment</div>
                <div class="suggestion" onclick="showSubQuestions('promotion')">Promotion</div>
                <div class="suggestion" onclick="showSubQuestions('benefits')">Benefits</div>
                <div class="suggestion" onclick="showSubQuestions('development')">Employee Development</div>
            </div>
        </div>

        <!-- Sub-questions -->
        <div class="chat-row bot sub-questions" id="employment-questions" style="display:none;">
            <div class="suggestion-box">
                <div class="suggestion" onclick="sendQuick('How long is a regular day’s work?')">How long is a regular day’s work?</div>
                <div class="suggestion" onclick="sendQuick('What will happen if I’m late 5 days in a row?')">What if I’m late 5 days in a row?</div>
            </div>
        </div>
        <div class="chat-row bot sub-questions" id="promotion-questions" style="display:none;">
            <div class="suggestion-box">
                <div class="suggestion" onclick="sendQuick('What are the requirements for promotion?')">Promotion requirements?</div>
                <div class="suggestion" onclick="sendQuick('How long do I need to work to be promoted?')">How long to be promoted?</div>
            </div>
        </div>
        <div class="chat-row bot sub-questions" id="benefits-questions" style="display:none;">
            <div class="suggestion-box">
                <div class="suggestion" onclick="sendQuick('What medical benefits do I have?')">Medical benefits?</div>
                <div class="suggestion" onclick="sendQuick('Do we have financial aid for emergencies?')">Financial aid?</div>
            </div>
        </div>
        <div class="chat-row bot sub-questions" id="development-questions" style="display:none;">
            <div class="suggestion-box">
                <div class="suggestion" onclick="sendQuick('Will the school pay for my seminars?')">Seminars covered?</div>
                <div class="suggestion" onclick="sendQuick('Who is eligible for schooling privileges?')">Schooling privileges?</div>
            </div>
        </div>
    </div>
    <div id="chatBox">
  <div id="chatMessages" style="height:400px;overflow-y:auto;"></div>
</div>


    <div class="chat-input">
        <input type="text" id="userMessage" placeholder="Type your message..." />
        <button onclick="sendMessage()">Send</button>
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
function showSection(id) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(id).style.display = 'block';
}
function showSubQuestions(topic) {
    // hide the topics completely
    document.getElementById('topics').style.display = 'none';

    // hide all other sub-questions
    document.querySelectorAll('.sub-questions').forEach(el => el.style.display = 'none');

    // show only the clicked topic’s questions
    document.getElementById(topic + '-questions').style.display = 'block';

    scrollChat();
}

function sendMessage() {
    let msg = document.getElementById('userMessage').value;
    if (!msg.trim()) return;

    let chatBox = document.getElementById('chatBox');
    chatBox.innerHTML += `<div class="chat-row user"><div class="chat-bubble">${msg}</div></div>`;
    scrollChat();

    fetch('{{ url("dialogflow-webhook") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ queryResult: { queryText: msg }, employeeNum: '{{ Auth::user()->employeeNum }}' })
    })
    .then(res => res.json())
    .then(data => {
        chatBox.innerHTML += `<div class="chat-row bot"><div class="chat-bubble">${data.fulfillmentText}</div></div>`;
        scrollChat();
    })
    .catch(() => {
        chatBox.innerHTML += `<div class="chat-row bot"><div class="chat-bubble" style="background:#f8d7da;color:#721c24;">Error: Could not connect.</div></div>`;
        scrollChat();
    });

    document.getElementById('userMessage').value = '';
}

function sendQuick(message) {
    document.getElementById('userMessage').value = message;
    sendMessage();
}

function scrollChat() {
    let chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
}

document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('ratingValue');
    let selectedRating = 0;

    stars.forEach((star, index) => {
        const value = index + 1;

        // Hover effect
        star.addEventListener('mouseover', () => {
            stars.forEach((s, i) => {
                s.style.color = i < value ? '#ffc107' : '#ccc';
            });
        });

        // Restore selected rating when mouse leaves
        star.addEventListener('mouseout', () => {
            stars.forEach((s, i) => {
                s.style.color = i < selectedRating ? '#ffc107' : '#ccc';
            });
        });

        // Click to select rating
        star.addEventListener('click', () => {
            selectedRating = value;
            ratingInput.value = value;
            stars.forEach((s, i) => {
                s.style.color = i < value ? '#ffc107' : '#ccc';
            });
        });
    });
});

const ticketInput = document.getElementById('ticket_no');
let ticketNo = ticketInput ? ticketInput.value : null;



// employee sending message
document.getElementById('employeeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('employeeMessage').value.trim();
    if (!msg) return;

    await fetch(`/employee/send`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ from_user: '{{ Auth::user()->employeeNum ?? "unknown" }}', message: msg })
    });

    document.getElementById('employeeMessage').value = '';
    loadMessages();
});


</script>

<script>

async function loadMessages() {
    const ticketNo = document.getElementById('ticket_no').value.trim();
    if (!ticketNo) return; // No ticket yet

    try {
       const res = await fetch(`/chat/messages/${ticketNo}`);

        if (!res.ok) throw new Error("Failed to fetch");
        const data = await res.json();

        const chatEl = document.getElementById('chatMessages');
        chatEl.innerHTML = '';

        data.forEach(msg => {
            const side = msg.sender === 'employee' ? 'left' : 'right';
            const color = msg.sender === 'employee' ? '#e8ffe8' : '#e0f0ff';
            chatEl.innerHTML += `
                <div style="text-align:${side}; margin:6px;">
                    <div style="display:inline-block; background:${color}; padding:8px 12px; border-radius:8px;">
                        ${msg.message}
                    </div><br>
                    <small>${new Date(msg.created_at).toLocaleString()}</small>
                </div>`;
        });

        chatEl.scrollTop = chatEl.scrollHeight;
    } catch (err) {
        console.error("Error loading messages", err);
    }
}

</script>

@endsection