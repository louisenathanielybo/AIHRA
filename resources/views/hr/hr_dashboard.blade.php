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
            <img src="{{ isset($user) && $user->profile_pic ? asset('uploads/'.$user->profile_pic) : asset('admin/assets/default-profile.png') }}" 
                 alt="HR" 
                 style="width:80px;height:80px;border-radius:50%;object-fit:cover;background:#fff;">
                 
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="logout" style="background:none; border:none; padding:0; cursor:pointer; color:#fff; font:inherit;">
                    Log Out
                </button>
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

        <!-- Inbox Section -->
        <div id="inbox" class="table-container section" style="display:none;">
            <h2>Inbox Tickets</h2>
            @if(isset($inbox) && $inbox->count())
                <table>
                    <tr>
                        <th>Ticket No</th>
                        <th>From</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Date</th>
                    </tr>
                    @foreach($inbox as $t)
                        <tr>
                            <td>{{ $t->ticket_no }}</td>
                            <td>{{ $t->from_user }}</td>
                            <td>{{ $t->message }}</td>
                            <td>{{ $t->status }}</td>
                            <td>{{ $t->priority }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->created_at)->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p>No tickets found.</p>
            @endif
        </div>

        <!-- Announcements Section -->
        <div id="announcements" class="table-container section" style="display:none;">
            <h2>Announcements</h2>

            <!-- Add new announcement -->
            <form method="POST" action="{{ route('hr.announcements.store') }}" style="margin-bottom:20px;">
                @csrf
                <input type="text" name="title" placeholder="Title" required style="width:100%;padding:8px;margin-bottom:10px;">
                <textarea name="content" placeholder="Write your announcement..." required style="width:100%;padding:8px;height:80px;"></textarea>
                <br>
                <button type="submit" class="btn">Post Announcement</button>
            </form>

            <!-- List announcements -->
            @forelse($announcements ?? [] as $a)
                <div class="card" style="margin-bottom:15px; padding:10px; border:1px solid #eee; border-radius:6px;">
                    <h3>{{ $a->title }}</h3>
                    <p>{!! nl2br(e($a->content)) !!}</p>
                    <small>📅 {{ \Carbon\Carbon::parse($a->created_at)->format('M d, Y H:i') }}</small>
                </div>
            @empty
                <p>No announcements yet.</p>
            @endforelse
        </div>

        <!-- Account Section -->
        <div id="account" class="table-container section" style="display:none;">
            <h2>Account</h2>
            <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                <img src="{{ isset($user) && $user->profile_pic ? asset('uploads/'.$user->profile_pic) : asset('admin/assets/default-profile.png') }}" 
                     alt="Profile Picture" 
                     style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
                <a href="{{ url('profile') }}" style="text-decoration:none;color:#0c5726;font-weight:bold;">✏️ Edit Profile</a>
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
