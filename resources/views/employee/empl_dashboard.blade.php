@php
    $pageTitle = "Employee Dashboard";
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.header')
<style>
    :root {
        --primary: #2d5a3d;
        --secondary: #4a8c5e;
        --accent: #3a7d54;
        --light: #f0f7f2;
        --dark: #1e3b2a;
        --success: #4caf50;
        --warning: #ff9800;
        --danger: #f44336;
        --gray: #789984;
        --sidebar-width: clamp(200px, 20vw, 250px);
        --card-bg: #ffffff;
        --hover-light: #e8f5e8;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Layout */
    .sidebar {
        background: linear-gradient(180deg, var(--primary) 0%, var(--dark) 100%);
        box-shadow: 2px 0 10px rgba(45, 90, 61, 0.1);
        width: var(--sidebar-width);
        color: white;
        min-height: 100vh;
        padding: 20px 0;
        position: fixed;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1000;
    }
    .sidebar-header {
        padding: 0 20px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 20px;
    }

    .sidebar-header h2 {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #e8f5e8;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        padding-bottom: 120px; /* Space for footer */
    }

    .sidebar-menu li {
        margin-bottom: 5px;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #e8f5e8;
        text-decoration: none;
        transition: all 0.3s;
        border-radius: 0 8px 8px 0;
        margin-right: 10px;
    }

    .sidebar-menu a:hover, 
    .sidebar-menu a.active {
        background: rgba(255,255,255,0.15);
        border-left: 4px solid var(--secondary);
        color: white;
    }

    .sidebar-menu i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
        color: #a8d5b5;
    }
    
    .main-content { 
        flex: 1;
        margin-left: var(--sidebar-width); 
        padding: clamp(15px, 2vw, 20px);
        background: #f8fdf9;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .section {
        flex: 1;
        min-height: 600px;
        padding-bottom: 60px;
    }

    /* Chat Container */
    .chat-container {
        height: 500px;
        border: 1px solid #e0efe5;
        border-radius: 12px 12px 0 0;
        background: white;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Chat Box */
    #chatBox, #ticketChatBox, .chat-box {
        overflow-y: scroll;
        overflow-x: hidden;
        padding: 20px;
        background: #f8f9fa;
        flex: 1;
        min-height: 0;
        max-height: 420px;
    }

    /* Chat Rows */
    .chat-row {
        display: flex;
        margin-bottom: 15px;
        width: 100%;
    }

    .chat-row.user {
        justify-content: flex-end;
    }

    .chat-row.bot {
        justify-content: flex-start;
    }

    /* Chat Bubbles */
    .chat-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
        word-wrap: break-word;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .chat-row.user .chat-bubble {
        background: var(--secondary);
        color: white;
        border-bottom-right-radius: 5px;
    }

    .chat-row.bot .chat-bubble {
        background: white;
        color: #333;
        border: 1px solid #ddd;
        border-bottom-left-radius: 5px;
        position: relative;
    }

    /* Flag Button */
    .flag-btn {
        position: absolute;
        bottom: -8px;
        right: -8px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.3s;
        opacity: 0.6;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        z-index: 10;
    }

    .chat-row.bot:hover .flag-btn {
        opacity: 1;
    }

    .flag-btn:hover {
        background: #fff3cd;
        border-color: #ffc107;
        transform: scale(1.1);
    }

    /* Flag Modal */
    .flag-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }

    .flag-modal-content {
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: min(500px, 90vw);
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }

    .flag-option {
        padding: 12px;
        margin: 8px 0;
        border: 2px solid #e0efe5;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .flag-option:hover {
        border-color: var(--secondary);
        background: var(--hover-light);
    }

    .flag-option.selected {
        border-color: var(--secondary);
        background: var(--hover-light);
    }

    /* Special Message Types */
    .chat-bubble.hr-reply {
        background: #e8f5e8 !important;
        border: 1px solid #c8e6c9 !important;
        color: #155724 !important;
    }

    .chat-bubble.employee-followup {
        background: #e3f2fd !important;
        border: 1px solid #bbdefb !important;
        color: #1565c0 !important;
    }

    .chat-bubble.error {
        background: #f8d7da !important;
        color: #721c24 !important;
        border-color: #f5c6cb !important;
    }

    .chat-bubble.success {
        background: #d4edda !important;
        color: #155724 !important;
        border-color: #c3e6cb !important;
    }

    .chat-bubble.info {
        background: #fff3cd !important;
        color: #856404 !important;
        border-color: #ffeaa7 !important;
    }

    /* Message Time */
    .message-time {
        font-size: 11px;
        color: #666;
        margin-top: 5px;
        opacity: 0.8;
        font-style: italic;
    }
    
    /* Message Time for user bubbles (white text on green background) */
    .chat-row.user .message-time {
        color: rgba(255, 255, 255, 0.9);
        opacity: 1;
    }

    /* Suggestion Box */
    .suggestion-box {
        margin-top: 10px;
    }

    .suggestion {
        display: inline-block;
        background: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 8px 16px;
        margin: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .suggestion:hover {
        background: var(--secondary);
        color: white;
        transform: translateY(-2px);
    }

    /* Input Area */
    .chat-input-container {
        border: 1px solid #e0efe5;
        border-top: 2px solid #e0efe5;
        padding: 15px;
        background: white;
        border-radius: 0 0 12px 12px;
        margin-top: 0;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        position: relative;
        z-index: 10;
        flex-shrink: 0;
        min-height: 70px;
    }

    /* Combined message + guided containers */
    #messagesContainer {
        padding: 0;
        background: transparent;
        flex-shrink: 0;
    }

    #guidedContainer {
        padding: 0;
        background: transparent;
        margin-top: 10px;
        flex-shrink: 0;
    }

    .chat-input {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .chat-input input {
        flex: 1;
        border: 2px solid #ddd;
        border-radius: 25px;
        padding: 14px 22px;
        outline: none;
        font-size: 15px;
        min-height: 45px;
    }

    .chat-input input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .chat-input button {
        background: #28a745;
        color: white;
        border: none;
        border-radius: 25px;
        padding: 14px 28px;
        cursor: pointer;
        transition: background 0.3s ease;
        font-weight: 500;
        min-height: 45px;
        font-size: 15px;
    }

    .chat-input button:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    /* Chat Tabs */
    .chat-tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
        margin-bottom: 20px;
        background: white;
        border-radius: 10px 10px 0 0;
        padding: 0 10px;
    }

    .tab-button {
        background: none;
        border: none;
        padding: 12px 24px;
        cursor: pointer;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #666;
    }

    .tab-button.active {
        background: #28a745;
        color: white;
    }

    .tab-button:hover:not(.active) {
        background: #e9ecef;
        color: #333;
    }

    .chat-tab {
        display: none;
    }

    .chat-tab.active {
        display: block;
    }

    /* Ticket History Styles */
    .history-container {
        height: 600px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .ticket-list {
        height: 30%;
        min-height: 180px;
        max-height: 250px;
        overflow-y: auto;
        border-bottom: 1px solid #ddd;
        padding: 15px;
        background: white;
    }

    .ticket-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    #selectedTicketInfo {
        padding: 15px;
        background: white;
        border-bottom: 1px solid #ddd;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Ticket Items */
    .ticket-item-history {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .ticket-item-history:hover {
        background: #e3f2fd;
        border-color: #28a745;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }

    .ticket-item-history.active {
        background: #e3f2fd;
        border-color: #28a745;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .ticket-meta-history {
        font-size: 12px;
        color: #666;
        margin-top: 8px;
    }

    /* Badges */
    .ticket-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
        margin-right: 8px;
    }

    .ticket-badge.open { background: #fff3cd; color: #856404; }
    .ticket-badge.replied { background: #d4edda; color: #155724; }
    .ticket-badge.resolved { background: #e2e3e5; color: #383d41; }
    .ticket-badge.pending { background: #cce7ff; color: #004085; }
    .ticket-badge.escalated { background: #f8d7da; color: #721c24; }

    /* Sections */
    .section {
        display: none;
    }

    .table-container {
        background: white;
        padding: 20px;
        padding-bottom: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);
        margin-bottom: 30px;
        border: 1px solid #e0efe5;
    }
    
    /* Announcements Container */
    .announcements-container {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        padding-right: 10px;
    }

    .announcements-container::-webkit-scrollbar {
        width: 8px;
    }

    .announcements-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .announcements-container::-webkit-scrollbar-thumb {
        background: var(--secondary);
        border-radius: 10px;
    }

    .announcements-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary);
    }
    
    #chat.section {
        padding-bottom: 30px;
        overflow: hidden;
    }
    
    #chat .chat-layout {
        max-height: calc(100vh - 250px);
        overflow: visible;
    }

    /* Account Section */
    .account {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.2);
    }

    .account img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #fff;
        margin-bottom: 10px;
        border: 3px solid rgba(255,255,255,0.3);
    }

    .logout {
        color: white;
        text-decoration: none;
        display: block;
        padding: 8px;
        border-radius: 5px;
        background: rgba(255,255,255,0.1);
        transition: background 0.3s ease;
    }

    .logout:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Sidebar Footer */
    .sidebar-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px;
    }

    .account-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: rgba(255,255,255,0.1);
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    .account-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        border: 2px solid #a8d5b5;
    }

    .account-details {
        flex: 1;
    }

    .account-name {
        font-weight: 500;
        font-size: 0.9rem;
        color: white;
    }

    .account-role {
        font-size: 0.8rem;
        color: #a8d5b5;
    }

    .logout-btn {
        background: none;
        border: none;
        color: #a8d5b5;
        cursor: pointer;
        padding: 5px;
        border-radius: 3px;
        transition: all 0.3s;
    }

    .logout-btn:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }

    /* Feedback Form */
    .feedback-form {
        max-width: 600px;
    }

    .feedback-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
    }

    .feedback-form textarea {
        width: 100%;
        height: 120px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
        resize: vertical;
    }

    .feedback-form textarea:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .star-rating {
        display: flex;
        gap: 5px;
        margin-bottom: 20px;
    }

    .star {
        font-size: 24px;
        cursor: pointer;
        color: #ccc;
        transition: color 0.2s ease;
    }

    .star:hover,
    .star.active {
        color: #ffc107;
    }

    .btn.review {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.3s ease;
    }

    .btn.review:hover {
        background: #218838;
    }

    /* Inline chat-area New/Restart buttons removed — use the top-left '➕ New Chat' button in the left panel */

    /* Close Ticket Button */
    .close-ticket-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .close-ticket-btn:hover {
        background: #5a6268;
    }

    /* Loading States */
    .loading {
        text-align: center;
        color: #666;
        padding: 20px;
    }

    .loading::after {
        content: '...';
        animation: dots 1.5s steps(4, end) infinite;
    }

    @keyframes dots {
        0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
        40% { color: #666; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
        60% { text-shadow: .25em 0 0 #666, .5em 0 0 rgba(0,0,0,0); }
        80%, 100% { text-shadow: .25em 0 0 #666, .5em 0 0 #666; }
    }

    /* New UI_AIHRA styles */
    .dashboard {
        display: flex;
        min-height: 100vh;
        width: 100%;
    }


    /* Header Section */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0efe5;
    }

    .header h1 {
        color: var(--primary);
        font-size: 1.8rem;
        font-weight: 600;
    }

    .header-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .date-range {
        background: white;
        padding: 8px 15px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);
        font-size: 0.9rem;
        color: var(--primary);
        border: 1px solid #e0efe5;
    }

    .user-account-header {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 8px 15px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);
        cursor: pointer;
        border: 1px solid #e0efe5;
        transition: all 0.3s;
    }

    .user-account-header:hover {
        box-shadow: 0 4px 12px rgba(45, 90, 61, 0.12);
        transform: translateY(-1px);
    }

    .user-avatar-header {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        border: 2px solid #a8d5b5;
    }

    .welcome-message {
        font-family: "Poppins", sans-serif;
        font-weight: 600;
        font-size: 24px;
        color: var(--primary);
        margin-bottom: 20px;
    }
    .welcome-message span {
        color: var(--secondary);
    }
    
    /* Top Navigation Ribbon */
    .top-nav {
        display: flex;
        gap: 8px;
        background: white;
        padding: 8px 12px;
        border-radius: 25px;
        box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);
        border: 1px solid #e0efe5;
    }
    
    .top-nav-link {
        padding: 10px 24px;
        border-radius: 20px;
        text-decoration: none;
        color: #666;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }
    
    .top-nav-link:hover {
        background: #f0f0f0;
        color: #333;
    }
    
    .top-nav-link.active {
        background: #28a745;
        color: white;
        border-color: #28a745;
        transform: scale(1.05);
    }

    /* Responsive Design */
    @media (max-width: 1600px) {
        :root {
            --sidebar-width: 250px;
        }
    }

    @media (max-width: 1400px) {
        :root {
            --sidebar-width: 240px;
        }
        .main-content {
            padding: 18px;
        }
    }

    @media (max-width: 1200px) {
        :root {
            --sidebar-width: 230px;
        }
        .top-nav-link {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        .main-content {
            padding: 15px;
        }
    }

    @media (max-width: 992px) {
        :root {
            --sidebar-width: 220px;
        }
        .main-content {
            padding: 15px;
        }
    }

    @media (max-width: 768px) {
        :root {
            --sidebar-width: 200px;
        }
        .sidebar {
            width: var(--sidebar-width);
        }
        .main-content {
            margin-left: var(--sidebar-width);
        }
        .top-nav {
            flex-wrap: wrap;
            gap: 6px;
        }
        .top-nav-link {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .sidebar {
            position: fixed;
            left: -220px;
            width: 220px;
            transition: left 0.3s ease;
        }
        .sidebar.mobile-open {
            left: 0;
        }
        .main-content {
            margin-left: 0;
            padding: 10px;
            width: 100%;
        }
        .top-nav {
            padding: 6px 8px;
        }
        .top-nav-link {
            padding: 6px 10px;
            font-size: 0.75rem;
        }
    }

</style>
</head>

<body class="forAll">

<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2 style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                AIHRA
                <img src="{{ asset('assets/AIHRA_Logo.png') }}" alt="AIHRA Logo" style="width: 50px; height: 50px;"> 
            </h2>
        </div>

        <ul class="sidebar-menu">
            <li><a href="#home" onclick="showSection('home')" class="active" id="link-home"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#chat" onclick="showSection('chat')" id="link-chat"><i class="fas fa-comments"></i> Chat</a></li>
            <li><a href="#feedback" onclick="showSection('feedback')" id="link-feedback"><i class="fas fa-star"></i> Feedback</a></li>
            <li><a href="#account" onclick="showSection('account')" id="link-account"><i class="fas fa-user"></i> Account</a></li>
        </ul>

        <div class="sidebar-footer">
            <div class="account-info">
                @if(isset(Auth::user()->profile_picture) && Auth::user()->profile_picture)
                    <img src="{{ asset('uploads/' . Auth::user()->profile_picture) }}" alt="Profile" class="account-avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #a8d5b5;">
                @else
                    <div class="account-avatar">
                        {{ substr(Auth::user()->firstName ?? Auth::user()->name, 0, 1) }}{{ substr(Auth::user()->lastName ?? '', 0, 1) }}
                    </div>
                @endif
                <div class="account-details">
                    <div class="account-name">{{ Auth::user()->firstName ?? Auth::user()->name }} {{ Auth::user()->lastName ?? '' }}</div>
                    <div class="account-role">Employee</div>
                </div>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
                <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Log Out">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div style="background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08); display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border: 1px solid #e0efe5;">
            <h1 class="welcome-message" style="margin: 0; font-size: 1.8rem; font-weight: 600;">Welcome, <span>{{ Auth::user()->name }}</span>!</h1>
            
            <nav class="top-nav">
                <a href="#home" onclick="showSection('home')" class="top-nav-link active" id="top-link-home">Home</a>
                <a href="#chat" onclick="showSection('chat')" class="top-nav-link" id="top-link-chat">Chat</a>
                <a href="#feedback" onclick="showSection('feedback')" class="top-nav-link" id="top-link-feedback">Feedback</a>
                <a href="#account" onclick="showSection('account')" class="top-nav-link" id="top-link-account">Account</a>
            </nav>
        </div>
        
        <!-- Home Section -->
        <div id="home" class="section" style="display: block;">
            <div class="announcements-container" style="max-height: calc(100vh - 250px); overflow-y: auto; padding-right: 10px;">
                <h2 style="margin-bottom: 20px; color: var(--primary);">📢 Announcements</h2>
                @forelse($announcements as $announcement)
                    <div class='card' style='margin-bottom:20px; padding:20px; border:1px solid #e0efe5; border-radius:12px; background:white; box-shadow: 0 4px 12px rgba(45, 90, 61, 0.08);'>
                        <h3 style='margin:0 0 10px 0; color: var(--primary); font-weight: 600;'>{{ $announcement->title }}</h3>
                        <p style='margin:0 0 15px 0; color:#666;'>{{ $announcement->description }}</p>
                        @if ($announcement->image)
                            <img src="data:image/jpeg;base64,{{ base64_encode($announcement->image) }}" 
                                alt="{{ $announcement->title }}"
                                style="max-width: 600px; width: 100%; height: auto; border-radius: 8px; margin-top: 10px;">
                        @endif
                        @if(isset($announcement->createdAt))
                            <small style="display: block; margin-top: 10px; color: var(--gray);">
                                {{ \Carbon\Carbon::parse($announcement->createdAt)->timezone('Asia/Manila')->format('M d, Y \a\t h:i A') }}
                            </small>
                        @endif
                    </div>
                @empty
                    <p style='text-align:center; color:#666;'>No announcements yet.</p>
                @endforelse
            </div>
        </div>

    <!-- Chat Section -->
    <div id="chat" class="table-container section" style="display:none;">
        <h2><img src="{{ asset('assets/AIHRA_Logo.png') }}" alt="AIHRA" style="width: 50px; height: 50px; vertical-align: middle; margin-right: 8px;"> AI HR Assistant</h2>
        
        <!-- Chat Layout: Left = Conversations & Tickets, Right = Viewer -->
        <style>
            .chat-layout { display: flex; gap: 20px; align-items: flex-start; }
            .left-panel { width: 320px; }
            .left-panel .panel-box { background: white; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 12px; }
            #conversationList, #ticketList { max-height: 400px; overflow-y: auto; }
            .left-panel .panel-box h4 { margin: 0 0 8px 0; }
            #convoPanel, #ticketsPanel { max-height: 470px; }
            .left-panel .new-chat-btn { display: inline-block; width: 100%; margin-bottom: 10px; padding: 14px 20px; text-align: center; background: #28a745; color: white; border-radius: 10px; cursor: pointer; border: none; font-weight: 600; font-size: 15px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3); transition: all 0.3s ease; }
            .left-panel .new-chat-btn:hover { background: #218838; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4); }
            .left-panel .new-chat-btn:active { transform: translateY(0); }
            .right-panel { flex: 1; }
            /* Make chat-container full height inside right-panel */
            .right-panel .chat-container { 
                height: auto; 
                max-height: calc(100vh - 280px);
            }
        </style>

        <div class="chat-layout">
            <div class="left-panel">
                <button id="leftNewConversationBtn" class="new-chat-btn" onclick="startNewConversation()"><i class="fa-solid fa-plus"></i> New Chat</button>
                <div style="display:flex; gap:8px; margin:10px 0 6px 0;">
                    <button id="chatsTabBtn" class="tab-button active" style="flex:1;" onclick="showChats()">💬 Chats</button>
                    <button id="ticketsTabBtn" class="tab-button" style="flex:1;" onclick="showTickets()">🎫 Tickets</button>
                </div>
                <div id="convoPanel" class="panel-box">
                    <div id="conversationList">
                        <p class="loading">Loading your conversations</p>
                    </div>
                </div>

                <div id="ticketsPanel" class="panel-box">
                    <h4 style="margin-top:0;">🎫 Your Support Tickets</h4>
                    <div id="ticketList">
                        <p class="loading">Loading your tickets</p>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <!-- Viewer Header / Info -->
                <div id="selectedTicketInfo" style="margin-bottom:12px; background: white; padding: 12px; border: 1px solid #ddd; border-radius:8px;">
                    <p style="text-align: center; color: #666; margin: 0;">Select a conversation or ticket from the left to view messages</p>
                </div>

                <!-- Chat Container -->
                <div class="chat-container">
                    <div id="chatBox" class="chat-box">
                            <div id="messagesContainer"></div>
                            <div id="guidedContainer">
                                <div class="chat-row bot">
                                    <div class="chat-bubble">
                                        👋 Hello! I'm Aihra - Your AI Human Resource Assistant. 
                                        <div class="message-time">How can I help you today?</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- Separate Input Container -->
                <div class="chat-input-container">
                    <div class="chat-input">
                        <input type="text" id="userMessage" placeholder="Type your message here..." />
                        <button onclick="sendMessage()" style="background: #28a745; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='#218838'; this.style.transform='scale(1.05)'" onmouseout="this.style.background='#28a745'; this.style.transform='scale(1)'">
                            <i class="fa-solid fa-paper-plane" style="color: white; font-size: 18px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Ticket Chat Box (reused for viewing conversations & tickets) -->
                <!-- Legacy ticket viewer elements remain for backward compatibility but are not used when viewing chats/tickets.
                     Messages now render into #messagesContainer and guided suggestions into #guidedContainer so both
                     message history and guided questions can be shown together in a single section. -->
            </div>
        </div>
    </div>

    <!-- Feedback Section -->
    <div id="feedback" class="section" style="display:none;">
        <div style="max-width: 600px; margin: 50px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; color: #333; margin: 0 0 10px 0; font-size: 24px;">Rate your experience! 😊</h2>
            <p style="text-align: center; color: #666; margin: 0 0 30px 0; font-size: 14px;">We would like to hear from you! How's your experience with AIHRA?</p>
            
            <form method="POST" action="{{ route('feedback.store') }}" onsubmit="handleFeedbackSubmit(event)">
                @csrf
                
                <div class="star-rating" style="display: flex; justify-content: center; align-items: center; margin-bottom: 15px;">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star" data-value="{{ $i }}" style="font-size: 48px; cursor: pointer; color: #ddd; margin: 0 5px;">★</span>
                    @endfor
                </div>
                <p id="ratingDescription" style="text-align: center; color: #666; font-size: 16px; font-weight: 500; margin-bottom: 25px; min-height: 24px;">Select a rating</p>
                <input type="hidden" name="rating" id="ratingValue" required>

                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Subject</label>
                <input type="text" name="subject" id="feedbackSubject" placeholder="Brief subject line..." style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box;" required>

                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Comment</label>
                <textarea name="suggestion" id="suggestion" placeholder="We would like to know what you think ..." 
                    style="width: 100%; height: 120px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; box-sizing: border-box;" required></textarea>

                <button type="submit" style="width: 100%; margin-top: 20px; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.3s ease;" 
                    onmouseover="this.style.background='#218838'" onmouseout="this.style.background='#28a745'">SUBMIT</button>
            </form>
        </div>
    </div>

    <!-- Account Section -->
    <div id="account" class="section" style="display:none;">
        <div style="display: flex; gap: 20px; max-width: 1100px; margin: 0 auto;">
            <!-- Left Panel - Profile -->
            <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: bold;">Profile</h3>
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{{ asset('uploads/' . Auth::user()->profile_picture) }}" alt="Profile Picture" 
                         style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #ddd;">
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 4px; font-weight: 500; color: #333; font-size: 13px;">Name</label>
                    <div style="padding: 10px; background: #e6f7f0; border-radius: 8px; color: #333; font-size: 14px;">
                        {{ Auth::user()->name }}
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 4px; font-weight: 500; color: #333; font-size: 13px;">Email</label>
                    <div style="padding: 10px; background: #e6f7f0; border-radius: 8px; color: #333; font-size: 14px;">
                        {{ Auth::user()->email }}
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 4px; font-weight: 500; color: #333; font-size: 13px;">Employee number</label>
                    <div style="padding: 10px; background: #e6f7f0; border-radius: 8px; color: #333; font-size: 14px;">
                        {{ Auth::user()->employeeNum }}
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 4px; font-weight: 500; color: #333; font-size: 13px;">Age</label>
                        <div style="padding: 10px; background: #e6f7f0; border-radius: 8px; color: #333; font-size: 14px;">
                            @php
                                if (Auth::user()->dob) {
                                    $dob = new DateTime(Auth::user()->dob);
                                    $today = new DateTime();
                                    $age = $today->diff($dob)->y;
                                    echo $age;
                                } else {
                                    echo Auth::user()->age ?? 'N/A';
                                }
                            @endphp
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 4px; font-weight: 500; color: #333; font-size: 13px;">Sex</label>
                        <div style="padding: 10px; background: #e6f7f0; border-radius: 8px; color: #333; font-size: 14px;">
                            {{ Auth::user()->sex ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Account Settings -->
            <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: bold;">Account Settings</h3>

                <button onclick="showEditProfileModal()" style="width: 100%; padding: 12px; margin-bottom: 12px; background: #e6f7f0; border: none; border-radius: 8px; text-align: left; cursor: pointer; font-size: 14px; font-weight: 500; transition: background 0.3s;" onmouseover="this.style.background='#d0f0e0'" onmouseout="this.style.background='#e6f7f0'">
                    Edit Profile
                </button>

                <button onclick="showChangePasswordModal()" style="width: 100%; padding: 12px; margin-bottom: 12px; background: #e6f7f0; border: none; border-radius: 8px; text-align: left; cursor: pointer; font-size: 14px; font-weight: 500; transition: background 0.3s;" onmouseover="this.style.background='#d0f0e0'" onmouseout="this.style.background='#e6f7f0'">
                    Change Password
                </button>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #333; font-size: 13px;">About</label>
                    <div onclick="showAboutModal()" style="padding: 12px; background: #e6f7f0; border-radius: 8px; color: #333; cursor: pointer; min-height: 50px; transition: background 0.3s; font-size: 14px;" onmouseover="this.style.background='#d0f0e0'" onmouseout="this.style.background='#e6f7f0'">
                        {{ Auth::user()->about ?: 'Click to add information about yourself...' }}
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; transition: background 0.3s;" onmouseover="this.style.background='#218838'" onmouseout="this.style.background='#28a745'">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #e6fbf5; padding: 30px; border-radius: 15px; max-width: 420px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="position: relative; text-align: center; margin-bottom: 20px;">
                <button onclick="closeEditProfileModal()" style="position: absolute; left: 0; top: 0; background: none; border: none; cursor: pointer; padding: 5px;">
                    <i class="fa-solid fa-arrow-left" style="font-size: 20px; color: #333;"></i>
                </button>
                <h3 style="margin: 0; font-size: 18px; font-weight: bold;">Edit Profile</h3>
            </div>
            <form id="editProfileForm" onsubmit="handleProfileUpdate(event)">
                @csrf
                
                <div style="margin-bottom: 20px; text-align: center; position: relative;">
                    <div style="display: inline-block; position: relative;">
                        <img id="profilePreview" src="{{ asset('uploads/' . Auth::user()->profile_picture) }}" 
                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #ddd;">
                        <label for="profilePictureInput" style="position: absolute; bottom: 0; right: 0; background: #28a745; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white;">
                            <i class="fa-solid fa-camera" style="color: white; font-size: 14px;"></i>
                        </label>
                        <input type="file" id="profilePictureInput" name="profile_picture" accept="image/*" style="display: none;">
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">First name</label>
                        <input type="text" name="firstName" value="{{ Auth::user()->firstName }}" required style="width: 100%; padding: 12px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Last name</label>
                        <input type="text" name="lastName" value="{{ Auth::user()->lastName }}" required style="width: 100%; padding: 12px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Middle name</label>
                    <input type="text" name="middleName" value="{{ Auth::user()->middleName }}" style="width: 100%; padding: 12px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Email</label>
                        <input type="email" name="email" value="{{ Auth::user()->email }}" required style="width: 100%; padding: 12px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Date of Birth</label>
                        <input type="date" name="dob" value="{{ Auth::user()->dob }}" max="{{ date('Y-m-d', strtotime('-1 year')) }}" style="width: 100%; padding: 12px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Employee number</label>
                    <input type="text" name="employeeNum" value="{{ Auth::user()->employeeNum }}" readonly style="width: 100%; padding: 12px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0; color: #666;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Sex</label>
                    <select name="sex" style="width: 100%; padding: 12px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                        <option value="Male" {{ Auth::user()->sex == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ Auth::user()->sex == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <button type="submit" style="width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 20px;">Save</button>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #e6fbf5; padding: 30px; border-radius: 15px; max-width: 360px; width: 90%;">
            <div style="position: relative; text-align: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: bold;">Change Password</h3>
            </div>
            <form id="changePasswordForm" onsubmit="handlePasswordChange(event)">
                @csrf
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Current Password</label>
                    <div style="position: relative;">
                        <input type="password" name="current_password" id="currentPassword" required style="width: 100%; padding: 12px; padding-right: 40px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                        <button type="button" onclick="togglePassword('currentPassword')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <i class="fa-solid fa-eye" style="color: #28a745;"></i>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">New Password</label>
                    <div style="position: relative;">
                        <input type="password" name="new_password" id="newPassword" required style="width: 100%; padding: 12px; padding-right: 40px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                        <button type="button" onclick="togglePassword('newPassword')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <i class="fa-solid fa-eye" style="color: #28a745;"></i>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Confirm New Password</label>
                    <div style="position: relative;">
                        <input type="password" name="new_password_confirmation" id="confirmPassword" required style="width: 100%; padding: 12px; padding-right: 40px; border: none; border-radius: 8px; box-sizing: border-box; background: #d0f0e0;">
                        <button type="button" onclick="togglePassword('confirmPassword')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <i class="fa-solid fa-eye" style="color: #28a745;"></i>
                        </button>
                    </div>
                </div>

                <button type="button" onclick="closeChangePasswordModal()" style="width: 100%; padding: 15px; background: #d3d3d3; color: #666; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-bottom: 10px;">Cancel</button>
                <button type="submit" style="width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">Save</button>
            </form>
        </div>
    </div>

    <!-- About Modal -->
    <div id="aboutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: #e6fbf5; padding: 30px; border-radius: 15px; max-width: 420px; width: 90%;">
            <div style="position: relative; text-align: center; margin-bottom: 20px;">
                <button onclick="closeAboutModal()" style="position: absolute; left: 0; top: 0; background: none; border: none; cursor: pointer; padding: 5px;">
                    <i class="fa-solid fa-arrow-left" style="font-size: 20px; color: #333;"></i>
                </button>
                <h3 style="margin: 0; font-size: 18px; font-weight: bold;">About</h3>
            </div>
            <form id="aboutForm" onsubmit="handleAboutUpdate(event)">
                @csrf
                
                <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #333;">Tell us about yourself</label>
                <textarea name="about" rows="8" style="width: 100%; padding: 12px; margin-bottom: 15px; border: none; border-radius: 8px; box-sizing: border-box; resize: vertical; font-family: inherit; background: #d0f0e0;" placeholder="Share something about yourself...">{{ Auth::user()->about }}</textarea>

                <button type="submit" style="width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">Save</button>
            </form>
        </div>
    </div>

    <!-- Flag Response Modal -->
    <div id="flagModal" class="flag-modal">
        <div class="flag-modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: var(--primary);">⚠️ Flag this response</h3>
                <button onclick="closeFlagModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
            </div>
            
            <p style="color: #666; margin-bottom: 20px;">Please select a reason for flagging this response:</p>
            
            <div id="flagReasonsList">
                <div class="flag-option" onclick="selectFlagReason('Wrong Info', this)">
                    <strong>❌ Wrong Info</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">The information provided is incorrect or outdated</p>
                </div>
                <div class="flag-option" onclick="selectFlagReason('Incomplete', this)">
                    <strong>⚠️ Incomplete</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">The response doesn't fully answer the question</p>
                </div>
                <div class="flag-option" onclick="selectFlagReason('Confusing', this)">
                    <strong>🤔 Confusing</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">The response is hard to understand</p>
                </div>
                <div class="flag-option" onclick="selectFlagReason('Irrelevant', this)">
                    <strong>🔀 Irrelevant</strong>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">The response doesn't relate to the question</p>
                </div>
            </div>
            
            <input type="hidden" id="flaggedQuery" />
            <input type="hidden" id="flaggedResponse" />
            <input type="hidden" id="selectedReason" />
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button onclick="closeFlagModal()" style="flex: 1; padding: 12px; background: #e0efe5; color: var(--primary); border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">Cancel</button>
                <button onclick="submitFlag()" style="flex: 1; padding: 12px; background: var(--secondary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">Submit Flag</button>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <img src="{{ asset('assets/dwccLogo.png') }}" class="dwccLogo"/>
        <div class="displayicn">
            <div>
                <a href="https://dwcc.edu.ph" target="_blank">
                    <i class="fa-solid fa-globe footerIcon"></i>
                </a>
                <p>Website</p>
            </div>
            <div>
                <a href="https://www.facebook.com/DWCC1946" target="_blank">
                    <i class="fa-brands fa-facebook-f footerIcon"></i>
                </a>
                <p>Facebook</p>
            </div>
            <div>
                <a href="mailto:hrecruitment@dwcc.edu.ph">
                    <i class="fa-solid fa-envelope footerIcon"></i>
                </a>
                <p>E-mail</p>
            </div>
        </div>
        <hr class="footer-line">
        <div class="footer-text">
            <p><strong>Divine Word College of Calapan, Inc.</strong> Gov. Infantado St., Calapan City, Oriental Mindoro, Philippines</p>
            <p>&copy; DWCC Human Resources Office. All Rights Reserved.</p>
        </div>
    </div>
</div>

<script>
// Global variables
let currentLevel = 0;
let conversationPath = [];
let currentSelectedTicket = null;
let sendingTicket = false;
let currentSessionId = '{{ session()->getId() }}';
let currentConversationId = null;
let lastEscalatedTicketNo = null;
let lastEscalationMessage = null;
let isChangingSection = false; // Flag to prevent recursive hash updates

// Initialize when page loads
document.addEventListener('DOMContentLoaded', async function() {
    initializeStarRating();
    initializeChat();
    // Load existing conversations and tickets into the left panel
    try {
        await loadConversations();
    } catch (e) { console.warn('Initial conversations load failed', e); }
    try {
        await loadEmployeeTickets();
    } catch (e) { console.warn('Initial tickets load failed', e); }
    // Default to chats view and main chat visible
    showChats();
    // Auto-restore last active conversation if available
    try {
        const lastConvoId = localStorage.getItem('lastConversationId');
        if (lastConvoId) {
            await viewConversation(lastConvoId);
        }
    } catch (e) { console.debug('No last conversation to restore or failed restore', e); }
    requestNotificationPermission();
    
    // Restore the active section based on URL hash
    const hash = window.location.hash.substring(1); // Remove the # character
    if (hash && ['home', 'chat', 'feedback', 'account'].includes(hash)) {
        showSection(hash);
    }
});

// Handle hash changes (browser back/forward or direct hash changes)
window.addEventListener('hashchange', function() {
    if (isChangingSection) {
        isChangingSection = false;
        return; // Ignore hash changes triggered by showSection
    }
    const hash = window.location.hash.substring(1);
    if (hash && ['home', 'chat', 'feedback', 'account'].includes(hash)) {
        showSection(hash);
    }
});

// Toggle between Conversations and Tickets in left panel
function showChats() {
    // Ensure left tabs and conversation list are visible
    document.getElementById('convoPanel').style.display = 'block';
    const convList = document.getElementById('conversationList');
    if (convList) convList.style.display = 'block';
    document.getElementById('ticketsPanel').style.display = 'none';
    document.getElementById('chatsTabBtn').classList.add('active');
    document.getElementById('ticketsTabBtn').classList.remove('active');
    // show main chat area
    showMainChat();
}

function showTickets() {
    // Keep tabs visible but hide the conversation list and show tickets
    document.getElementById('convoPanel').style.display = 'block';
    const convList2 = document.getElementById('conversationList');
    if (convList2) convList2.style.display = 'none';
    document.getElementById('ticketsPanel').style.display = 'block';
    document.getElementById('chatsTabBtn').classList.remove('active');
    document.getElementById('ticketsTabBtn').classList.add('active');
    // ensure tickets list is refreshed
    loadEmployeeTickets();
}

function showMainChat() {
    // Show the main chat container and hide the ticket/conversation viewer
    const chatContainer = document.querySelector('.chat-container');
    const ticketBox = document.getElementById('ticketChatBox');
    const replySection = document.getElementById('ticketReplySection');
    if (chatContainer) chatContainer.style.display = 'block';
    if (ticketBox) ticketBox.style.display = 'none';
    if (replySection) replySection.style.display = 'none';
    // Clear any selected ticket so messages route to Dialogflow, not ticket replies
    currentSelectedTicket = null;
    const userInput = document.getElementById('userMessage');
    if (userInput) userInput.placeholder = 'Type your message...';
    // reset selected info header
    const sel = document.getElementById('selectedTicketInfo');
    if (sel) sel.innerHTML = '<p style="text-align: center; color: #666; margin: 0;">Select a conversation or ticket from the left to view messages</p>';
}

// Section Navigation
function showSection(id) {
    // Update URL hash to preserve tab state
    if (window.location.hash !== '#' + id) {
        isChangingSection = true;
        window.location.hash = id;
    }
    
    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Fade out current section
    const currentSection = document.querySelector('.section[style*="display: block"]');
    if (currentSection) {
        currentSection.style.opacity = '0';
        currentSection.style.transition = 'opacity 0.2s ease';
    }
    
    setTimeout(() => {
        document.querySelectorAll('.section').forEach(s => {
            s.style.display = 'none';
            s.style.opacity = '0';
        });
        
        const section = document.getElementById(id);
        if (section) {
            section.style.display = 'block';
            // Fade in new section
            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transition = 'opacity 0.3s ease';
            }, 10);
        }
    }, 200);
    
    // Update active nav item with new sidebar structure
    document.querySelectorAll('.sidebar-menu a').forEach(link => link.classList.remove('active'));
    const activeLink = document.getElementById('link-' + id);
    if (activeLink) activeLink.classList.add('active');
    
    // Update top navigation ribbon with animation
    document.querySelectorAll('.top-nav-link').forEach(link => {
        link.classList.remove('active');
        link.style.transform = 'scale(1)';
    });
    const topLink = document.getElementById('top-link-' + id);
    if (topLink) {
        topLink.classList.add('active');
        // Trigger animation
        topLink.style.transform = 'scale(1.05)';
    }
    
    // Initialize chat if chat section is opened
    if (id === 'chat') {
        setTimeout(() => {
            if (conversationPath.length === 0) {
                startGuidedFlow();
            }
            startReplyChecker();
        }, 300);
    }
}

// Chat Tab Management
function showChatTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.chat-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab and activate button
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
    
    // Load data when switching to history tab
    if (tabName === 'chat-history') {
        // Load both conversations and tickets
        loadConversations();
        loadEmployeeTickets();
    }
}

// Initialize Star Rating
function initializeStarRating() {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('ratingValue');
    const ratingDesc = document.getElementById('ratingDescription');
    const descriptions = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
    let selectedRating = 0;

    stars.forEach((star, index) => {
        const value = index + 1;

        star.addEventListener('mouseover', function() {
            stars.forEach((s, i) => s.style.color = i < value ? '#ffc107' : '#ddd');
            if (ratingDesc) ratingDesc.textContent = descriptions[index];
        });

        star.addEventListener('mouseout', function() {
            stars.forEach((s, i) => s.style.color = i < selectedRating ? '#ffc107' : '#ddd');
            if (ratingDesc) ratingDesc.textContent = selectedRating > 0 ? descriptions[selectedRating - 1] : 'Select a rating';
        });

        star.addEventListener('click', function() {
            selectedRating = value;
            if (ratingInput) ratingInput.value = value;
            stars.forEach((s, i) => s.style.color = i < value ? '#ffc107' : '#ddd');
            if (ratingDesc) ratingDesc.textContent = descriptions[index];
        });
    });
}

// Initialize Chat
function initializeChat() {
    const chatContainer = document.querySelector('.chat-container');
    // Note: top-left '➕ New Chat' button lives in the left panel now.
    // We intentionally do not create inline New/Restart buttons inside the chat area.

    // Enter key support
    document.getElementById('userMessage')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    document.getElementById('ticketReplyMessage')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendTicketReply();
    });
}

// Scroll chat to bottom
function scrollChat(chatBoxId = 'chatBox') {
    const chatBox = document.getElementById(chatBoxId);
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

// Send Message to AI
// 🎯 FIXED: Send Message to Dialogflow with proper response handling
async function sendMessage() {
    const msgInput = document.getElementById('userMessage');
    const msg = msgInput.value.trim();
    if (!msg) return;

    // Use messagesContainer for all runtime messages (keeps guidedContainer intact)
    const messagesEl = document.getElementById('messagesContainer');

    // If a ticket is selected, route the message as a ticket reply
    if (currentSelectedTicket) {
        // Add user message locally
        addMessageToChat(messagesEl, 'user', msg);
        msgInput.value = '';
        conversationPath.push({ type: 'user', message: msg });
        // Send to ticket reply endpoint using unified path
        await sendTicketReply(true, msg);
        return;
    }

    // Add user message for normal conversations
    addMessageToChat(messagesEl, 'user', msg);
    msgInput.value = '';
    conversationPath.push({ type: 'user', message: msg });

    try {
        console.log('Sending to Dialogflow:', msg);
        
        const res = await fetch('{{ url("dialogflow-webhook") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
                body: JSON.stringify({ 
                message: msg, // 🆕 FIXED: Use correct parameter name
                sessionId: currentSessionId
            })
        });

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

    const data = await res.json();
        console.log('🔍 Dialogflow RAW Response:', data);
        // Capture conversation id from server if provided (helps persistence on reload)
        if (data && data.conversation_id) {
            currentConversationId = String(data.conversation_id);
            try { localStorage.setItem('lastConversationId', String(currentConversationId)); } catch (_) {}
        }
        // Capture ticket number for persisted check/retry
        if (data && data.ticket_no) {
            lastEscalatedTicketNo = String(data.ticket_no);
            try { localStorage.setItem('lastEscalatedTicketNo', lastEscalatedTicketNo); } catch (_) {}
        } else if (data && data.response && data.response.ticket_no) {
            lastEscalatedTicketNo = String(data.response.ticket_no);
            try { localStorage.setItem('lastEscalatedTicketNo', lastEscalatedTicketNo); } catch (_) {}
        }
        // Also capture nested conversation id if the backend wraps response
        if (data && data.response && data.response.conversation_id && !currentConversationId) {
            currentConversationId = String(data.response.conversation_id);
            try { localStorage.setItem('lastConversationId', String(currentConversationId)); } catch (_) {}
        }
        // If escalated but server didn't include conversation_id, try restoring last known conversation
        if (data && data.escalated && !currentConversationId) {
            try {
                const last = localStorage.getItem('lastConversationId');
                if (last) currentConversationId = String(last);
            } catch (_) {}
        }

        // 🆕 FIXED: Handle different response formats (render into messagesContainer)
        console.log('Checking response formats:', {
            hasFulfillmentText: !!data.fulfillmentText,
            fulfillmentText: data.fulfillmentText,
            hasResponse: !!data.response,
            hasMessage: !!data.message,
            status: data.status,
            guidedFlow: data.guided_flow
        });
        
        if (data.fulfillmentText && data.fulfillmentText.trim()) {
            // Always show the notification immediately so the user sees it
            addMessageToChat(messagesEl, 'bot', data.fulfillmentText, 'normal', msg);
            conversationPath.push({ type: 'bot', message: data.fulfillmentText });
            try { localStorage.setItem('lastEscalationMessage', data.fulfillmentText); } catch (_) {}
            lastEscalationMessage = data.fulfillmentText;
            
            // 🆕 Hide guided questions if escalated
            if (data.status === 'escalated' || data.escalated) {
                const guidedContainer = document.getElementById('guidedContainer');
                if (guidedContainer) {
                    guidedContainer.style.display = 'none';
                    guidedContainer.innerHTML = '';
                }
            }
            // If guided_flow flag is set, also load guided questions
            else if (data.guided_flow || data.status === 'guided_flow') {
                await loadGuidedQuestions();
            }
        } 
        // If backend sent a wrapped response with message, render appropriately
        else if (data.response && data.response.message) {
            const r = data.response;
            // Always show the notification immediately
            addMessageToChat(messagesEl, 'bot', r.message, 'normal', msg);
            conversationPath.push({ type: 'bot', message: r.message });
            try { localStorage.setItem('lastEscalationMessage', r.message); } catch (_) {}
            lastEscalationMessage = r.message;
        }
        else if (data.response) {
            // Custom backend response format
            handleCustomResponse(data.response, messagesEl, msg);
        }
        else if (data.message && data.message.trim()) {
            // Alternative response format
            addMessageToChat(messagesEl, 'bot', data.message, 'normal', msg);
            conversationPath.push({ type: 'bot', message: data.message });
        }
        else {
            // Fallback to guided questions
            console.warn('No valid response from Dialogflow, falling back to guided questions');
            console.warn('Response data was:', JSON.stringify(data));
            await loadGuidedQuestions();
        }

        // Reload conversation list to update title with first message
        try {
            await loadConversations();
            // To prevent the just-shown  from disappearing due to immediate reload,
            // do NOT auto-reload the conversation here. The persisted message will be visible
            // the next time the user opens or switches conversations.
            // We keep the local bubble, and rely on conversation reloads initiated by the user.
        } catch (e) {
            console.warn('Could not reload conversations after message', e);
        }

    } catch (err) {
        console.error('❌ Chat error:', err);
        addMessageToChat(chatBox, 'bot', 
            "I'm having trouble connecting right now. Please try the guided questions Above or contact HR directly.", 
            'error'
        );
        await loadGuidedQuestions();
    }
}

// 🆕 NEW: Handle custom backend responses
function handleCustomResponse(response, chatBox, query = '') {
    console.log('🔄 Handling custom response:', response);
    
    const { message, ticket_no, escalated, needs_hr, message_type } = response;
    
    if (escalated && ticket_no) {
        // 🎯 FIXED: Handle ticket escalation properly
        handleTicketCreation(ticket_no, message || "Your query has been escalated to HR.");
    } 
    else if (needs_hr) {
        // Suggest escalation
        addMessageToChat(chatBox, 'bot', message, 'normal', query);
        addMessageToChat(chatBox, 'bot', 
            "Would you like me to escalate this to HR for further assistance?",
            'info'
        );
        
        // Add escalation buttons
        const escalationButtons = `
            <div class="suggestion-box">
                <div class="suggestion" onclick="escalateToHR('${message}')">✅ Yes, escalate to HR</div>
                <div class="suggestion" onclick="continueChat()">❌ No, continue chatting</div>
            </div>
        `;
        
        chatBox.innerHTML += `
            <div class="chat-row bot">
                <div class="chat-bubble">${escalationButtons}</div>
            </div>
        `;
    }
    else if (message_type === 'hr_reply') {
        // HR response in existing ticket
        addMessageToChat(chatBox, 'bot', message, 'hr-reply');
    }
    else {
        // Regular AI response
        addMessageToChat(chatBox, 'bot', message, 'normal', query);
    }
    
    conversationPath.push({ type: 'bot', message: message });
    scrollChat();
}

// 🆕 NEW: Handle ticket creation and escalation
async function handleTicketCreation(ticketNo, message) {
    const chatBox = document.getElementById('chatBox');
    const guidedContainer = document.getElementById('guidedContainer');
    
    // Hide guided questions after escalation
    if (guidedContainer) {
        guidedContainer.style.display = 'none';
        guidedContainer.innerHTML = '';
    }
    
    // Show success message
    addMessageToChat(chatBox, 'bot', 
        `✅ ${message}\n\n🎫 **Ticket Number:** ${ticketNo}`,
        'success'
    );
    
    // Add ticket management options
    const ticketActions = `
        <div class="suggestion-box">
            <div class="suggestion" onclick="viewTicket('${ticketNo}')">📋 View Ticket</div>
            <div class="suggestion" onclick="addMoreInfo('${ticketNo}')">💬 Add More Info</div>
            <div class="suggestion" onclick="continueChat()">💬 Ask Another Question</div>
        </div>
    `;
    
    chatBox.innerHTML += `
        <div class="chat-row bot">
            <div class="chat-bubble info">
                <strong>What would you like to do next?</strong>
                ${ticketActions}
            </div>
        </div>
    `;
    
    conversationPath.push({ 
        type: 'bot', 
        message: `Ticket ${ticketNo} created successfully` 
    });
    
    // Switch to tickets tab after a delay
    setTimeout(() => {
        showChatTab('chat-history');
        loadEmployeeTickets();
    }, 3000);
    
    scrollChat();
}

// 🆕 NEW: Manual escalation function
async function escalateToHR(originalMessage) {
    const chatBox = document.getElementById('chatBox');
    
    try {
        console.log('Escalating to HR:', originalMessage);
        
        const res = await fetch('{{ url("escalate-to-hr") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                message: originalMessage,
                sessionId: currentSessionId
            })
        });

        const data = await res.json();
        console.log('Escalation response:', data);

        if (data.success && data.ticket_no) {
            handleTicketCreation(data.ticket_no, data.message || "Your query has been escalated to HR.");
        } else {
            throw new Error(data.message || 'Escalation failed');
        }

    } catch (err) {
        console.error('Escalation error:', err);
        addMessageToChat(chatBox, 'bot', 
            "Sorry, I couldn't escalate your query right now. Please try again later or contact HR directly.",
            'error'
        );
    }
}

