<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AIHRA - HR Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 240px;
            background: #0c5726;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
        }
        .sidebar h1 {
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 12px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .sidebar ul li.active a {
            color: #f5d742;
        }
        .main {
            flex: 1;
            padding: 30px;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .ticket-list {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            background: #fafafa;
        }
        .ticket-item {
            background: white;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: 0.2s;
        }
        .ticket-item:hover {
            background: #f0f8f0;
        }
        .chat-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #fff;
            display: flex;
            flex-direction: column;
            height: 500px;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
            margin-bottom: 10px;
        }
        .chat-message {
            margin-bottom: 12px;
        }
        .chat-message.user {
            background: #e8ffe8;
            padding: 10px;
            border-radius: 8px;
        }
        .chat-message.hr {
            background: #e0f0ff;
            padding: 10px;
            border-radius: 8px;
            text-align: right;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 5px;
        }
        .badge.low { background: #c6f6d5; color: #22543d; }
        .badge.medium { background: #fefcbf; color: #744210; }
        .badge.high { background: #fed7d7; color: #742a2a; }
        .badge.urgent { background: #f56565; color: white; }
        .ticket-meta {
            font-size: 13px;
            color: #555;
        }
        .reply-form {
            display: flex;
        }
        .reply-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px 0 0 6px;
        }
        .reply-form button {
            background: #0c5726;
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h1>AIHRA</h1>
            <ul>
                <li class="active"><a href="#home" onclick="showSection('home')">Home</a></li>
                <li><a href="#inbox" onclick="showSection('inbox')">Inbox</a></li>
                <li><a href="#announcements" onclick="showSection('announcements')">Announcements</a></li>
                <li><a href="#account" onclick="showSection('account')">Account</a></li>
            </ul>
        </div>

        <div class="account">
            <img src="{{ isset($user) && $user->profile_picture ? asset('uploads/'.$user->profile_picture) : asset('admin/assets/default-profile.png') }}" 
                 alt="HR" 
                 style="width:80px;height:80px;border-radius:50%;object-fit:cover;background:#fff;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout">Log Out</button>
            </form>
        </div>
    </div>

    <!-- Main Section -->
    <div class="main">
        <!-- Inbox Section -->
        <div id="inbox" class="table-container section">
            
            <h2>📨 HR Inbox</h2>
        <!-- Counters --> <div style="display:flex; gap:10px; margin-bottom:20px;"> <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;"> <h3>{{ $inbox->count() }}</h3><p>All</p> </div> <div style="flex:1; text-align:center; background:#ffe5e5; padding:10px; border-radius:6px;"> <h3 style="color:red;">{{ $inbox->where('priority', 'Urgent')->count() }}</h3><p>Urgent</p> </div> <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;"> <h3>{{ $inbox->where('priority', 'High')->count() }}</h3><p>High</p> </div> <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;"> <h3>{{ $inbox->where('priority', 'Medium')->count() }}</h3><p>Medium</p> </div> <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;"> <h3>{{ $inbox->where('priority', 'Low')->count() }}</h3><p>Low</p> </div> </div>

            <div style="display:flex; gap:20px;">
                <!-- Ticket List -->
                <div style="flex:1;">
                    <h3>Pending Tickets</h3>
                    <div class="ticket-list">
                        @forelse($inbox as $ticket)
                            <div class="ticket-item" onclick="openTicket('{{ $ticket->ticket_no }}')">
                                <strong>🎫 {{ $ticket->ticket_no }}</strong>
                                <div class="ticket-meta">
                                    {{ Str::limit($ticket->message, 40) }}
                                    <br>
                                    <span class="badge {{ strtolower($ticket->priority) }}">{{ $ticket->priority }}</span>
                                    <span class="badge" style="background:#e2e8f0;">{{ $ticket->category }}</span>
                                    <span style="font-size:11px;color:#666;">{{ number_format($ticket->confidence * 100, 0) }}%</span>
                                </div>
                            </div>
                        @empty
                            <p>No tickets available.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Box -->
                <div style="flex:2;">
                    <div class="chat-box" id="chatBox">
                        <div class="chat-messages" id="chatMessages">
                            <p>Select a ticket to view messages.</p>
                        </div>
                        <form id="replyForm" method="POST" action="{{ route('hr.reply') }}" class="reply-form">
    @csrf
    <input type="hidden" name="ticket_no" id="ticket_no">
    <input type="text" id="replyMessage" name="message" placeholder="Type your reply..." required>
    <button type="submit">Send ➤</button>
</form>

                    </div>
                </div>
            </div>
        </div>

        <!-- Other sections remain same -->
    </div>

    <script>
        function showSection(id) {
            document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
            document.getElementById(id).style.display = 'block';
            document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
            document.querySelector(`.sidebar ul li a[href="#${id}"]`).parentElement.classList.add('active');
        }

        function openTicket(ticketNo) {
            const ticket = @json($inbox).find(t => t.ticket_no === ticketNo);
            if (ticket) {
                const chat = document.getElementById('chatMessages');
                chat.innerHTML = `
                    <div class="chat-message user">
                        <strong>From Employee #${ticket.from_user}</strong><br>
                        ${ticket.message}<br>
                        <small>${ticket.created_at}</small>
                    </div>
                `;
                document.getElementById('ticket_no').value = ticket.ticket_no;
            }
        }
        document.getElementById('replyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const ticketNo = document.getElementById('ticket_no').value;
    const msg = document.getElementById('replyMessage').value.trim();
    if (!msg || !ticketNo) return;

    const res = await fetch(`{{ route('hr.reply') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ticket_no: ticketNo, message: msg })
    });

    if (res.ok) {
        document.getElementById('replyMessage').value = '';
        openTicket(ticketNo); // refresh the chat to show HR reply
    }
});

    </script>
</body>
</html>
