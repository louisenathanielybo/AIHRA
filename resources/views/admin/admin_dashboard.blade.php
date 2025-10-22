@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('/css/dashboard.css') }}">
<style>
    .sidebar {
        width: 200px;
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
    .section { display: none; }
    .section.active { display: block; }

    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }

    input, textarea, button {
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
</style>
@endsection

@section('content')
<div class="sidebar">
    <h1>AIHRA</h1>
    <ul>
        <li class="active"><a href="#kb" onclick="showSection('kb')">Create Account</a></li>
        <li><a href="#announcements" onclick="showSection('announcements')">Announcements</a></li>
        <li><a href="#feedback" onclick="showSection('feedback')">Feedback & Flags</a></li>
        <li><a href="#profile" onclick="showSection('profile')">Profile</a></li>
    </ul>
    <div class="account">
        <img src="{{ asset('uploads/' . $admin->profile_picture) }}" alt="Admin"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;background:#fff;">
        <a href="{{ route('logout') }}" class="logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>
</div>

<div class="main">
    {{-- CREATE ACCOUNT --}} 
    <div id="kb" class="section active">
        <h2>Create Account</h2>

        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div style="color:red;">
                {{ implode(', ', $errors->all()) }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.account.create') }}">
            @csrf
            <label>Employee Number:</label>
            <input type="text" name="employeeNum" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>First Name:</label>
            <input type="text" name="firstName" required>

            <label>Middle Name:</label>
            <input type="text" name="middleName">

            <label>Last Name:</label>
            <input type="text" name="lastName" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="HR">HR</option>
                <option value="Employee">Employee</option>
            </select>

            <label>Sex:</label>
            <select name="sex" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label>Birthdate:</label>
            <input type="date" name="age" required>

            <button type="submit">Create Account</button>
        </form>

        <h3>Existing Users</h3>
        <table>
            <tr><th>Employee #</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>
            @foreach(DB::table('users')->where('role', '!=', 'Admin')->get() as $user)
                <tr>
                    <td>{{ $user->employeeNum }}</td>
                    <td>{{ $user->firstName }} {{ $user->lastName }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->status }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    {{-- ANNOUNCEMENTS --}}
    <div id="announcements" class="section">
        <h2>Announcements</h2>
        <form method="POST" action="{{ route('admin.announcement.add') }}">
            @csrf
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="content" placeholder="Content" required></textarea>
            <button type="submit">Publish</button>
        </form>
        <table>
            <tr><th>ID</th><th>Title</th><th>Content</th><th>Action</th></tr>
            @forelse($announcements as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ $a->title }}</td>
                    <td><a href="{{ route('admin.announcement.delete', $a->id) }}" onclick="return confirm('Delete this announcement?')">🗑 Delete</a></td>
                </tr>
            @empty
                <tr><td colspan="4">No announcements yet.</td></tr>
            @endforelse
        </table>
    </div>

    {{-- FEEDBACK --}}
    <div id="feedback" class="section">
        <h2>Feedback</h2>
        <table>
            <tr><th>ID</th><th>Rating</th><th>Suggestion</th><th>Date</th></tr>
            @forelse($feedback as $f)
                <tr><td>{{ $f->feedbackID }}</td>
                <td>{{ $f->rating }}</td>
                <td>{{ $f->suggestion }}</td>
                <td>{{ $f->timeStamp }}</td></tr>
            @empty
                <tr><td colspan="4">No feedback yet.</td></tr>
            @endforelse
        </table>
        <h2>Flagged Responses</h2>
        <table>
            <tr><th>ID</th><th>Reason</th><th>Details</th><th>Date</th></tr>
            @forelse($flags as $fl)
                <tr><td>{{ $fl->flaggedID }}</td>
                <td>{{ $fl->reason }}</td>
                <td>{{ $fl->details }}</td>
                <td>{{ $fl->created_at }}</td></tr>
            @empty
                <tr><td colspan="4">No flagged responses yet.</td></tr>
            @endforelse
        </table>
    </div>

    {{-- PROFILE --}}
    <div id="profile" class="section">
        <h2>Profile</h2>
        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif
        <img src="{{ asset('uploads/' . $admin->profile_picture) }}" 
             style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf
            <label>Change Picture:</label>
            <input type="file" name="profile_pic">
            <label>About Me:</label>
            <textarea name="about">{{ $admin->about ?? '' }}</textarea>
            <button type="submit">Update</button>
        </form>
    </div>
</div>

<script>
function showSection(id) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('.sidebar ul li').forEach(li => li.classList.remove('active'));
    event.target.closest('li').classList.add('active');
}
</script>
@endsection
