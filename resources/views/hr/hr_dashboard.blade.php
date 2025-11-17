<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AIHRA - HR Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
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
        .section { 
            display: none; 
        }
        .section.active { 
            display: block; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 8px; 
            text-align: left; 
        }

        input, textarea, button, select {
            margin: 5px 0;
            padding: 8px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        button {
            background: #0c5726;
            color: white;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        /* HR Dashboard Specific Styles */
        .dashboard-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin: 0;
            font-size: 24px;
            color: #0c5726;
        }
        .card p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .card.high { border-left: 4px solid #dc3545; }
        .card.medium { border-left: 4px solid #ffc107; }
        .card.low { border-left: 4px solid #28a745; }
        .card.replied { border-left: 4px solid #007bff; }

        .ticket-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .ticket-list {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-height: 600px;
            overflow-y: auto;
        }
        .chat-container {
            flex: 2;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 600px;
        }

        .ticket-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .ticket-item:hover {
            background: #e9ecef;
            border-color: #0c5726;
        }
        .ticket-item.active {
            background: #e8f5e8;
            border-color: #0c5726;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 5px;
        }
        .badge.high { background: #f8d7da; color: #721c24; }
        .badge.medium { background: #fff3cd; color: #856404; }
        .badge.low { background: #d1ecf1; color: #0c5460; }
        .badge.replied { background: #d4edda; color: #155724; }
        .badge.resolved { background: #e2e3e5; color: #383d41; }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
        }
        .message.employee {
            background: #e3f2fd;
            margin-right: auto;
            border: 1px solid #bbdefb;
        }
        .message.hr {
            background: #e8f5e8;
            margin-left: auto;
            border: 1px solid #c8e6c9;
            text-align: right;
        }
        .message-meta {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .reply-form {
            display: flex;
            gap: 10px;
        }
        .reply-form input {
            flex: 1;
            margin: 0;
        }
        .reply-form button {
            width: auto;
            padding: 8px 20px;
        }

        .resolve-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-top: 5px;
            width: auto;
        }
        .resolved-badge {
            color: #28a745;
            font-size: 12px;
            font-weight: bold;
        }

        .ticket-meta {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .ticket-meta span {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="{{ asset('img/AIHRA_Logo.png') }}" alt="AIHRA Logo" class="sidebar-logo"><h1>AIHRA</h1>
        <ul>
            <li><a href="#announcements" onclick="showSection('announcements')">🏠 Home</a></li>
            <li class="active"><a href="#inbox" onclick="showSection('inbox')">📨 Inbox</a></li>
            <li><a href="#account" onclick="showSection('account')">👤 Account</a></li>
        </ul>
        <div class="account">
            <img src="{{ isset($user) && $user->profile_picture ? asset('uploads/'.$user->profile_picture) : asset('admin/assets/default-profile.png') }}" 
                 alt="HR" 
                 style="margin-left:18px;width:80px;height:80px;border-radius:50%;object-fit:cover;background:#fff;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout">Log Out</button>
            </form>
        </div>
    </div>

    <!-- Main Section -->
    <div class="main">
        <!-- Inbox Section -->
        <div id="inbox" class="section active">
            <h2 class="text-xl font-semibold mb-3 text-green-800">📨 Inbox</h2>
            
            <div class="bg-white p-4 rounded-lg shadow mb-4">
                <h1 class="text-lg font-semibold text-green-800">Hello!</h1>
                <p class="text-gray-700">Welcome to your HR Inbox.</p>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <h3>{{ $inbox->count() }}</h3>
                    <p>Total Tickets</p>
                </div>
                <div class="card high">
                    <h3>{{ $inbox->where('priority', 'high')->count() }}</h3>
                    <p>High Priority</p>
                </div>
                <div class="card medium">
                    <h3>{{ $inbox->where('priority', 'medium')->count() }}</h3>
                    <p>Medium Priority</p>
                </div>
                <div class="card low">
                    <h3>{{ $inbox->where('priority', 'low')->count() }}</h3>
                    <p>Low Priority</p>
                </div>
                <div class="card replied">
                    <h3>{{ $inbox->where('status', 'Replied')->count() }}</h3>
                    <p>Replied</p>
                </div>
            </div>

            <div class="ticket-container">
                <!-- Ticket List -->
                <div class="ticket-list">
                    <h3>Pending Tickets</h3>
                    @forelse($inbox as $ticket)
                        <div class="ticket-item" id="ticket-{{ $ticket->ticket_no }}" onclick="openTicket('{{ $ticket->ticket_no }}')">
                            <strong>🎫 {{ $ticket->ticket_no }}</strong>
                            <div class="ticket-meta">
                                <div>{{ Str::limit($ticket->message, 50) }}</div>
                                <div>
                                    <span class="badge {{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                                    <span class="badge">{{ $ticket->category }}</span>
                                    <span style="color:#666;">{{ number_format($ticket->confidence * 100, 0) }}%</span>
                                </div>
                                @if($ticket->status === 'Replied')
                                    <span class="badge replied">Replied</span>
                                @elseif($ticket->status === 'Resolved')
                                    <span class="badge resolved">Resolved</span>
                                @endif
                                
                                @if($ticket->status !== 'Resolved')
                                <button class="resolve-btn" onclick="event.stopPropagation(); resolveTicket('{{ $ticket->ticket_no }}')">
                                    ✅ Resolve
                                </button>
                                @else
                                <span class="resolved-badge">✅ Resolved</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p>No tickets available.</p>
                    @endforelse
                </div>

                <!-- Chat Container -->
                <div class="chat-container">
                    <h3>Ticket Conversation</h3>
                    <div class="chat-messages" id="chatMessages">
                        <p style="text-align: center; color: #666; margin-top: 50px;">Select a ticket to view conversation</p>
                    </div>
                    <form id="replyForm" class="reply-form">
                        @csrf
                        <input type="hidden" name="ticket_no" id="ticket_no">
                        <input type="text" id="replyMessage" name="message" placeholder="Type your reply..." required>
                        <button type="submit">Send</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ANNOUNCEMENTS SECTION --}}
    <section id="announcements" class="section mt-6">
    <h2 class="text-xl font-semibold mb-3 text-green-800">🏠 Home</h2>

    <div class="bg-white p-4 rounded-lg shadow mb-4">
        <h1 class="text-lg font-semibold text-green-800">Hello!</h1>
        <p class="text-gray-700">Welcome to your HR Dashboard.</p>
    </div>

    {{-- Toggle Button --}}
    <button id="toggleAnnouncementForm" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 mb-3">
        + Add an Announcement
    </button>

    {{-- Announcement Form (hidden by default) --}}
    <form id="announcementForm" action="{{ route('hr.announcements.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-4 mb-4" style="display:none;">
        @csrf
        <div class="mb-3">
            <label class="block font-medium">Title</label>
            <input type="text" name="title" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-3">
            <label class="block font-medium">Description</label>
            <textarea name="description" rows="4" class="w-full border rounded p-2" required></textarea>
        </div>

        <div class="mb-3">
            <label class="block font-medium">Image (optional)</label>
            <input type="file" name="image" class="w-full border rounded p-2">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">
                Proceed
            </button>
            <button type="button" id="cancelAnnouncement" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                Cancel
            </button>
        </div>
    </form>

    {{-- Display announcements --}}
    <div class="space-y-3">
        @php
            $announcements = DB::table('announcements')
                ->where('isActive', 1)
                ->orderBy('createdAt', 'desc')
                ->get();
        @endphp

        @forelse ($announcements as $a)
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-green-800">{{ $a->title }}</h3>
                <p class="text-gray-700 mb-2">{{ $a->description }}</p>
                @if ($a->image)
                    <img src="data:image/jpeg;base64,{{ base64_encode($a->image) }}" 
                        class="rounded announcement-img" 
                        style="max-width: 100%; height: auto;">
                @endif
                <small class="text-gray-500">
                    {{ \Carbon\Carbon::parse($a->createdAt)->timezone('Asia/Manila')->format('M d, Y \a\t h:i A') }}
                </small>
            </div>
        @empty
            <p class="text-gray-500">No announcements yet.</p>
        @endforelse
    </div>
</section>

{{-- ACCOUNT SECTION (separate) --}}
<section id="account" class="section mt-6">
    <h2 class="text-xl font-semibold mb-3 text-green-800">👤 Account Settings</h2>

    {{-- Include your existing hr_profile.blade.php --}}
    @include('hr.hr_profile')
</section>

    </div>

    <script>
        let currentTicket = null;

        function showSection(id) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
            event.target.closest('li').classList.add('active');
        }

        async function resolveTicket(ticketNo) {
            if (!confirm('Mark this ticket as resolved?')) return;
            try {
                const res = await fetch('/hr/resolve-ticket', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ticket_no: ticketNo })
                });
                const result = await res.json();
                console.log('Resolve response:', result);
                if (result.success) {
                    alert('Ticket resolved!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Resolve error:', error);
                alert('Failed to resolve ticket');
            }
        }

        async function openTicket(ticketNo) {
            currentTicket = ticketNo;
            document.querySelectorAll('.ticket-item').forEach(item => item.classList.remove('active'));
            document.getElementById(`ticket-${ticketNo}`).classList.add('active');
            try {
                const response = await fetch(`/hr/messages/${ticketNo}`);
                const messages = await response.json();
                const chat = document.getElementById('chatMessages');
                chat.innerHTML = '';
                if (messages.length === 0) {
                    chat.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages found for this ticket</p>';
                    return;
                }
                messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${msg.sender}`;
                    messageDiv.innerHTML = `
                        <div>${msg.message}</div>
                        <div class="message-meta">
                            ${msg.sender === 'employee' ? 'Employee' : 'HR'} • 
                            ${new Date(msg.created_at).toLocaleString()}
                        </div>
                    `;
                    chat.appendChild(messageDiv);
                });
                document.getElementById('ticket_no').value = ticketNo;
                chat.scrollTop = chat.scrollHeight;
            } catch (error) {
                console.error('Error loading messages:', error);
                document.getElementById('chatMessages').innerHTML = '<p style="color: red;">Error loading messages</p>';
            }
        }

        document.getElementById('replyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const ticketNo = document.getElementById('ticket_no').value;
            const msg = document.getElementById('replyMessage').value.trim();
            if (!msg || !ticketNo) {
                alert('Please select a ticket and enter a message');
                return;
            }
            try {
                console.log('Sending reply for ticket:', ticketNo);
                const res = await fetch(`/hr/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        ticket_no: ticketNo, 
                        message: msg 
                    })
                });
                const result = await res.json();
                console.log('Reply response:', result);
                if (res.ok && result.success) {
                    document.getElementById('replyMessage').value = '';
                    await openTicket(ticketNo);
                } else {
                    alert('Failed to send reply: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Reply error:', error);
                alert('Failed to send reply. Check console for details.');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            showSection('announcements');
        });

        // Toggle announcement form
        const toggleBtn = document.getElementById('toggleAnnouncementForm');
        const form = document.getElementById('announcementForm');
        const cancelBtn = document.getElementById('cancelAnnouncement');

        toggleBtn.addEventListener('click', () => {
            form.style.display = 'block';
            toggleBtn.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            form.style.display = 'none';
            toggleBtn.style.display = 'inline-block';
        });
    </script>
</body>
</html>
