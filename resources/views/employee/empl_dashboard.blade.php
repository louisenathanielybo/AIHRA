@extends('layouts.app')
@php
    $pageTitle = "Admin Dashboard";
@endphp

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sb-brand">
            <img src="{{ asset('assets/logo.png') }}" alt="AIHRA Logo" class="sb-logo">
            <h1>AIHRA</h1>
        </div>
        
        <nav class="sb-nav">
            <a href="#kb" class="sb-link active" onclick="showSection('kb')">
                <i>📚</i> Knowledge Base
            </a>
            <a href="#announcements" class="sb-link" onclick="showSection('announcements')">
                <i>📢</i> Announcements
            </a>
            <a href="#create-account" class="sb-link" onclick="showSection('create-account')">
                <i>👥</i> Create Account
            </a>
            <a href="#feedback" class="sb-link" onclick="showSection('feedback')">
                <i>💬</i> Feedback & Flags
            </a>
            <a href="#profile" class="sb-link" onclick="showSection('profile')">
                <i>👤</i> Profile
            </a>
        </nav>

        <div class="sb-bottom">
            <div class="account">
                <img src="{{ asset('uploads/' . $admin->profile_picture) }}" alt="Admin Profile">
                <a href="{{ route('logout') }}" class="logout"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Log Out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="welcome-message">
            Welcome back, <span>{{ session('name') }}</span> ({{ session('role') }})
        </div>

        {{-- KNOWLEDGE BASE --}}
        <div id="kb" class="section active">
            <h2>Knowledge Base</h2>
            <form method="POST" action="{{ route('admin.kb.add') }}">
                @csrf
                <div class="form-group">
                    <label>Question:</label>
                    <input type="text" name="question" required>
                </div>
                <div class="form-group">
                    <label>Answer:</label>
                    <textarea name="answer" rows="4" required></textarea>
                </div>
                <button type="submit">Add Entry</button>
            </form>

            <table>
                <thead>
                    <tr><th>ID</th><th>Question</th><th>Answer</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($kb as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->question }}</td>
                            <td>{{ $row->answer }}</td>
                            <td>
                                <a href="{{ route('admin.kb.delete', $row->id) }}" 
                                   onclick="return confirm('Delete this entry?')">🗑 Delete</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No knowledge base entries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ANNOUNCEMENTS --}}
        <div id="announcements" class="section">
            <h2>Announcements</h2>
            <form method="POST" action="{{ route('admin.announcement.add') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="title" placeholder="Title" required>
                </div>
                <div class="form-group">
                    <textarea name="content" placeholder="Content" rows="4" required></textarea>
                </div>
                <button type="submit">Publish</button>
            </form>
            <table>
                <thead>
                    <tr><th>ID</th><th>Title</th><th>Content</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($announcements as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>{{ $a->title }}</td>
                            <td>{{ $a->content }}</td>
                            <td>
                                <a href="{{ route('admin.announcement.delete', $a->id) }}" 
                                   onclick="return confirm('Delete this announcement?')">🗑 Delete</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No announcements yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CREATE ACCOUNT --}}
        <div id="create-account" class="section">
            <h2>Create New Account</h2>
            
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CREATE ACCOUNT FORM --}}
            <div id="create-account-form">
                <form method="POST" action="{{ route('admin.create-account') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="employeeNum">Employee Number *</label>
                        <input type="text" id="employeeNum" name="employeeNum" value="{{ old('employeeNum') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" value="{{ old('firstName') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="middleName">Middle Name</label>
                        <input type="text" id="middleName" name="middleName" value="{{ old('middleName') }}">
                    </div>

                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR Manager</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Department Manager</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sex">Gender *</label>
                        <select id="sex" name="sex" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('sex') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="age">Age *</label>
                        <input type="number" id="age" name="age" value="{{ old('age') }}" min="18" max="65" required>
                    </div>

                    <div class="form-group">
                        <label for="about">About (Optional)</label>
                        <textarea id="about" name="about" placeholder="Brief description about the user" rows="3">{{ old('about') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Account Status *</label>
                        <select id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <button type="submit">Create Account</button>
                </form>
            </div>

            {{-- EDIT ACCOUNT FORM --}}
            <div id="edit-account-form" style="display: none;">
                <h3>Edit Account</h3>
                <form method="POST" action="" id="edit-form">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="edit_employeeNum" name="employeeNum">
                    
                    <div class="form-group">
                        <label for="edit_email">Email Address *</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_firstName">First Name *</label>
                        <input type="text" id="edit_firstName" name="firstName" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_lastName">Last Name *</label>
                        <input type="text" id="edit_lastName" name="lastName" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_middleName">Middle Name</label>
                        <input type="text" id="edit_middleName" name="middleName">
                    </div>

                    <div class="form-group">
                        <label for="edit_role">Role *</label>
                        <select id="edit_role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="employee">Employee</option>
                            <option value="admin">Administrator</option>
                            <option value="hr">HR Manager</option>
                            <option value="manager">Department Manager</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_sex">Gender *</label>
                        <select id="edit_sex" name="sex" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_age">Age *</label>
                        <input type="number" id="edit_age" name="age" min="18" max="65" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_about">About (Optional)</label>
                        <textarea id="edit_about" name="about" placeholder="Brief description about the user" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_status">Account Status *</label>
                        <select id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>

                    <button type="submit">Update Account</button>
                    <button type="button" class="cancel" onclick="cancelEdit()">Cancel</button>
                </form>
            </div>

            <h3 style="margin-top: 30px;">Existing Accounts</h3>
            <table>
                <thead>
                    <tr>
                        <th>Employee #</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->employeeNum }}</td>
                            <td>{{ $user->firstName }} {{ $user->lastName }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{{ ucfirst($user->status) }}</td>
                            <td>
                                <a href="#" onclick="editAccount('{{ $user->employeeNum }}')">✏️ Edit</a>
                                @if($user->employeeNum != Auth::user()->employeeNum)
                                    | <a href="{{ route('admin.delete-account', $user->employeeNum) }}" 
                                         onclick="return confirm('Are you sure you want to delete this account?')">🗑 Delete</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FEEDBACK --}}
        <div id="feedback" class="section">
            <h2>Feedback</h2>
            <table>
                <thead>
                    <tr><th>ID</th><th>Rating</th><th>Suggestion</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($feedback as $f)
                        <tr>
                            <td>{{ $f->feedbackID }}</td>
                            <td>{{ $f->rating }}</td>
                            <td>{{ $f->suggestion }}</td>
                            <td>{{ $f->timeStamp }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No feedback yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            
            <h2 style="margin-top: 30px;">Flagged Responses</h2>
            <table>
                <thead>
                    <tr><th>ID</th><th>Reason</th><th>Details</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($flags as $fl)
                        <tr>
                            <td>{{ $fl->flaggedID }}</td>
                            <td>{{ $fl->reason }}</td>
                            <td>{{ $fl->details }}</td>
                            <td>{{ $fl->created_at }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No flagged responses yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PROFILE --}}
        <div id="profile" class="section">
            <h2>Profile</h2>
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            
            <div style="text-align: center;">
                <img src="{{ asset('uploads/' . $admin->profile_picture) }}" 
                     class="profile-image" alt="Admin Profile">
            </div>
            
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Change Picture:</label>
                    <input type="file" name="profile_pic" accept="image/*">
                </div>
                <div class="form-group">
                    <label>About Me:</label>
                    <textarea name="about" rows="4">{{ $admin->about ?? '' }}</textarea>
                </div>
                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<script>
function showSection(id) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    
    // Remove active class from all sidebar links
    document.querySelectorAll('.sb-link').forEach(link => link.classList.remove('active'));
    
    // Show selected section
    document.getElementById(id).classList.add('active');
    
    // Add active class to clicked sidebar link
    event.target.closest('.sb-link').classList.add('active');
}

