<!DOCTYPE html>
<html>
<head>
    <title>HR Module</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h1>AIHRA</h1>
        <ul>
            <li class="active"><a href="#home" onclick="showSection('home')">Home</a></li>
            <li><a href="#inbox" onclick="showSection('inbox')">Inbox</a></li>
            <li><a href="#announcements" onclick="showSection('announcements')">Announcements</a></li>
            <li><a href="#account" onclick="showSection('account')">Account</a></li>
        </ul>
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

    <!-- Main -->
    <div class="main">
        <!-- Home Section -->
        <div id="home" class="table-container section">
            <h2>Welcome, {{ $user->username ?? 'HR User' }}!</h2>
            <p>This is your HR dashboard. Use the sidebar to navigate between sections.</p>
        </div>

        <!-- ✅ Inbox Section (clean version) -->
        <div id="inbox" class="table-container section" style="display:none;">
            @if(session('success'))
    <div style="background:#d4edda; color:#155724; padding:8px; border-radius:4px; margin-bottom:10px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#f8d7da; color:#721c24; padding:8px; border-radius:4px; margin-bottom:10px;">
        {{ session('error') }}
    </div>
@endif

            <h2>Inbox</h2>

            <!-- Counters -->
            <div style="display:flex; gap:10px; margin-bottom:20px;">
                <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;">
                    <h3>{{ $inbox->count() }}</h3><p>All</p>
                </div>
                <div style="flex:1; text-align:center; background:#ffe5e5; padding:10px; border-radius:6px;">
                    <h3 style="color:red;">{{ $inbox->where('priority', 'Urgent')->count() }}</h3><p>Urgent</p>
                </div>
                <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;">
                    <h3>{{ $inbox->where('priority', 'High')->count() }}</h3><p>High</p>
                </div>
                <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;">
                    <h3>{{ $inbox->where('priority', 'Medium')->count() }}</h3><p>Medium</p>
                </div>
                <div style="flex:1; text-align:center; background:#eee; padding:10px; border-radius:6px;">
                    <h3>{{ $inbox->where('priority', 'Low')->count() }}</h3><p>Low</p>
                </div>
            </div>

            <div style="display:flex; gap:20px;">
                <!-- Urgent Tickets Sidebar -->
                <div style="flex:1; background:#f8f8f8; padding:15px; border-radius:6px;">
                    <h3>Urgent Tickets</h3>
                    @forelse($inbox->where('priority', 'Urgent') as $turgent)
                        <div style="margin-bottom:10px; padding:8px; border:1px solid #ddd; border-radius:4px;">
                            🎫 Ticket #{{ $turgent->ticket_no }}
                        </div>
                    @empty
                        <p>No urgent tickets</p>
                    @endforelse
                </div>

                <!-- Chat-like View -->
                <div style="flex:3; background:#fff; padding:15px; border-radius:6px; border:1px solid #ddd;">
                    <h3>AIHRA</h3>
                    @if($inbox->count())
                        @php $t = $inbox->last(); @endphp
                        <div style="background:#e6ffe6; padding:12px; border-radius:6px; margin-bottom:15px;">
                            <p><strong>Ticket no:</strong> {{ $t->ticket_no }}</p>
                            <p><strong>From:</strong> {{ $t->from_user }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($t->created_at)->format('M d, Y H:i') }}</p>
                            <p><strong>Status:</strong> {{ $t->status }}</p>
                            <p><strong>Inquiry:</strong> {{ $t->message }}</p>
                        </div>

                        <p><strong>ResolvedBy:</strong> hr_{{ $user->username }}</p>
                        <p><strong>ResolvedAt:</strong> {{ now()->format('M d Y, g:ia') }}</p>

                        <form method="POST" action="{{ route('hr.reply') }}">
                            @csrf
                            <input type="hidden" name="ticket_no" value="{{ $t->ticket_no }}">
                            <input type="hidden" name="employeeNum" value="{{ $t->from_user }}">
                            <div style="display:flex; margin-top:10px;">
                                <input type="text" name="message" placeholder="Send a message..." required
                                    style="flex:1; padding:8px; border:1px solid #ccc; border-radius:4px 0 0 4px;">
                                <button type="submit" style="padding:8px 12px; background:#28a745; color:white; border:none; border-radius:0 4px 4px 0;">➤</button>
                            </div>
                        </form>
                    @else
                        <p>No messages yet.</p>
                    @endif

                    <!-- Message Input -->
                    <form method="POST" action="{{ route('hr.reply') }}">
    @csrf
    <input type="hidden" name="ticket_no" value="{{ $t->ticket_no }}">
    <div style="display:flex; margin-top:10px;">
                        @csrf
                        <div style="display:flex; margin-top:10px;">
                            <input type="text" placeholder="Send a message..." style="flex:1; padding:8px; border:1px solid #ccc; border-radius:4px 0 0 4px;">
                            <button type="submit" style="padding:8px 12px; background:#28a745; color:white; border:none; border-radius:0 4px 4px 0;">
                                ➤
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div id="announcements" class="section table-container" style="display:none;">
            <h2>📢 Announcements</h2>

            <div class="card" style="padding:25px; max-width:800px; margin:0 auto; background:#fff; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.08);">
                <form method="POST" action="{{ route('hr.announcements.store') }}">
                    @csrf
                    <div style="margin-bottom:15px;">
                        <label for="title" style="display:block; font-weight:600; margin-bottom:6px;">Title</label>
                        <input type="text" name="title" id="title" required
                            placeholder="Enter announcement title"
                            style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; font-size:15px;">
                    </div>

                    <div style="margin-bottom:15px;">
                        <label for="content" style="display:block; font-weight:600; margin-bottom:6px;">Content</label>
                        <textarea name="content" id="content" required rows="5"
                            placeholder="Write your announcement..."
                            style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; font-size:15px; resize:none;"></textarea>
                    </div>

                    <button type="submit" style="background-color:#0c5726; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">
                        Post Announcement
                    </button>
                </form>
            </div>

            <div style="margin-top:30px; max-width:800px; margin-left:auto; margin-right:auto;">
                @forelse($announcements ?? [] as $a)
                    <div style="background:#f8fdf8; border:1px solid #cde0cd; border-radius:10px; padding:20px; margin-bottom:15px;">
                        <h3 style="margin-top:0; color:#0c5726;">{{ $a->title }}</h3>
                    </div>
                @empty
                    <div style="text-align:center; color:#777; padding:40px;">
                        <p>No announcements yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Account Section -->
        <div id="account" class="table-container section" style="display:none;">
            <h2>Account</h2>
            <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                <img src="{{ isset($user) && $user->profile_picture ? asset('uploads/'.$user->profile_picture) : asset('admin/assets/default-profile.png') }}" 
                     alt="Profile Picture" 
                     style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
                <a href="{{ route('hr.profile') }}" style="text-decoration:none;color:#0c5726;font-weight:bold;">✏️ Edit Profile</a>
            </div>
            <p><strong>About Me:</strong></p>
            <p>{{ $user->about ?? 'No information provided.' }}</p>
        </div>
    </div>

    <script>
        function showSection(id) {
            document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
            document.getElementById(id).style.display = 'block';
            document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
            document.querySelector(`.sidebar ul li a[href="#${id}"]`).parentElement.classList.add('active');
        }
    </script>
</body>
</html>
