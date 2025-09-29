@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('public/css/dashboard.css') }}">
<style>
    /* Chat bubble styles */
    .chat-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 20px;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .chat-bubble.user {
        background: #d4f7c5;
        align-self: flex-end;
        border-bottom-right-radius: 5px;
    }

    .chat-bubble.bot {
        background: #e5e5ea;
        align-self: flex-start;
        border-bottom-left-radius: 5px;
    }

    .sidebar {
        width: 220px;
        background: #0c5726;
        color: white;
        height: 100vh;
        float: left;
        padding: 20px;
    }

    .sidebar h1 {
        font-size: 22px;
        margin-bottom: 20px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin-bottom: 15px;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    .sidebar ul li.active a {
        text-decoration: underline;
    }

    .main {
        margin-left: 240px;
        padding: 20px;
    }
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
    @if(session('feedback_success'))
        <p style="color: green; text-align:center; font-weight:bold;">
            ✅ Feedback submitted successfully!
        </p>
    @endif

    <!-- Home Section -->
    <div id="home" class="table-container section">
        <h2>Announcements</h2>
        @forelse($announcements as $announcement)
            <div class='card' style='margin-bottom:15px; padding:10px; border:1px solid #eee; border-radius:6px;'>
                <h3>{{ $announcement->title }}</h3>
                <p>{!! nl2br(e($announcement->content)) !!}</p>
            </div>
        @empty
            <p>No announcements yet.</p>
        @endforelse
    </div>

    <!-- Chat Section -->
    <div id="chat" class="table-container section" style="display:none;">
        <h2>Chatbot</h2>
        <div id="chatBox" style="width:100%; height:400px; border:1px solid #ccc; border-radius:6px; padding:10px; overflow-y:auto; background:#f9f9f9; margin-bottom:10px; display:flex; flex-direction:column; gap:8px;"></div>
        <div style="display:flex; gap:10px;">
            <input type="text" id="userMessage" placeholder="Type your message..." style="flex:1; padding:10px; border-radius:20px; border:1px solid #ccc;">
            <button onclick="sendMessage()" style="padding:10px 20px; background:#0c5726; color:white; border:none; border-radius:20px; cursor:pointer;">Send</button>
        </div>
    </div>

    <!-- Feedback Section -->
<div id="feedback" class="table-container section" style="display:none;">
    <h2>Give Feedback</h2>

    <form method="POST" action="{{ route('feedback.store') }}" class="feedback-form">
        @csrf

        <!-- Rating -->
        <label for="ratingValue" style="font-weight:bold;">Your Rating:</label>
        <div class="star-rating" style="margin:10px 0;">
            @for($i=1;$i<=5;$i++)
                <span class="star" data-value="{{ $i }}">★</span>
            @endfor
        </div>
        <input type="hidden" name="rating" id="ratingValue" required>

        <!-- Suggestion -->
        <label for="suggestion" style="font-weight:bold; display:block; margin-top:15px;">Your Suggestion:</label>
        <textarea 
            name="suggestion" 
            id="suggestion" 
            class="input" 
            placeholder="Write your feedback here..."
            style="width:100%; min-height:100px; padding:10px; border:1px solid #ccc; border-radius:6px; resize: vertical;" 
            required></textarea>

        <!-- Submit Button -->
        <div style="margin-top:15px;">
            <button type="submit" class="btn review">✅ Submit Feedback</button>
        </div>
    </form>
</div>


    <!-- Account Section -->
    <div id="account" class="table-container section" style="display:none;">
        <h2>Account</h2>
        <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
            <img src="{{ asset('uploads/' . Auth::user()->profile_picture) }}" alt="Profile Picture" style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
            <a href="{{ route('profile.edit') }}" style="text-decoration:none;color:#0c5726;font-weight:bold;">✏️ Edit Profile</a>
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

function sendMessage() {
    let msg = document.getElementById('userMessage').value;
    if (msg.trim() === '') return;

    let chatBox = document.getElementById('chatBox');
    chatBox.innerHTML += `<div class="chat-bubble user"><b>You:</b> ${msg}</div>`;

    fetch('{{ url("dialogflow-webhook") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            queryResult: { queryText: msg },
            employeeNum: '{{ Auth::user()->employeeNum }}'
        })
    })
    .then(res => res.json())
    .then(data => {
        let botMessage = data.fulfillmentText;
        let isEscalated = botMessage.includes("forwarded to HR");

        chatBox.innerHTML += `<div class="chat-bubble bot" style="${isEscalated ? 'background:#fff3cd; color:#856404;' : ''}">
                                <b>Bot:</b> ${botMessage}
                              </div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(err => {
        chatBox.innerHTML += `<div class="chat-bubble bot" style="background:#f8d7da; color:#721c24;">
                                <b>Error:</b> Could not connect.
                              </div>`;
    });

    document.getElementById('userMessage').value = '';
}

// ⭐ Star rating handler
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star');
    const ratingValue = document.getElementById('ratingValue');

    stars.forEach((star, index) => {
        star.addEventListener('mouseover', () => {
            stars.forEach((s, i) => s.style.color = i <= index ? '#ffc107' : '#ccc');
        });

        star.addEventListener('mouseout', () => {
            stars.forEach((s, i) => s.style.color = i < ratingValue.value ? '#ffc107' : '#ccc');
        });

        star.addEventListener('click', () => {
            ratingValue.value = index + 1;
            stars.forEach((s, i) => s.style.color = i <= index ? '#ffc107' : '#ccc');
        });
    });
});
</script>
@endsection