// 🆕 NEW: View specific ticket
function viewTicket(ticketNo) {
    showChatTab('chat-history');
    setTimeout(() => {
        loadTicketConversation(ticketNo);
    }, 500);
}

// 🆕 NEW: Add more information to existing ticket
async function addMoreInfo(ticketNo) {
    showChatTab('chat-history');
    
    setTimeout(async () => {
        await loadTicketConversation(ticketNo);
        
        // Focus on reply input
        const replyInput = document.getElementById('ticketReplyMessage');
        if (replyInput) {
            replyInput.focus();
            replyInput.placeholder = "Add additional information for HR...";
        }
    }, 500);
}

// 🆕 NEW: Continue chatting
function continueChat() {
    showChatTab('new-chat');
    addMessageToChat(
        document.getElementById('chatBox'), 
        'bot', 
        "What else can I help you with?",
        'info'
    );
}

// 🆕 IMPROVED: Load messages with better NLP detection
async function loadTicketMessages(ticketNo) {
    try {
        console.log('Loading messages for ticket:', ticketNo);
        const response = await fetch(`{{ url('employee/messages') }}/${ticketNo}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const messages = await response.json();
        console.log('Loaded messages:', messages);
        
        const chatBox = document.getElementById('ticketChatBox');
        chatBox.innerHTML = '';
        
        if (messages.length === 0) {
            chatBox.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages found for this ticket</p>';
            return;
        }
        
        // 🎯 Render ticket messages: include employee and HR messages (skip guided/button-only entries)
        messages.forEach(msg => {
            if (!msg || msg.is_button) return; // skip guided/button-only records

            const isEmployee = msg.sender === 'employee';
            const isHR = msg.sender === 'hr';

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-row ${isEmployee ? 'user' : 'bot'}`;

            let bubbleClass = 'chat-bubble';
            if (isHR) bubbleClass += ' hr-reply';
            if (isEmployee && (msg.message && (/Employee\s*-?\s*Follow-?up/i.test(msg.message) || msg.is_followup))) {
                bubbleClass += ' employee-followup';
            }

            // Normalize employee messages so UI shows only the user's text
            const displayMessage = normalizeEmployeeMessage(msg.message || '');

            messageDiv.innerHTML = `
                <div class="${bubbleClass}">
                    ${displayMessage}
                    <div class="message-time">
                        ${new Date(msg.created_at).toLocaleString()}${isEmployee ? ' (You)' : (isHR ? ' (HR)' : '')}
                    </div>
                </div>
            `;

            chatBox.appendChild(messageDiv);
        });
        
        scrollChat('ticketChatBox');
        
    } catch (error) {
        console.error('Error loading ticket messages:', error);
        const chatBox = document.getElementById('ticketChatBox');
        chatBox.innerHTML = `
            <div style="text-align: center; color: red; margin-top: 50px;">
                <p>Error loading messages: ${error.message}</p>
            </div>
        `;
    }
}

