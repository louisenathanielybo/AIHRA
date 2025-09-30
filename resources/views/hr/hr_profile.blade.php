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
    <div style="margin-bottom:20px;">
        @if($user->profile_picture)
            <img src="{{ asset('uploads/' . $user->profile_picture) }}" 
                 alt="Profile Picture" 
                 style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
        @else
            <img src="{{ asset('admin/assets/default-profile.png') }}" 
                 alt="Default Profile Picture" 
                 style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
        @endif
    </div>

    {{-- ✅ Profile Update Form --}}
    <form method="POST" action="{{ route('hr.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div style="margin-bottom:15px;">
            <label for="profile_picture">Change Profile Picture:</label><br>
            <input type="file" name="profile_picture" id="profile_picture">
        </div>

        <div style="margin-bottom:15px;">
            <label for="about">About Me:</label><br>
            <textarea name="about" id="about" rows="4" style="width:100%; padding:8px;">
                {{ old('about', $user->about) }}
            </textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
@endsection