async function editAccount(employeeNum) {
    try {
        const response = await fetch(`/admin/get-account/${employeeNum}`);
        const user = await response.json();
        
        if (user.error) {
            alert(user.error);
            return;
        }

        // Populate the form fields
        document.getElementById('edit_employeeNum').value = user.employeeNum;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_firstName').value = user.firstName;
        document.getElementById('edit_lastName').value = user.lastName;
        document.getElementById('edit_middleName').value = user.middleName || '';
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_sex').value = user.sex;
        document.getElementById('edit_age').value = user.age;
        document.getElementById('edit_about').value = user.about || '';
        document.getElementById('edit_status').value = user.status;

        // Update form action
        document.getElementById('edit-form').action = `/admin/update-account/${user.employeeNum}`;

        // Show edit form and hide create form
        document.getElementById('create-account-form').style.display = 'none';
        document.getElementById('edit-account-form').style.display = 'block';
        
        // Scroll to the form
        document.getElementById('edit-account-form').scrollIntoView({ behavior: 'smooth' });
        
    } catch (error) {
        console.error('Error fetching user data:', error);
        alert('Error loading user data');
    }
}

function cancelEdit() {
    document.getElementById('edit-account-form').style.display = 'none';
    document.getElementById('create-account-form').style.display = 'block';
    document.getElementById('edit-form').reset();
}
</script>
@endsection