// 🆕 NEW: Enhanced response debugging
function debugNLPResponse(data) {
    console.group('🔍 NLP Response Debug');
    console.log('Raw response:', data);
    
    if (data.fulfillmentText) {
        console.log('✅ Dialogflow fulfillmentText:', data.fulfillmentText);
    }
    
    if (data.response) {
        console.log('✅ Custom response:', data.response);
    }
    
    if (data.queryResult) {
        console.log('✅ QueryResult:', data.queryResult);
    }
    
    if (data.intent) {
        console.log('✅ Intent:', data.intent.displayName);
    }
    
    console.log('✅ Parameters:', data.parameters);
    console.groupEnd();
}

// 🆕 NEW: Test NLP endpoints
async function testNLPIntegration() {
    console.group('🧪 Testing NLP Integration');
    
    const testMessages = [
        "I need help with my benefits",
        "How do I request time off?",
        "I want to talk to HR about a personal matter",
        "What is the vacation policy?"
    ];
    
    for (let message of testMessages) {
        console.log(`Testing: "${message}"`);
        
        try {
            const res = await fetch('{{ url("dialogflow-webhook") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    message: message,
                    sessionId: 'test-session'
                })
            });
            
            const data = await res.json();
            console.log('Response:', data);
            
        } catch (error) {
            console.error('Error:', error);
        }
        
        console.log('---');
    }
    
    console.groupEnd();
}

