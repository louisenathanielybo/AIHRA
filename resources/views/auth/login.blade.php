@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <div class="login-form">
        <h2>Welcome, I’m AIHRA!</h2>

        @if(session('registered'))
            <p style="color:green; text-align:center;">✅ Account created successfully! Please log in.</p>
        @endif

        @if(session('error'))
            <p style="color:red; text-align:center;">{{ session('error') }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="text" name="employeeNum" placeholder="Employee Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOG IN</button>
        </form>
    </div>

    <div class="login-right">
        <img src="{{ asset('assets/logo.png') }}" alt="AIHRA">
        <h1>AIHRA</h1>
        <p>Your AI Human Resource Assistant</p>
    </div>
</div>
@endsection
