@extends('layouts.app') {{-- or hr.hr_layout if you have one --}}

@section('content')

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<div class="profile-container">
    <h2>My Profile</h2>

    {{-- ✅ Success message --}}
    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
        <script>
            setTimeout(function() {
                window.location.href = "{{ route('hr.dashboard') }}"; 
            }, 1500);
        </script>
    @endif

    {{-- ✅ Show default avatar if no profile pic --}}
    <div style="margin-bottom:20px; text-align:center;">
        @if($user->profile_picture)
            <img src="{{ asset('uploads/' . $user->profile_picture) }}" 
                 alt="Profile Picture" 
                 class="profile-pic">
        @else
            <img src="{{ asset('admin/assets/default-profile.png') }}" 
                 alt="Default Profile Picture" 
                 class="profile-pic">
        @endif
    </div>

    {{-- ✅ Display extra info --}}
    <div style="margin-bottom:20px; text-align:center;">
        <p><strong>Employee Number:</strong> {{ $user->employeeNum ?? 'N/A' }}</p>
        <p><strong>Age:</strong> {{ $user->age ?? 'N/A' }}</p>
    </div>

    {{-- ✅ Profile Update Form --}}
    <form method="POST" action="{{ route('hr.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="profile_picture">Change Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture">
        </div>

        <div class="form-group">
            <label for="about">About Me:</label>
            <textarea name="about" id="about" rows="4">{{ old('about', $user->about) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

@endsection