// loadTicketConversation is implemented later in the file to render into the single messages container

// Replace the existing sendMessage function with the fixed version above

// Add message to chat with proper formatting
function addMessageToChat(chatBox, sender, message, type = 'normal', query = '') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-row ${sender}`;
    
    let bubbleClass = 'chat-bubble';
    if (type === 'error') bubbleClass += ' error';
    else if (type === 'success') bubbleClass += ' success';
    else if (type === 'info') bubbleClass += ' info';
    else if (sender === 'bot' && message.includes('HR')) bubbleClass += ' hr-reply';
    
    const bubble = document.createElement('div');
    bubble.className = bubbleClass;
    bubble.innerHTML = `
        ${message}
        <div class="message-time">${new Date().toLocaleString()}</div>
    `;
    
    // Add flag button for bot responses (not for tickets or guided questions)
    if (sender === 'bot' && !currentSelectedTicket && query) {
        console.log('Adding flag button for query:', query);
        const flagBtn = document.createElement('button');
        flagBtn.className = 'flag-btn';
        flagBtn.title = 'Flag this response';
        flagBtn.innerHTML = '⚠️';
        flagBtn.onclick = function(e) {
            e.stopPropagation();
            console.log('Flag button clicked');
            openFlagModal(query, message);
        };
        bubble.appendChild(flagBtn);
    } else {
        console.log('No flag button:', { sender, hasTicket: !!currentSelectedTicket, hasQuery: !!query });
    }
    
    messageDiv.appendChild(bubble);
    chatBox.appendChild(messageDiv);
    
    // scroll the messages container (fallback to messagesContainer if caller passed an element without id)
    const targetId = (chatBox && chatBox.id) ? chatBox.id : 'messagesContainer';
    scrollChat(targetId);
}

// Start Guided Flow
async function startGuidedFlow() {
    const guidedContainer = document.getElementById('guidedContainer');
    if (conversationPath.length === 0) {
        guidedContainer.innerHTML = `
            <div class="chat-row bot">
                <div class="chat-bubble">
                    👋 Hello! I'm Aihra - Your AI Human Resource Assistant. 
                    <div class="message-time">How can I help you today?</div>
                </div>
            </div>`;
    }
    conversationPath = [];
    if (guidedContainer) {
        guidedContainer.style.display = 'block';
        guidedContainer.innerHTML = '';
    }
    await loadGuidedQuestions();
}

// Load Guided Questions
async function loadGuidedQuestions(parentId = null) {
    const guidedContainer = document.getElementById('guidedContainer');
    
    try {
    const url = parentId ? `{{ url('guided') }}/${parentId}` : `{{ url('guided') }}`;
        console.log('🔍 Loading guided questions from:', url);
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        
        console.log('📡 Response status:', res.status, res.statusText);

        // Check for authentication errors
        if (res.status === 401 || res.status === 419) {
            console.warn('Session expired, redirecting to login');
            window.location.href = '{{ route("login") }}';
            return;
        }

        // Handle 404 (no guided questions) gracefully - show simple prompt
        if (res.status === 404) {
            console.log('No guided questions in database, showing simple prompt');
            guidedContainer.innerHTML = `
                <div class="chat-row bot">
                    <div class="chat-bubble">
                        <strong>How can I help you today?</strong>
                        <div style="margin-top: 10px; font-size: 14px; color: #666;">
                            💬 Type your question below and I'll do my best to assist you!
                        </div>
                    </div>
                </div>`;
            return;
        }

        if (!res.ok) {
            const errorText = await res.text();
            console.error('Server error response:', errorText);
            throw new Error(`Server error: ${res.status} - ${errorText.substring(0, 200)}`);
        }
        
        const data = await res.json();
        console.log('✅ Guided questions data:', data);

        // Handle empty or error type response (e.g., no questions configured)
        if (data.type === 'error' || data.type === 'empty') {
            console.log('Guided questions not available, showing simple prompt');
            guidedContainer.innerHTML = `
                <div class="chat-row bot">
                    <div class="chat-bubble">
                        <strong>How can I help you today?</strong>
                        <div style="margin-top: 10px; font-size: 14px; color: #666;">
                            💬 Type your question below and I'll do my best to assist you!
                        </div>
                    </div>
                </div>`;
            return;
        }
        if (data.type === 'final' || data.type === 'escalate') {
            // Final answer: render into the main messages area (not the guided box),
            // then ask if user needs more help
            const answer = data.data?.[0]?.answer || data.answer || "Thank you for your question!";
            const question = data.data?.[0]?.question_text || 'guided question';
            const messagesEl = document.getElementById('messagesContainer');
            if (messagesEl) addMessageToChat(messagesEl, 'bot', answer, 'normal', question);

            // Ask if they need more help with Yes/No options
            guidedContainer.innerHTML = `
                <div class="chat-row bot">
                    <div class="chat-bubble">
                        <strong>Is there anything else I can help you with?</strong>
                        <div class="suggestion-box">
                            <div class="suggestion" onclick="handleMoreHelpResponse('yes')">✅ Yes, please</div>
                            <div class="suggestion" onclick="handleMoreHelpResponse('no')">❌ No, thanks</div>
                        </div>
                    </div>
                </div>`;
            guidedContainer.style.display = 'block';
            return;
        }

        if (!data.data || data.data.length === 0) {
            // No guided questions available - show friendly prompt to use Dialogflow
            guidedContainer.innerHTML = `
                <div class="chat-row bot">
                    <div class="chat-bubble">
                        <strong>How can I help you today?</strong>
                        <div style="margin-top: 10px; font-size: 14px; color: #666;">
                            💬 Type your question below, or try one of these topics:
                        </div>
                        <div class="suggestion-box" style="margin-top: 10px;">
                            <div class="suggestion" onclick="sendQuick('What are the employment requirements?')">Employment</div>
                            <div class="suggestion" onclick="sendQuick('Tell me about employee benefits')">Benefits</div>
                            <div class="suggestion" onclick="sendQuick('How do promotions work?')">Promotion</div>
                            <div class="suggestion" onclick="sendQuick('What training programs are available?')">Training</div>
                        </div>
                    </div>
                </div>`;
            return;
        }

        const options = data.data.map(q => `
            <div class="suggestion" onclick="handleQuestionClick(${q.gq_id}, '${escapeHtml(q.question_text)}')">
                ${q.question_text}
            </div>`).join('');

        // Replace guided options (do not append) so older questions are not shown during the flow
        guidedContainer.innerHTML = `
            <div class="chat-row bot">
                <div class="chat-bubble">
                    <strong>${data.message || 'Please choose a topic:'}</strong>
                    <div class="suggestion-box">${options}</div>
                </div>
            </div>`;
        
        // keep messages scrolled to bottom
        scrollChat('messagesContainer');
        
    } catch (error) {
        console.error('Error loading questions:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            parentId: parentId,
            url: parentId ? `{{ url('guided') }}/${parentId}` : `{{ url('guided') }}`
        });
        
        // Show friendly Dialogflow-first UI instead of error
        guidedContainer.innerHTML = `
            <div class="chat-row bot">
                <div class="chat-bubble">
                    <strong>How can I help you today?</strong>
                    <div style="margin-top: 10px; font-size: 14px; color: #666;">
                        💬 Type your question below, or try one of these topics:
                    </div>
                    <div class="suggestion-box" style="margin-top: 10px;">
                        <div class="suggestion" onclick="sendQuick('What are the employment requirements?')">Employment</div>
                        <div class="suggestion" onclick="sendQuick('Tell me about employee benefits')">Benefits</div>
                        <div class="suggestion" onclick="sendQuick('How do promotions work?')">Promotion</div>
                        <div class="suggestion" onclick="sendQuick('What training programs are available?')">Training</div>
                    </div>
                </div>
            </div>`;
        scrollChat('messagesContainer');
    }
}

// Handle Question Click
// Handle user response to "Need more help?" after T3 answer
function handleMoreHelpResponse(response) {
    const messagesEl = document.getElementById('messagesContainer');
    const guidedContainer = document.getElementById('guidedContainer');
    
    if (response === 'yes') {
        // User wants more help - restart guided flow from T1
        addMessageToChat(messagesEl, 'user', 'Yes, please');
        conversationPath.push({ type: 'user', message: 'Yes, please' });
        
        addMessageToChat(messagesEl, 'bot', 'Great! Let me help you with another question.');
        conversationPath.push({ type: 'bot', message: 'Great! Let me help you with another question.' });
        
        // Clear guided container and restart from Level 1
        if (guidedContainer) guidedContainer.innerHTML = '';
        loadGuidedQuestions(); // Load T1 questions
    } else {
        // User is done
        addMessageToChat(messagesEl, 'user', 'No, thanks');
        conversationPath.push({ type: 'user', message: 'No, thanks' });
        
        addMessageToChat(messagesEl, 'bot', 'Thank you for using AIHRA! Feel free to start a new chat anytime.');
        conversationPath.push({ type: 'bot', message: 'Thank you for using AIHRA! Feel free to start a new chat anytime.' });
        
        // Clear guided container
        if (guidedContainer) {
            guidedContainer.innerHTML = '';
            guidedContainer.style.display = 'none';
        }
    }
}

async function handleQuestionClick(id, text) {
    const messagesEl = document.getElementById('messagesContainer');
    addMessageToChat(messagesEl, 'user', text);
    conversationPath.push({ type: 'user', message: text });

    try {
        // First query the guided endpoint to see if this selection has children
        const guidedRes = await fetch(`{{ url('guided') }}/${id}`, { headers: { 'Accept': 'application/json' } });
        
        // Check for authentication errors
        if (guidedRes.status === 401 || guidedRes.status === 419) {
            console.warn('Session expired, redirecting to login');
            window.location.href = '{{ route("login") }}';
            return;
        }
        
        if (!guidedRes.ok) throw new Error('Failed to load guided question');
        const guidedData = await guidedRes.json();

        // If there are children (type like 'level2'/'level1'), render them
        if (guidedData.type && guidedData.type.startsWith('level') && guidedData.type !== 'final') {
            // Reuse existing renderer to show next-level options
            await loadGuidedQuestions(id);
            return;
        }

        // If this is a final node (LEVEL 3) or an escalate/final response, send the selected text
        // to the dialogflow webhook so the backend will persist both the user's selection and the bot reply.
        const dfRes = await fetch('{{ url("dialogflow-webhook") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text, sessionId: currentSessionId })
        });

        if (!dfRes.ok) {
            // Fallback: show the answer from guidedData if provided
            if (guidedData.type === 'final' && guidedData.data && guidedData.data[0] && guidedData.data[0].answer) {
                addMessageToChat(messagesEl, 'bot', guidedData.data[0].answer, 'normal', text);
                conversationPath.push({ type: 'bot', message: guidedData.data[0].answer });
            }
            return;
        }

        const dfData = await dfRes.json();

        // Reload conversation list to update title with first message
        try {
            await loadConversations();
        } catch (e) {
            console.warn('Could not reload conversations after message', e);
        }

        // Display Dialogflow/backend reply (DialogflowController persists messages server-side)
        if (dfData.fulfillmentText) {
            addMessageToChat(messagesEl, 'bot', dfData.fulfillmentText, 'normal', text);
            conversationPath.push({ type: 'bot', message: dfData.fulfillmentText });
            
            // Only show "Need more help?" if this is a successful response (not error/retry/escalation)
            const isErrorResponse = dfData.status === 'retry' || dfData.status === 'offer_escalation' || dfData.fallback === true || dfData.max_retries_reached === true;
            
            if (!isErrorResponse) {
                const guidedContainer = document.getElementById('guidedContainer');
                if (guidedContainer) {
                    guidedContainer.innerHTML = `
                        <div class="chat-row bot">
                            <div class="chat-bubble">
                                <strong>Is there anything else I can help you with?</strong>
                                <div class="suggestion-box">
                                    <div class="suggestion" onclick="handleMoreHelpResponse('yes')">✅ Yes, please</div>
                                    <div class="suggestion" onclick="handleMoreHelpResponse('no')">❌ No, thanks</div>
                                </div>
                            </div>
                        </div>`;
                    guidedContainer.style.display = 'block';
                }
            }
        } else if (dfData.response) {
            handleCustomResponse(dfData.response, messagesEl, text);
        } else if (dfData.message) {
            addMessageToChat(messagesEl, 'bot', dfData.message, 'normal', text);
            conversationPath.push({ type: 'bot', message: dfData.message });
            
            // Only show "Need more help?" if this is a successful response (not error/retry/escalation)
            const isErrorResponse = dfData.status === 'retry' || dfData.status === 'offer_escalation' || dfData.fallback === true || dfData.max_retries_reached === true;
            
            if (!isErrorResponse) {
                const guidedContainer = document.getElementById('guidedContainer');
                if (guidedContainer) {
                    guidedContainer.innerHTML = `
                        <div class="chat-row bot">
                            <div class="chat-bubble">
                                <strong>Is there anything else I can help you with?</strong>
                                <div class="suggestion-box">
                                    <div class="suggestion" onclick="handleMoreHelpResponse('yes')">✅ Yes, please</div>
                                    <div class="suggestion" onclick="handleMoreHelpResponse('no')">❌ No, thanks</div>
                                </div>
                            </div>
                        </div>`;
                    guidedContainer.style.display = 'block';
                }
            }
        } else if (guidedData.type === 'final' && guidedData.data && guidedData.data[0] && guidedData.data[0].answer) {
            // Last-resort: show guided answer in messages and ask if they need more help
            addMessageToChat(messagesEl, 'bot', guidedData.data[0].answer);
            conversationPath.push({ type: 'bot', message: guidedData.data[0].answer });
            const guidedContainer = document.getElementById('guidedContainer');
            if (guidedContainer) {
                guidedContainer.innerHTML = `
                    <div class="chat-row bot">
                        <div class="chat-bubble">
                            <strong>Is there anything else I can help you with?</strong>
                            <div class="suggestion-box">
                                <div class="suggestion" onclick="handleMoreHelpResponse('yes')">✅ Yes, please</div>
                                <div class="suggestion" onclick="handleMoreHelpResponse('no')">❌ No, thanks</div>
                            </div>
                        </div>
                    </div>`;
                guidedContainer.style.display = 'block';
            }
        }

    } catch (err) {
        console.error('Error handling question click:', err);
        // Don't show "Need more help?" on errors - just reload guided questions
        // Clear any error prompts from guided container
        const guidedContainer = document.getElementById('guidedContainer');
        if (guidedContainer) {
            guidedContainer.innerHTML = '';
        }
        // Fallback: still try to load guided children/UI
        await loadGuidedQuestions(id);
    }
}

// Load Employee Tickets
async function loadEmployeeTickets() {
    try {
        const ticketList = document.getElementById('ticketList');
        ticketList.innerHTML = '<p class="loading">Loading your tickets</p>';
        
        const response = await fetch('{{ route("employee.tickets") }}');
        const tickets = await response.json();
        
        if (tickets.length === 0) {
            ticketList.innerHTML = '<p style="text-align: center; color: #666;">No support tickets found.</p>';
            return;
        }
        
        let ticketsHtml = '';
        tickets.forEach(ticket => {
            const statusClass = ticket.status ? ticket.status.toLowerCase().replace(' ', '-') : 'open';
            const shortMessage = ticket.message ? 
                (ticket.message.length > 50 ? ticket.message.substring(0, 50) + '...' : ticket.message) : 
                'No message';
                
            ticketsHtml += `
                <div class="ticket-item-history" id="history-ticket-${ticket.ticket_no}" onclick="loadTicketConversation('${ticket.ticket_no}')">
                    <strong>🎫 ${ticket.ticket_no}</strong>
                    <div class="ticket-meta-history">
                        <span class="ticket-badge ${statusClass}">${ticket.status || 'Open'}</span>
                        <span>${new Date(ticket.created_at).toLocaleDateString()}</span>
                        <div style="margin-top: 5px; font-size: 13px; color: #555;">${shortMessage}</div>
                    </div>
                </div>
            `;
        });
        
        ticketList.innerHTML = ticketsHtml;
        
    } catch (error) {
        console.error('Error loading tickets:', error);
        document.getElementById('ticketList').innerHTML = '<p style="color: red; text-align: center;">Error loading tickets</p>';
    }
}

// Load Chat Conversations
async function loadConversations() {
    try {
        const list = document.getElementById('conversationList');
        list.innerHTML = '<p class="loading">Loading your conversations</p>';

        const response = await fetch('{{ route("employee.conversations") }}');
        const convos = await response.json();

        if (!convos || convos.length === 0) {
            list.innerHTML = '<p style="text-align: center; color: #666;">No conversations found.</p>';
            return;
        }

        let html = '';
        convos.forEach(c => {
            const title = c.title ? c.title : (c.first_message ? (new Date(c.created_at).toLocaleDateString() + ' - ' + c.first_message.substring(0,60) + '...') : 'Conversation');
            const preview = c.latest_message ? c.latest_message : (c.first_message || 'No message');
                html += `
                    <div class="ticket-item-history" id="history-convo-${c.id}" data-session="${c.session_id || ''}">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div style="flex:1; cursor:pointer;" onclick="viewConversation('${c.id}')">
                                <strong>💬 ${escapeHtml(title)}</strong>
                                <div class="ticket-meta-history">
                                    <span>${new Date(c.created_at).toLocaleDateString()}</span>
                                    <div style="margin-top: 5px; font-size: 13px; color: #555;">${escapeHtml(preview.length > 80 ? preview.substring(0,80) + '...' : preview)}</div>
                                </div>
                            </div>
                            <div style="margin-left:10px;">
                                <button class="convo-delete" onclick="deleteConversation('${c.id}'); event.stopPropagation();" title="Delete conversation" style="background:transparent;border:none;color:#d9534f;cursor:pointer;font-size:16px;">🗑️</button>
                            </div>
                        </div>
                    </div>
                `;
        });

        list.innerHTML = html;
        // Remove any stray close/delete buttons that may remain from older UI versions
        if (typeof sanitizeConversationButtons === 'function') sanitizeConversationButtons();
    } catch (err) {
        console.error('Error loading conversations:', err);
        document.getElementById('conversationList').innerHTML = '<p style="color: red; text-align: center;">Error loading conversations</p>';
    }
}

// View specific conversation messages
async function viewConversation(convoId) {
    try {
        // Ensure we are in chat context, not ticket reply context
        currentSelectedTicket = null;
        const msgInput = document.getElementById('userMessage');
        if (msgInput) msgInput.placeholder = 'Type your message...';
        // Keep main chat visible and render conversation messages into messagesContainer
        const chatContainer = document.querySelector('.chat-container');
        if (chatContainer) chatContainer.style.display = 'block';

        // mark active UI
        document.querySelectorAll('#conversationList .ticket-item-history').forEach(item => item.classList.remove('active'));
        const el = document.getElementById(`history-convo-${convoId}`);
        if (el) el.classList.add('active');

        // If this conversation DOM element contains a data-session attribute, use it so
        // messages sent now will be attached to the same conversation/session server-side.
        if (el && el.dataset && el.dataset.session) {
            currentSessionId = el.dataset.session;
        }
        console.debug('viewConversation', { convoId: convoId, session: currentSessionId });
        currentConversationId = convoId;
        try { localStorage.setItem('lastConversationId', String(convoId)); } catch (_) {}

        document.getElementById('selectedTicketInfo').innerHTML = `<strong>💬 Conversation</strong> - <span id="ticketStatus"></span>`;

        const messagesEl = document.getElementById('messagesContainer');
        messagesEl.innerHTML = '<p class="loading">Loading conversation...</p>';

        const response = await fetch(`{{ url('employee/conversations') }}/${convoId}`);
        
        if (!response.ok) {
            console.error('Failed to load conversation:', response.status, response.statusText);
            messagesEl.innerHTML = '<p style="text-align: center; color: #ff6b6b; margin-top: 50px;">❌ Failed to load conversation messages</p>';
            return;
        }
        
        const messages = await response.json();

        if (!messages || messages.length === 0) {
            messagesEl.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages in this conversation</p>';
            return;
        }

        messagesEl.innerHTML = '';
        let foundTicketMessage = false;
        messages.forEach(msg => {
            if (!msg || msg.is_button) return; // skip guided/button-only entries

                const isEmployee = msg.sender === 'employee';
                const isHR = msg.sender === 'hr';
                const isBot = msg.sender === 'bot';

                // If this message represents guided options (stored as is_button), render into guidedContainer
                if (msg.is_button) {
                    const guidedContainer = document.getElementById('guidedContainer');
                    if (guidedContainer) {
                        guidedContainer.style.display = 'block';
                        // Render the guided entry (user choices or bot suggestions) into guided area
                        const who = isEmployee ? 'user' : 'bot';
                        const display = normalizeEmployeeMessage(msg.message || '');
                        addMessageToChat(guidedContainer, who, display);
                    }
                    return; // skip adding to main messages area
                }

                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-row ${isEmployee ? 'user' : 'bot'}`;

                let bubbleClass = 'chat-bubble';
                if (isHR) bubbleClass += ' hr-reply';
                if (isEmployee && msg.message && /Employee\s*-?\s*Follow-?up/i.test(msg.message)) bubbleClass += ' employee-followup';

                // Strip any stored prefix for employee follow-ups
                const displayMessage = normalizeEmployeeMessage(msg.message || '');
                const senderLabel = isEmployee ? ' (You)' : (isHR ? ' (HR)' : '');

                const bubble = document.createElement('div');
                bubble.className = bubbleClass;
                bubble.innerHTML = `
                    ${displayMessage}
                    <div class="message-time">${new Date(msg.created_at).toLocaleString()}${senderLabel}</div>
                `;

                // Detect if this message contains the last escalated ticket number
                try {
                    const lastTicket = lastEscalatedTicketNo || localStorage.getItem('lastEscalatedTicketNo');
                    if (!foundTicketMessage && lastTicket && (displayMessage.includes(lastTicket))) {
                        foundTicketMessage = true;
                    }
                } catch (_) {}

                // Add flag button for bot messages
                if (isBot && msg.query) {
                    const flagBtn = document.createElement('button');
                    flagBtn.className = 'flag-btn';
                    flagBtn.title = 'Flag this response';
                    // Inline SVG for consistent yellow exclamation icon
                    flagBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 2L1 21h22L12 2z" fill="#FFC107" stroke="#B8860B" stroke-width="0.5"/>
                            <rect x="11" y="8" width="2" height="6" fill="#3F2F00"/>
                            <circle cx="12" cy="17" r="1.5" fill="#3F2F00"/>
                        </svg>`;
                    flagBtn.style.padding = '0';
                    flagBtn.style.background = 'transparent';
                    flagBtn.style.border = 'none';
                    flagBtn.onclick = function(e) {
                        e.stopPropagation();
                        openFlagModal(msg.query, msg.message);
                    };
                    bubble.appendChild(flagBtn);
                }

                messageDiv.appendChild(bubble);
                messagesEl.appendChild(messageDiv);
        });

        // If we didn't find the persisted ticket message yet and this is the last active conversation,
        // re-display the last escalation message from localStorage to keep it visible.
        try {
            const lastId = localStorage.getItem('lastConversationId');
            const lastMsg = lastEscalationMessage || localStorage.getItem('lastEscalationMessage');
            const lastTicket = lastEscalatedTicketNo || localStorage.getItem('lastEscalatedTicketNo');
            if (!foundTicketMessage && lastMsg && lastId && String(lastId) === String(convoId)) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-row bot';
                const bubble = document.createElement('div');
                bubble.className = 'chat-bubble';
                bubble.innerHTML = `
                    ${lastMsg}
                    <div class="message-time">${new Date().toLocaleString()} (Pending sync)</div>
                `;
                messageDiv.appendChild(bubble);
                messagesEl.appendChild(messageDiv);
            }
        } catch (_) {}

        scrollChat('messagesContainer');

    } catch (err) {
        console.error('Error loading conversation messages:', err);
        document.getElementById('ticketChatBox').innerHTML = '<p style="color: red; text-align: center;">Error loading conversation messages</p>';
    }
}

// Delete a conversation
async function deleteConversation(convoId) {
    if (!confirm('Are you sure you want to remove this conversation from history? This cannot be undone.')) return;

    try {
        const res = await fetch(`{{ url('employee/conversations') }}/${convoId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showNotification('🗑️ Conversation removed');
            // Refresh list
            await loadConversations();
            // If the deleted conversation was the last active, clear it
            try {
                const last = localStorage.getItem('lastConversationId');
                if (last && String(last) === String(convoId)) {
                    localStorage.removeItem('lastConversationId');
                    if (currentConversationId && String(currentConversationId) === String(convoId)) {
                        currentConversationId = null;
                    }
                }
            } catch (_) {}

            // If the deleted conversation is currently displayed, clear it
            const chatBox = document.getElementById('ticketChatBox');
            if (chatBox && document.getElementById(`history-convo-${convoId}`) === null) {
                chatBox.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">Conversation removed.</p>';
            }
        } else {
            alert('Failed to delete conversation: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        console.error('Error deleting conversation:', err);
        alert('Failed to delete conversation');
    }
}

// Load Ticket Conversation (renders into messagesContainer so guided questions remain visible)
async function loadTicketConversation(ticketNo) {
    try {
        currentSelectedTicket = ticketNo;

        // Update UI quickly (don't hide main chat). Show loading immediately in messages container
        document.querySelectorAll('.ticket-item-history').forEach(item => item.classList.remove('active'));
        const ticketElement = document.getElementById(`history-ticket-${ticketNo}`);
        if (ticketElement) ticketElement.classList.add('active');

        document.getElementById('selectedTicketInfo').innerHTML = `
            <strong>🎫 Ticket: ${ticketNo}</strong> 
            - <span id="ticketStatus">Loading...</span>
        `;

        // hide guided suggestions for ticket view — tickets are for HR/employee back-and-forth only
        const guidedContainer = document.getElementById('guidedContainer');
        if (guidedContainer) {
            guidedContainer.innerHTML = '';
            guidedContainer.style.display = 'none';
        }

        // set main input placeholder to indicate ticket reply
        const userInput = document.getElementById('userMessage');
        if (userInput) {
            userInput.placeholder = 'Type your reply to HR...';
            userInput.focus();
        }

        // Load messages into messagesContainer immediately
        const response = await fetch(`{{ url('employee/messages') }}/${ticketNo}`);
        const messages = await response.json();
        const messagesEl = document.getElementById('messagesContainer');
        messagesEl.innerHTML = '';

        if (!messages || messages.length === 0) {
            messagesEl.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages in this ticket</p>';
            return;
        }

        // Display messages (tickets include employee and HR replies)
        messages.forEach(msg => {
            if (!msg || msg.is_button) return; // skip guided/button-only entries

            const isEmployee = msg.sender === 'employee';
            const isHR = msg.sender === 'hr';

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-row ${isEmployee ? 'user' : 'bot'}`;

            let bubbleClass = 'chat-bubble';
            if (isHR) bubbleClass += ' hr-reply';
            else if (isEmployee && msg.message && /Employee\s*-?\s*Follow-?up/i.test(msg.message)) bubbleClass += ' employee-followup';

            const displayMessage = normalizeEmployeeMessage(msg.message || '');

            messageDiv.innerHTML = `
                <div class="${bubbleClass}">
                    ${displayMessage}
                    <div class="message-time">
                        ${new Date(msg.created_at).toLocaleString()}${isEmployee ? ' (You)' : (isHR ? ' (HR)' : '')}
                        ${msg.message && /Employee\s*-?\s*Follow-?up/i.test(msg.message) ? ' (Follow-up)' : ''}
                    </div>
                </div>
            `;
            messagesEl.appendChild(messageDiv);
        });

        // Load ticket status
        try {
            const statusResponse = await fetch(`{{ url('employee/ticket-status') }}/${ticketNo}`);
            if (statusResponse.ok) {
                const statusData = await statusResponse.json();
                document.getElementById('ticketStatus').innerHTML = 
                    `<span class="ticket-badge ${statusData.status.toLowerCase()}">${statusData.status}</span>`;
                
                // 🆕 Disable input for resolved tickets
                const userInput = document.getElementById('userMessage');
                const sendBtn = document.querySelector('.send-btn');
                const resolvedNotice = document.getElementById('ticketResolvedNotice');
                
                if (statusData.status === 'Resolved') {
                    if (userInput) {
                        userInput.disabled = true;
                        userInput.placeholder = '🔒 This ticket has been resolved. No further replies allowed.';
                    }
                    if (sendBtn) sendBtn.disabled = true;
                    
                    // Add resolved notice if not already present
                    if (!resolvedNotice) {
                        const notice = document.createElement('div');
                        notice.id = 'ticketResolvedNotice';
                        notice.style.cssText = 'text-align: center; padding: 10px; background: #f8d7da; color: #721c24; border-radius: 8px; margin: 10px 0;';
                        notice.innerHTML = '🔒 This ticket has been resolved. No further replies are allowed.';
                        const inputContainer = document.querySelector('.input-container');
                        if (inputContainer) inputContainer.parentNode.insertBefore(notice, inputContainer);
                    }
                } else {
                    if (userInput) {
                        userInput.disabled = false;
                        userInput.placeholder = 'Type your reply to HR...';
                    }
                    if (sendBtn) sendBtn.disabled = false;
                    if (resolvedNotice) resolvedNotice.remove();
                }
            }
        } catch (statusError) {
            console.error('Error loading status:', statusError);
        }

        scrollChat('messagesContainer');

    } catch (error) {
        console.error('Error loading conversation:', error);
        const messagesEl = document.getElementById('messagesContainer');
        messagesEl.innerHTML = `
            <div style="text-align: center; color: red; margin-top: 50px;">
                <p>Error loading conversation</p>
            </div>
        `;
    }
}

// Send Ticket Reply (unified: reads from main input when used from sendMessage)
async function sendTicketReply(fromSendMessage = false, passedMessage = null) {
    if (!currentSelectedTicket) {
        alert('Please select a ticket first');
        return;
    }

    // 🆕 Check if ticket is resolved before sending
    const ticketStatusEl = document.getElementById('ticketStatus');
    if (ticketStatusEl && ticketStatusEl.textContent.toLowerCase().includes('resolved')) {
        alert('This ticket has been resolved. No further replies are allowed.');
        return;
    }

    // Prevent duplicate sends
    if (sendingTicket) {
        console.warn('sendTicketReply called while a send is already in progress');
        return;
    }

    // Determine message source: passedMessage (priority), ticketReplyMessage (legacy), or main input
    let message = '';
    if (fromSendMessage && passedMessage) {
        message = passedMessage;
    } else {
        const legacy = document.getElementById('ticketReplyMessage');
        if (legacy && legacy.value && legacy.value.trim() !== '') {
            message = legacy.value.trim();
        } else {
            const main = document.getElementById('userMessage');
            if (main && main.value && main.value.trim() !== '') {
                message = main.value.trim();
            }
        }
    }

    if (!message) {
        alert('Please enter a message');
        return;
    }

    try {
        sendingTicket = true;

        const response = await fetch('{{ route("employee.reply-to-ticket") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ticket_no: currentSelectedTicket,
                message: message
            })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Clear inputs
            const legacy = document.getElementById('ticketReplyMessage');
            if (legacy) legacy.value = '';
            const main = document.getElementById('userMessage');
            if (main) main.value = '';

            // After successful send, refresh ticket UI from server (no optimistic append to avoid duplicates)
            document.getElementById('ticketStatus').innerHTML = 
                '<span class="ticket-badge replied">Waiting for HR</span>';
            showNotification('✅ Your reply has been sent to HR!');

        } else {
            alert('Failed to send reply: ' + (result.message || 'Unknown error'));
        }

    } catch (error) {
        console.error('Error sending reply:', error);
        alert('Failed to send reply. Please try again.');
    } finally {
        sendingTicket = false;
        // Refresh the ticket conversation from the server to ensure the UI matches persisted state
        try {
            if (currentSelectedTicket) await loadTicketConversation(currentSelectedTicket);
        } catch (e) {
            console.warn('Could not reload ticket after send', e);
        }
    }
}

// Handle Escalation Success
function handleEscalationSuccess(ticketNo, fulfillmentText) {
    const chatBox = document.getElementById('chatBox');
    const message = fulfillmentText || "✅ I've escalated your query to our HR team. They'll get back to you soon.";
    
    chatBox.innerHTML += `
        <div class="chat-row bot">
            <div class="chat-bubble success">
                ${message}
            </div>
        </div>
        <div class="chat-row bot">
            <div class="chat-bubble info">
                📋 <strong>Ticket Number:</strong> ${ticketNo}
                <div class="message-time">Keep this number for reference</div>
            </div>
        </div>
    `;
    
    conversationPath.push({ type: 'bot', message: message });
    
    // Switch to history tab
    setTimeout(() => {
        showChatTab('chat-history');
        loadEmployeeTickets();
    }, 2000);
    
    scrollChat();
}

// Utility Functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Normalize employee messages by stripping any stored "Employee Follow-up" prefixes
function normalizeEmployeeMessage(text) {
    if (!text) return '';
    return text.replace(/^Employee\s*-?\s*Follow-?up:?:?\s*/i, '');
}

function sendQuick(message) {
    document.getElementById('userMessage').value = message;
    sendMessage();
}

function restartChat() {
    const messagesEl = document.getElementById('messagesContainer');
    const guidedContainer = document.getElementById('guidedContainer');
    if (messagesEl) messagesEl.innerHTML = '';
    if (guidedContainer) guidedContainer.innerHTML = '';
    conversationPath = [];
    startGuidedFlow();
}

// Flag Modal Functions
let currentFlagData = { query: '', response: '' };

function openFlagModal(query, response) {
    currentFlagData = { query, response };
    document.getElementById('flaggedQuery').value = query;
    document.getElementById('flaggedResponse').value = response;
    document.getElementById('flagModal').style.display = 'flex';
    
    // Clear any previous selection
    document.querySelectorAll('.flag-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    document.getElementById('selectedReason').value = '';
}

function closeFlagModal() {
    document.getElementById('flagModal').style.display = 'none';
    currentFlagData = { query: '', response: '' };
}

function selectFlagReason(reason, element) {
    // Clear all selections
    document.querySelectorAll('.flag-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Select this one
    element.classList.add('selected');
    document.getElementById('selectedReason').value = reason;
}

async function submitFlag() {
    const reason = document.getElementById('selectedReason').value;
    
    if (!reason) {
        alert('Please select a reason for flagging this response.');
        return;
    }
    
    console.log('Submitting flag with data:', {
        user_query: currentFlagData.query,
        bot_response: currentFlagData.response,
        reason: reason
    });
    
    try {
        const response = await fetch('{{ route("flag.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_query: currentFlagData.query,
                bot_response: currentFlagData.response,
                reason: reason
            })
        });
        
        console.log('Flag response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Flag submission error:', errorText);
            alert('Failed to flag response: ' + response.status + ' - ' + errorText.substring(0, 100));
            return;
        }
        
        const data = await response.json();
        console.log('Flag response data:', data);
        
        if (data.success) {
            closeFlagModal();
            
            // Show success notification
            const notification = document.createElement('div');
            notification.style.cssText = 'position: fixed; top: 20px; right: 20px; background: var(--success); color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 3000;';
            notification.innerHTML = '✅ Response flagged successfully. Thank you for your feedback!';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        } else {
            console.error('Flag submission failed:', data);
            alert('Failed to flag response: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error flagging response:', error);
        alert('An error occurred: ' + error.message);
    }
}

// Remove any close/delete controls left over from previous UI iterations.
// This ensures the user-requested "no close button for chats" is enforced client-side
// even if some older markup slips into the list.
function sanitizeConversationButtons() {
    try {
        // Common selectors used in older versions — remove them if present.
        // NOTE: preserve our conversation delete buttons which use class 'convo-delete'.
        document.querySelectorAll('.conversation-close, .convo-close, .delete-btn, .close-btn').forEach(el => el.remove());

        // Also hide any inline buttons/links that have obvious labels, but skip our convo-delete controls
        document.querySelectorAll('#conversationList button, #conversationList a').forEach(el => {
            if (el.classList && el.classList.contains('convo-delete')) return; // keep our delete control
            const txt = (el.textContent || '').toLowerCase();
            if (txt.includes('delete') || txt.includes('close') || txt.includes('remove')) el.remove();
        });
    } catch (e) {
        console.warn('sanitizeConversationButtons failed', e);
    }
}

// Start a brand new conversation (server creates a new conversation/session)
async function startNewConversation() {
    try {
        // Ensure chats panel is visible when creating a new conversation
        showChats();
        const res = await fetch('{{ route("employee.conversations.start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        });

        const data = await res.json();
        console.debug('startNewConversation response', data);
        if (res.ok && data.success) {
            currentSessionId = data.session_id;
            currentConversationId = data.id;
            try { localStorage.setItem('lastConversationId', String(currentConversationId)); } catch (_) {}

            // Clear containers and start the guided flow reliably
            // Ensure main chat area is visible
            showMainChat();
            const messagesEl = document.getElementById('messagesContainer');
            const guidedContainer = document.getElementById('guidedContainer');
            if (messagesEl) messagesEl.innerHTML = '';
            if (guidedContainer) {
                guidedContainer.style.display = 'block';
                guidedContainer.innerHTML = '';
            }
            conversationPath = [];
            // Start guided flow (shows greeting and loads first-level questions)
            await startGuidedFlow();
            const userInput = document.getElementById('userMessage');
            if (userInput) userInput.focus();

            // Insert the new conversation into the list immediately (avoids timing issues
            // where reloading the whole list may not yet include the new item).
            try {
                const list = document.getElementById('conversationList');
                if (list) {
                    const title = data.title ? escapeHtml(data.title) : (data.first_message ? escapeHtml((data.first_message.length > 80 ? data.first_message.substring(0,80) + '...' : data.first_message)) : 'Conversation');
                    const firstMsg = data.first_message ? escapeHtml(data.first_message) : '';
                    const newHtml = `\n                        <div class="ticket-item-history" id="history-convo-${data.id}" data-session="${data.session_id || ''}">\n                            <div style="display:flex; justify-content:space-between; align-items:center;">\n                                <div style="flex:1; cursor:pointer;" onclick="viewConversation('${data.id}')">\n                                    <strong>💬 ${title}</strong>\n                                    <div class="ticket-meta-history">\n                                        <span>${new Date().toLocaleDateString()}</span>\n                                        <div style="margin-top: 5px; font-size: 13px; color: #555;">${firstMsg}</div>\n                                    </div>\n                                </div>\n                                <div style="margin-left:10px;">\n                                    <button class=\"convo-delete\" onclick=\"deleteConversation('${data.id}'); event.stopPropagation();\" title=\"Delete conversation\" style=\"background:transparent;border:none;color:#d9534f;cursor:pointer;font-size:16px;\">🗑️</button>\n                                </div>\n                            </div>\n                        </div>`;

                    // prepend new convo so users see it at the top
                    list.insertAdjacentHTML('afterbegin', newHtml);

                    // Remove active class from other conversations and trigger view
                    document.querySelectorAll('#conversationList .ticket-item-history').forEach(item => item.classList.remove('active'));
                    const newEl = document.getElementById(`history-convo-${data.id}`);
                    if (newEl) {
                        newEl.classList.add('active');
                        // Do not auto-open the new conversation viewer (which hides main chat).
                        // Keep main chat visible so guided questions appear for the new session.
                        showChats();
                        showMainChat();
                        console.debug('Inserted new conversation element', { id: data.id, session: newEl.dataset.session || null });
                    }
                }
            } catch (e) {
                console.warn('Could not insert/open new conversation directly', e);
                try { await loadConversations(); } catch (err) { console.warn('Could not reload conversations', err); }
            }
        } else {
            alert('Failed to start a new conversation');
        }
    } catch (err) {
        console.error('Error starting conversation:', err);
        alert('Failed to start a new conversation');
    }
}

