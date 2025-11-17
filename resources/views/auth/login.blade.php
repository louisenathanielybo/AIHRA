@php
    $pageTitle = "Login Page";
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.header')
</head>

<body class="forAll" style="overflow: hidden;">

<img src="{{ asset('assets/facade.jpg') }}" alt="Site Logo" class="facadeBG">
<div class="overlay"></div>

<div class="login">
    <div class="loginContainer">
        <h2 class="welcome">Welcome, I'm AIHRA!</h2>

        @if(session('registered'))
            <p style="color:green; text-align:center;">✅ Account created successfully! Please log in.</p>
        @endif

        @if(session('error'))
            <p style="color:red; text-align:center;">{{ session('error') }}</p>
        @endif

        <form class="login-form" action="{{ route('login') }}" method="POST">
        @csrf
            <div class="inputBox">
                <i class="fa-solid fa-user icon"></i>
                <input type="text" name="employeeNum" class="inputField" placeholder="Employee Number" required>
            </div>

            <div class="inputBox">
                <i class="fa-solid fa-lock icon"></i>
                <input type="password" name="password" class="inputField" placeholder="Password" required>
            </div>

            <button type="submit" class="loginButton">LOG IN</button>
        </form>
    </div>

    <div class="logoContainer">
        <img src="{{ asset('assets/AIHRA_Logo.png') }}" alt="AIHRA Logo" class="loginLogo">
        <p class="loginTitle">AIHRA</p>
        <p class="loginText">Your AI Human Resource Assistant</p>
    </div>
</div>

<footer class="pageFooter">
	&copy; Divine Word College of Calapan, Inc.
</footer>

@include('includes.footer')
</body>
</html>