function closeTicket() {
    currentSelectedTicket = null;
    document.getElementById('ticketReplySection').style.display = 'none';
    document.getElementById('selectedTicketInfo').innerHTML = 
        '<p style="text-align: center; color: #666; margin: 0;">Select a ticket to view conversation</p>';
    document.getElementById('ticketChatBox').innerHTML = '';
    document.querySelectorAll('.ticket-item-history').forEach(item => item.classList.remove('active'));
    // Restore main chat container when closing ticket/conversation view
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer) chatContainer.style.display = 'block';
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px; background: #28a745; color: white; 
        padding: 12px 20px; border-radius: 5px; z-index: 10000; box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    `;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Account Settings Modal Functions
function showEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'flex';
    // Setup image preview
    document.getElementById('profilePictureInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Setup age calculation from date of birth
    const dobInput = document.querySelector('input[name="dob"]');
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            // Only store age if it's valid (greater than 0)
            if (age > 0) {
                let ageInput = document.querySelector('input[name="age"]');
                if (!ageInput) {
                    ageInput = document.createElement('input');
                    ageInput.type = 'hidden';
                    ageInput.name = 'age';
                    document.getElementById('editProfileForm').appendChild(ageInput);
                }
                ageInput.value = age;
            }
        });
    }
}

function closeEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'none';
}

function showChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'flex';
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
}

function showAboutModal() {
    document.getElementById('aboutModal').style.display = 'flex';
}

function closeAboutModal() {
    document.getElementById('aboutModal').style.display = 'none';
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function handleProfileUpdate(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("profile.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ Profile updated successfully!');
            closeEditProfileModal();
            // Reload page to show updated info, preserving the current tab
            setTimeout(() => window.location.href = window.location.href, 1000);
        } else {
            showNotification('❌ ' + (data.message || 'Failed to update profile'));
        }
    })
    .catch(error => {
        console.error('Error updating profile:', error);
        showNotification('❌ Failed to update profile');
    });
}

function handlePasswordChange(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("password.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ Password changed successfully!');
            closeChangePasswordModal();
            form.reset();
        } else {
            showNotification('❌ ' + (data.message || 'Failed to change password'));
        }
    })
    .catch(error => {
        console.error('Error changing password:', error);
        showNotification('❌ Failed to change password');
    });
}

function handleAboutUpdate(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("profile.updateAbout") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ About section updated!');
            closeAboutModal();
            // Reload page to show updated info, preserving the current tab
            setTimeout(() => window.location.href = window.location.href, 1000);
        } else {
            showNotification('❌ Failed to update about section');
        }
    })
    .catch(error => {
        console.error('Error updating about:', error);
        showNotification('❌ Failed to update about section');
    });
}

// Handle feedback form submission
function handleFeedbackSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Server error:', text);
                throw new Error('Server error');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('✅ Thank you for your feedback!');
            form.reset();
            // Reset star rating
            document.querySelectorAll('.star-rating .star').forEach(star => {
                star.style.color = '#ddd';
            });
            document.getElementById('ratingValue').value = '';
            const ratingDesc = document.getElementById('ratingDescription');
            if (ratingDesc) ratingDesc.textContent = 'Select a rating';
            // Reset selected rating
            if (typeof initializeStarRating === 'function') {
                const stars = document.querySelectorAll('.star-rating .star');
                stars.forEach(star => star.style.color = '#ddd');
            }
        } else {
            showNotification('❌ Failed to submit feedback. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error submitting feedback:', error);
        showNotification('❌ Failed to submit feedback. Please try again.');
    });
}

function startReplyChecker() {
    setInterval(async () => {
        try {
            const response = await fetch('{{ route("employee.latestTicket") }}');
            const data = await response.json();
            
            if (data.ticket_no && data.has_new_reply) {
                if (currentSelectedTicket === data.ticket_no) {
                    await loadTicketConversation(data.ticket_no);
                }
                showNotification('💌 New reply from HR!');
            }
        } catch (error) {
            console.error('Error checking replies:', error);
        }
    }, 60000); // Check every 60 seconds (reduced from 15 to save DB connections)
}
</script>

    </div>
</div>

@include('includes.footer')
</body>
</html>
