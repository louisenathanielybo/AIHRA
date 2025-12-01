<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AIHRA - HR Dashboard</title>
    @include('includes.header')
    <style>
        /* Layout */
        .sidebar {
            background: linear-gradient(180deg, #0A2F2D 0%, #0F3936 40%, #1A6B61 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
            width: 16vw;
            color: white;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
            top: 0;
            left: 0;
            bottom: 0;
        }
        .sidebar h1 { 
            font-size: 22px; 
            margin-bottom: 20px; 
            text-align: center;
        }
        .sidebar ul { 
            list-style: none; 
            padding: 0; 
        }
        .sidebar ul li { 
            margin-bottom: 15px; 
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .sidebar ul li:hover { 
            background: rgba(255,255,255,0.1);
        }
        .sidebar ul li.active { 
            background: rgba(255,255,255,0.2);
        }
        .sidebar ul li a { 
            color: white; 
            text-decoration: none; 
            font-weight: bold; 
            display: block;
        }
        
        .main-content { 
            flex: 1;
            margin-left: 16vw; 
            padding: 30px;
            padding-bottom: 0;
            background: #e6fbf5;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .section {
            flex: 1;
            min-height: calc(100vh - 200px);
            display: none;
        }

        .section.active {
            display: block;
        }

        /* New UI_AIHRA styles */
        .dashboard {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        .sb-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 22px 16px 12px 16px;
        }
        .sb-logo {
            width: 42px;
            height: 42px;
            margin-bottom: 30px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,.25));
        }
        .sb-nav {
            display: flex;
            flex-direction: column;
            padding: 8px;
            gap: 4px;
            width: 100%;
        }
        .sb-link {
            font-family: "Poppins", sans-serif;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            margin: 2px 4px;
            border-radius: 10px;
            text-decoration: none;
            color: rgba(255,255,255,.92);
            transition: background .18s ease, transform .18s ease, color .18s ease, box-shadow .18s ease;
        }
        .sb-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
        .sb-link:hover {
            background: rgba(255,255,255,.10);
            transform: translateX(2px);
        }
        .sb-link.active {
            background: rgba(255,255,255,.16);
            box-shadow: inset 3px 0 0 0 #34c759;
        }
        .sb-bottom {
            margin-top: auto;
            padding: 8px;
            border-top: 1px solid rgba(255,255,255,.12);
            width: 100%;
        }
        .sb-btn {
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .welcome-message {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-size: 24px;
            color: #0F3936;
            margin-bottom: 20px;
        }
        .welcome-message span {
            color: #1fbf8e;
        }
        
        /* Top Navigation Ribbon */
        .top-nav {
            display: flex;
            gap: 8px;
            background: white;
            padding: 8px 12px;
            border-radius: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .top-nav-link {
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            position: relative;
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

        /* Table Container */
        .table-container {
            background: white;
            padding: 20px;
            padding-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        /* HR Dashboard Specific Styles */
        .dashboard-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin: 0;
            font-size: 24px;
            color: #0c5726;
        }
        .card p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .card.high { border-left: 4px solid #dc3545; }
        .card.urgent { border-left: 4px solid #b00020; }
        .card.medium { border-left: 4px solid #ffc107; }
        .card.low { border-left: 4px solid #28a745; }
        .card.replied { border-left: 4px solid #007bff; }

        .ticket-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .ticket-list {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-height: 600px;
            overflow-y: auto;
        }
        .chat-container {
            flex: 2;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 600px;
        }

        .ticket-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .ticket-item:hover {
            background: #e9ecef;
            border-color: #0c5726;
        }
        .ticket-item.active {
            background: #e8f5e8;
            border-color: #0c5726;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 5px;
        }
        .badge.high { background: #f8d7da; color: #721c24; }
        .badge.medium { background: #fff3cd; color: #856404; }
        .badge.low { background: #d1ecf1; color: #0c5460; }
        .badge.urgent { background: #fce8e6; color: #b00020; border: 1px solid #f5c2c7; font-weight: 700; }
        .badge.replied { background: #d4edda; color: #155724; }
        .badge.resolved { background: #e2e3e5; color: #383d41; }
        .badge.expired { background: #fdecea; color: #b00020; border: 1px solid #f5c2c7; }
        .badge.deadline { background: #e7f5ff; color: #0b5ed7; border: 1px solid #b6e0fe; }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
        }
        .message.employee {
            background: #e3f2fd;
            margin-right: auto;
            border: 1px solid #bbdefb;
        }
        .message.hr {
            background: #e8f5e8;
            margin-left: auto;
            border: 1px solid #c8e6c9;
            text-align: right;
        }
        .message-meta {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .reply-form {
            display: flex;
            gap: 10px;
        }
        .reply-form input {
            flex: 1;
            margin: 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
        }
        .reply-form button {
            width: auto;
            padding: 8px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .resolve-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-top: 5px;
            width: auto;
        }
        .resolved-badge {
            color: #28a745;
            font-size: 12px;
            font-weight: bold;
        }

        .ticket-meta {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .ticket-meta span {
            margin-right: 8px;
        }

        /* Form Styles */
        input, textarea, button, select {
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            padding: 0 20px;
        }

        .account-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            width: 100%;
            box-sizing: border-box;
        }

        .account-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #1fbf8e;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
            flex-shrink: 0;
        }

        .account-details {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }

        .account-name {
            font-weight: 500;
            font-size: 14px;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .account-role {
            font-size: 12px;
            color: #a8d5b5;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-btn {
            background: none;
            border: none;
            color: #a8d5b5;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        /* ===== ACCOUNT SECTION STYLES ===== */
        .account-layout {
            display: flex;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-panel, .settings-panel {
            flex: 1;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .profile-panel h3, .settings-panel h3 {
            margin: 0 0 20px 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .profile-image-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ddd;
        }

        .profile-field {
            margin-bottom: 20px;
        }

        .profile-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .profile-value {
            padding: 12px;
            background: #e6f7f0;
            border-radius: 8px;
            color: #333;
        }

        .settings-button {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            background: #e6f7f0;
            border: none;
            border-radius: 8px;
            text-align: left;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.3s;
        }

        .settings-button:hover {
            background: #d0f0e0;
        }

        .about-section {
            margin-bottom: 30px;
        }

        .about-section label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .about-content {
            padding: 15px;
            background: #e6f7f0;
            border-radius: 8px;
            color: #333;
            cursor: pointer;
            min-height: 60px;
            transition: background 0.3s;
        }

        .about-content:hover {
            background: #d0f0e0;
        }

        .logout-button {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logout-button:hover {
            background: #218838;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            position: relative;
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-back-button {
            position: absolute;
            left: 0;
            top: 0;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
        }

        .modal-back-button i {
            font-size: 20px;
            color: #333;
        }

        .modal-title {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #0F3936;
        }

        /* Form Styles */
        .modal-form input,
        .modal-form select,
        .modal-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            background: white;
            font-family: inherit;
            font-size: 14px;
        }

        .modal-form input:focus,
        .modal-form select:focus,
        .modal-form textarea:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
        }

        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .form-row > div {
            flex: 1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
        }

        .submit-button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .submit-button:hover {
            background: #218838;
        }

        .cancel-button {
            width: 100%;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 10px;
            transition: background 0.3s;
        }

        .cancel-button:hover {
            background: #5a6268;
        }

        /* Profile Picture Upload */
        .profile-upload-container {
            display: inline-block;
            position: relative;
        }

        .profile-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ddd;
        }

        .upload-label {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #28a745;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
        }

        .upload-label i {
            color: white;
            font-size: 14px;
        }

        .file-input {
            display: none;
        }
        
        /* Announcement Card Styles */
        .announcement-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            position: relative;
        }
        
        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .announcement-title {
            color: #0c5726;
            margin: 0;
            flex: 1;
        }
        
        .announcement-actions {
            display: flex;
            gap: 15px;
            margin-left: 15px;
        }
        
        .edit-link, .delete-link {
            color: #28a745;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 0;
        }
        
        .edit-link:hover, .delete-link:hover {
            color: #1fbf8e;
            text-decoration: underline;
        }
        
        .announcement-image {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin-top: 10px;
        }
        
        /* Modal styles for edit announcement */
        #editAnnouncementModal .modal-content {
            max-width: 600px;
        }
        
        /* Add Announcement Button */
        .add-announcement-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 8px;
        }
        
        .add-announcement-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Add Announcement Modal specific styles */
        #addAnnouncementModal .modal-content {
            max-width: 500px;
        }
        
        #addAnnouncementModal .form-group label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        #addAnnouncementModal input[type="text"],
        #addAnnouncementModal textarea,
        #addAnnouncementModal input[type="file"] {
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
        }
        
        #addAnnouncementModal textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        #addAnnouncementModal input[type="file"] {
            padding: 8px;
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .modal-buttons button {
            flex: 1;
        }
        
        /* Status Messages */
        .status-message {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            display: none;
        }
        
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }
        
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body class="forAll">

<div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sb-brand" style="display: flex; align-items: center; gap: 10px;">
            <span style="color: white; font-weight: bold; font-size: 18px;">AIHRA</span>
            <img src="{{ asset('assets/AIHRA_Logo.png') }}" alt="AIHRA" class="sb-logo" style="margin-bottom: 0;">
        </div>

        <nav class="sb-nav">
            <a href="#announcements" onclick="showSection('announcements')" class="sb-link active" id="link-announcements">
                <i class="fa-solid fa-house"></i><span>Home</span>
            </a>
            <a href="#inbox" onclick="showSection('inbox')" class="sb-link" id="link-inbox">
                <i class="fa-solid fa-inbox"></i><span>Inbox</span>
            </a>
            <a href="#account" onclick="showSection('account')" class="sb-link" id="link-account">
                <i class="fa-solid fa-user"></i><span>Account</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="account-info">
                <div class="account-avatar">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="account-details">
                    <div class="account-name">{{ Auth::user()->name }}</div>
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
        <div style="background: white; padding: 15px 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 class="welcome-message" style="margin: 0;">Welcome back, <span>{{ Auth::user()->name }}</span>!</h1>
            
            <nav class="top-nav">
                <a href="#announcements" onclick="showSection('announcements')" class="top-nav-link active" id="top-link-announcements">Home</a>
                <a href="#inbox" onclick="showSection('inbox')" class="top-nav-link" id="top-link-inbox">Inbox</a>
                <a href="#account" onclick="showSection('account')" class="top-nav-link" id="top-link-account">Account</a>
            </nav>
        </div>

        <!-- Announcements Section -->
        <div id="announcements" class="section active">
            <div class="table-container">
                <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 10px; margin-bottom: 20px;">
                    <h2 style="margin: 0;">🏠 Announcements</h2>
                    <button class="add-announcement-btn" onclick="showAddAnnouncementModal()">
                        + Add Announcement
                    </button>
                </div>

                {{-- Display announcements --}}
                <div style="display: flex; flex-direction: column; gap: 15px;" id="announcementsList">
                    @php
                        $announcements = DB::table('announcements')
                            ->where('isActive', 1)
                            ->orderBy('createdAt', 'desc')
                            ->get();
                    @endphp

                    @forelse ($announcements as $a)
                        <div class="announcement-card" id="announcement-{{ $a->id }}">
                            <div class="announcement-header">
                                <h3 class="announcement-title">{{ $a->title }}</h3>
                                <div class="announcement-actions">
                                    <span class="delete-link" onclick="deleteAnnouncement({{ $a->id }})">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </span>
                                </div>
                            </div>
                            <p style="color: #666; margin-bottom: 10px;">{{ $a->description }}</p>
                            @if ($a->image)
                                <img src="data:image/jpeg;base64,{{ base64_encode($a->image) }}" 
                                    class="announcement-image">
                            @endif
                            <small style="color: #999;">
                                {{ \Carbon\Carbon::parse($a->createdAt)->timezone('Asia/Manila')->format('M d, Y \a\t h:i A') }}
                            </small>
                        </div>
                    @empty
                        <p style="color: #666; text-align: center; padding: 40px;">No announcements yet. Create your first announcement!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Inbox Section -->
        <div id="inbox" class="section">
            <div class="table-container">
                <h2>📨 Inbox</h2>

                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card">
                        <h3>{{ $inbox->count() }}</h3>
                        <p>Total Tickets</p>
                    </div>
                    <div class="card urgent">
                        <h3>{{ $inbox->where('priority', 'urgent')->count() }}</h3>
                        <p>Urgent Priority</p>
                    </div>
                    <div class="card high">
                        <h3>{{ $inbox->where('priority', 'high')->count() }}</h3>
                        <p>High Priority</p>
                    </div>
                    <div class="card medium">
                        <h3>{{ $inbox->where('priority', 'medium')->count() }}</h3>
                        <p>Medium Priority</p>
                    </div>
                    <div class="card low">
                        <h3>{{ $inbox->where('priority', 'low')->count() }}</h3>
                        <p>Low Priority</p>
                    </div>
                    <div class="card replied">
                        <h3>{{ $inbox->where('status', 'Replied')->count() }}</h3>
                        <p>Replied</p>
                    </div>
                </div>

                <div class="ticket-container">
                    <!-- Ticket List -->
                    <div class="ticket-list">
                        <h3 style="margin-top: 0;">Pending Tickets</h3>
                        @forelse($inbox as $ticket)
                            <div class="ticket-item" id="ticket-{{ $ticket->ticket_no }}" onclick="openTicket('{{ $ticket->ticket_no }}')">
                                <strong>🎫 {{ $ticket->ticket_no }}</strong>
                                <div class="ticket-meta">
                                    <div>{{ Str::limit($ticket->message, 50) }}</div>
                                    <div>
                                        <span class="badge {{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                                        <span class="badge">{{ $ticket->category }}</span>
                                        @php
                                            $deadlineLabel = null;
                                            $deadlineOverdue = false;
                                            $action = null;
                                            if ($ticket->status !== 'Resolved') {
                                                if (is_null($ticket->responded_at)) {
                                                    $deadline = $ticket->response_deadline;
                                                    $action = 'Respond';
                                                } else {
                                                    $deadline = $ticket->resolution_deadline;
                                                    $action = 'Resolve';
                                                }
                                                if ($deadline) {
                                                    $dl = \Carbon\Carbon::parse($deadline);
                                                    $deadlineOverdue = $dl->isPast();
                                                    $diff = $dl->diffForHumans(null, ['parts' => 2, 'short' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
                                                    $deadlineLabel = $deadlineOverdue ? ($action . ' overdue by ' . $diff) : ($action . ' in ' . $diff);
                                                }
                                            }
                                        @endphp
                                        @if(!is_null($deadlineLabel))
                                            <span class="badge deadline {{ $deadlineOverdue ? 'overdue' : '' }}">{{ $deadlineLabel }}</span>
                                        @endif
                                    </div>
                                    @if($ticket->status === 'Replied')
                                        <span class="badge replied">Replied</span>
                                    @elseif($ticket->status === 'Resolved')
                                        <span class="badge resolved">Resolved</span>
                                    @endif
                                    @if($ticket->is_expired)
                                        <span class="badge expired">Expired</span>
                                    @endif
                                    
                                    @if($ticket->status !== 'Resolved')
                                    <button class="resolve-btn" onclick="event.stopPropagation(); resolveTicket('{{ $ticket->ticket_no }}')">
                                        ✅ Resolve
                                    </button>
                                    @else
                                    <span class="resolved-badge">✅ Resolved</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p>No tickets available.</p>
                        @endforelse
                    </div>

                    <!-- Chat Container -->
                    <div class="chat-container">
                        <h3 style="margin-top: 0;">Ticket Conversation</h3>
                        <div class="chat-messages" id="chatMessages">
                            <p style="text-align: center; color: #666; margin-top: 50px;">Select a ticket to view conversation</p>
                        </div>
                        <form id="replyForm" class="reply-form">
                            @csrf
                            <input type="hidden" name="ticket_no" id="ticket_no">
                            <input type="text" id="replyMessage" name="message" placeholder="Type your reply..." required>
                            <button type="submit">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Section -->
        <div id="account" class="section">
            <div class="table-container">
                <h2>👤 Account Settings</h2>
                
                <div class="account-layout">
                    <!-- Left Panel - Profile -->
                    <div class="profile-panel">
                        <h3>Profile</h3>
                        
                        <div class="profile-image-container">
                            <img src="{{ isset($user) && $user->profile_picture ? asset('uploads/'.$user->profile_picture) : asset('assets/Logo.png') }}" 
                                 alt="Profile Picture" class="profile-image">
                        </div>

                        <div class="profile-field">
                            <label>Name</label>
                            <div class="profile-value">
                                {{ $user->name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="profile-field">
                            <label>Email</label>
                            <div class="profile-value">
                                {{ $user->email ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="profile-field">
                            <label>Employee Number</label>
                            <div class="profile-value">
                                {{ $user->employeeNum ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="profile-field">
                                <label>Age</label>
                                <div class="profile-value">
                                    @php
                                        if (isset($user->dob) && $user->dob) {
                                            $dob = new DateTime($user->dob);
                                            $today = new DateTime();
                                            $age = $today->diff($dob)->y;
                                            echo $age;
                                        } else {
                                            echo $user->age ?? 'N/A';
                                        }
                                    @endphp
                                </div>
                            </div>
                            <div class="profile-field">
                                <label>Sex</label>
                                <div class="profile-value">
                                    {{ $user->sex ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Account Settings -->
                    <div class="settings-panel">
                        <h3>Account Settings</h3>

                        <button class="settings-button" onclick="showEditProfileModal()">
                            Edit Profile
                        </button>

                        <button class="settings-button" onclick="showChangePasswordModal()">
                            Change Password
                        </button>

                        <div class="about-section">
                            <label>About</label>
                            <div class="about-content" onclick="showAboutModal()">
                                {{ $user->about ?: 'Click to add information about yourself...' }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-button">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Announcement Modal -->
<div id="addAnnouncementModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Announcement</h3>
        </div>
        <div id="announcementStatus" class="status-message"></div>
        <form id="addAnnouncementForm" class="modal-form" action="{{ route('hr.announcements.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Title <span class="required">*</span></label>
                <input type="text" name="title" id="announcementTitle" required 
                       placeholder="Enter announcement title" maxlength="200">
            </div>

            <div class="form-group">
                <label>Description <span class="required">*</span></label>
                <textarea name="description" id="announcementDescription" rows="4" required 
                          placeholder="Enter announcement description"></textarea>
            </div>

            <div class="form-group">
                <label>Image (optional)</label>
                <input type="file" name="image" id="announcementImage" accept="image/*">
                <small style="color: #666; display: block; margin-top: 5px;">
                    Supported formats: JPG, PNG, GIF. Max size: 64KB.
                </small>
            </div>

            <div class="modal-buttons">
                <button type="button" class="cancel-button" onclick="closeAddAnnouncementModal()">Cancel</button>
                <button type="submit" class="submit-button">Create Announcement</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <button class="modal-back-button" onclick="closeEditProfileModal()">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h3 class="modal-title">Edit Profile</h3>
        </div>
        <form id="editProfileForm" class="modal-form" onsubmit="handleProfileUpdate(event)">
            @csrf
            
            <div class="form-group" style="text-align: center; margin-bottom: 20px;">
                <div class="profile-upload-container">
                    <img id="profilePreview" class="profile-preview" 
                         src="{{ isset($user) && $user->profile_picture ? asset('uploads/'.$user->profile_picture) : asset('assets/Logo.png') }}">
                    <label for="profilePictureInput" class="upload-label">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                    <input type="file" id="profilePictureInput" class="file-input" 
                           name="profile_picture" accept="image/*">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="firstName" value="{{ $user->firstName ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lastName" value="{{ $user->lastName ?? '' }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $user->email ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="{{ $user->dob ?? '' }}" 
                           max="{{ date('Y-m-d', strtotime('-1 year')) }}">
                </div>
            </div>

            <div class="form-group">
                <label>Employee Number</label>
                <input type="text" name="employeeNum" value="{{ $user->employeeNum ?? '' }}" readonly>
            </div>

            <div class="form-group">
                <label>Sex</label>
                <select name="sex">
                    <option value="Male" {{ ($user->sex ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ ($user->sex ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <button type="submit" class="submit-button">Save</button>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Change Password</h3>
        </div>
        <form id="changePasswordForm" class="modal-form" onsubmit="handlePasswordChange(event)">
            @csrf
            
            <div class="form-group">
                <label>Current Password</label>
                <div style="position: relative;">
                    <input type="password" name="current_password" id="currentPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <div style="position: relative;">
                    <input type="password" name="new_password" id="newPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password" name="new_password_confirmation" id="confirmPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="button" class="cancel-button" onclick="closeChangePasswordModal()">Cancel</button>
            <button type="submit" class="submit-button">Save</button>
        </form>
    </div>
</div>

<!-- About Modal -->
<div id="aboutModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <button class="modal-back-button" onclick="closeAboutModal()">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h3 class="modal-title">About</h3>
        </div>
        <form id="aboutForm" class="modal-form" onsubmit="handleAboutUpdate(event)">
            @csrf
            
            <div class="form-group">
                <label>Tell us about yourself</label>
                <textarea name="about" rows="8" placeholder="Share something about yourself...">{{ $user->about ?? '' }}</textarea>
            </div>

            <button type="submit" class="submit-button">Save</button>
        </form>
    </div>
</div>

<!-- Edit Announcement Modal -->
<div id="editAnnouncementModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <button class="modal-back-button" onclick="closeEditAnnouncementModal()">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h3 class="modal-title">Edit Announcement</h3>
        </div>
        <form id="editAnnouncementForm" class="modal-form" onsubmit="handleAnnouncementUpdate(event)">
            @csrf
            @method('PUT')
            <input type="hidden" name="announcement_id" id="editAnnouncementId">
            
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" id="editAnnouncementTitle" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="editAnnouncementDescription" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Current Image</label>
                <div id="currentImageContainer">
                    <img id="currentAnnouncementImage" src="" style="max-width: 100%; height: auto; border-radius: 6px; display: none;">
                    <p id="noImageMessage" style="color: #666;">No image uploaded</p>
                </div>
            </div>

            <div class="form-group">
                <label>Update Image (optional)</label>
                <input type="file" name="image" id="editAnnouncementImage" accept="image/*">
                <small style="color: #666;">Leave empty to keep current image</small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="submit-button" style="flex: 1;">Update</button>
                <button type="button" class="cancel-button" onclick="closeEditAnnouncementModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentTicket = null;

    function showSection(id) {
        // Scroll to top smoothly
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Fade out current section
        const currentSection = document.querySelector('.section.active');
        if (currentSection) {
            currentSection.style.opacity = '0';
            currentSection.style.transition = 'opacity 0.2s ease';
        }
        
        setTimeout(() => {
            document.querySelectorAll('.section').forEach(s => {
                s.classList.remove('active');
                s.style.opacity = '0';
            });
            
            const section = document.getElementById(id);
            if (section) {
                section.classList.add('active');
                // Fade in new section
                setTimeout(() => {
                    section.style.opacity = '1';
                    section.style.transition = 'opacity 0.3s ease';
                }, 10);
            }
        }, 200);
        
        // Update active nav item with new sidebar structure
        document.querySelectorAll('.sb-link').forEach(link => link.classList.remove('active'));
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
    }

    async function resolveTicket(ticketNo) {
        if (!confirm('Mark this ticket as resolved?')) return;
        try {
            const res = await fetch('/hr/resolve-ticket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ticket_no: ticketNo })
            });
            const result = await res.json();
            console.log('Resolve response:', result);
            if (result.success) {
                alert('Ticket resolved!');
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Resolve error:', error);
            alert('Failed to resolve ticket');
        }
    }

    async function openTicket(ticketNo) {
        currentTicket = ticketNo;
        document.querySelectorAll('.ticket-item').forEach(item => item.classList.remove('active'));
        document.getElementById(`ticket-${ticketNo}`).classList.add('active');
        try {
            const response = await fetch(`/hr/messages/${ticketNo}`);
            const messages = await response.json();
            const chat = document.getElementById('chatMessages');
            chat.innerHTML = '';
            if (messages.length === 0) {
                chat.innerHTML = '<p style="text-align: center; color: #666; margin-top: 50px;">No messages found for this ticket</p>';
                return;
            }
            messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${msg.sender}`;
                messageDiv.innerHTML = `
                    <div>${msg.message}</div>
                    <div class="message-meta">
                        ${msg.sender === 'employee' ? 'Employee' : 'HR'} • 
                        ${new Date(msg.created_at).toLocaleString()}
                    </div>
                `;
                chat.appendChild(messageDiv);
            });
            document.getElementById('ticket_no').value = ticketNo;
            chat.scrollTop = chat.scrollHeight;
        } catch (error) {
            console.error('Error loading messages:', error);
            document.getElementById('chatMessages').innerHTML = '<p style="color: red;">Error loading messages</p>';
        }
    }

    document.getElementById('replyForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const ticketNo = document.getElementById('ticket_no').value;
        const msg = document.getElementById('replyMessage').value.trim();
        if (!msg || !ticketNo) {
            alert('Please select a ticket and enter a message');
            return;
        }
        try {
            console.log('Sending reply for ticket:', ticketNo);
            const res = await fetch(`/hr/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    ticket_no: ticketNo, 
                    message: msg 
                })
            });
            const result = await res.json();
            console.log('Reply response:', result);
            if (res.ok && result.success) {
                document.getElementById('replyMessage').value = '';
                await openTicket(ticketNo);
            } else {
                alert('Failed to send reply: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Reply error:', error);
            alert('Failed to send reply. Check console for details.');
        }
    });

    // ===== ANNOUNCEMENT MODAL FUNCTIONS =====
    function showAddAnnouncementModal() {
        document.getElementById('addAnnouncementModal').style.display = 'flex';
        document.getElementById('addAnnouncementForm').reset();
        document.getElementById('announcementStatus').className = 'status-message';
        document.getElementById('announcementStatus').innerHTML = '';
        document.getElementById('announcementStatus').style.display = 'none';
    }
    
    function closeAddAnnouncementModal() {
        document.getElementById('addAnnouncementModal').style.display = 'none';
    }
    
document.getElementById('addAnnouncementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const statusElement = document.getElementById('announcementStatus');
    const submitButton = form.querySelector('.submit-button');
    
    const originalText = submitButton.textContent;
    submitButton.innerHTML = 'Creating...';
    submitButton.disabled = true;
    
    form.submit();
    
});
    
    function editAnnouncement(id) {
        // Fetch announcement data
        fetch(`/hr/announcements/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const announcement = data.announcement;
                    
                    // Populate the edit form
                    document.getElementById('editAnnouncementId').value = announcement.id;
                    document.getElementById('editAnnouncementTitle').value = announcement.title;
                    document.getElementById('editAnnouncementDescription').value = announcement.description;
                    
                    // Handle image display
                    const imageElement = document.getElementById('currentAnnouncementImage');
                    const noImageMessage = document.getElementById('noImageMessage');
                    
                    if (announcement.image) {
                        imageElement.src = `data:image/jpeg;base64,${announcement.image}`;
                        imageElement.style.display = 'block';
                        noImageMessage.style.display = 'none';
                    } else {
                        imageElement.style.display = 'none';
                        noImageMessage.style.display = 'block';
                    }
                    
                    // Show the modal
                    document.getElementById('editAnnouncementModal').style.display = 'flex';
                } else {
                    showNotification('❌ Failed to load announcement data');
                }
            })
            .catch(error => {
                console.error('Error loading announcement:', error);
                showNotification('❌ Failed to load announcement data');
            });
    }
    
    function closeEditAnnouncementModal() {
        document.getElementById('editAnnouncementModal').style.display = 'none';
    }
    
    function handleAnnouncementUpdate(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const announcementId = document.getElementById('editAnnouncementId').value;
        
        fetch(`/hr/announcements/${announcementId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ Announcement updated successfully!');
                closeEditAnnouncementModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('❌ ' + (data.message || 'Failed to update announcement'));
            }
        })
        .catch(error => {
            console.error('Error updating announcement:', error);
            showNotification('❌ Failed to update announcement');
        });
    }
    
    function deleteAnnouncement(id) {
        if (!confirm('Are you sure you want to delete this announcement?')) {
            return;
        }
        
        fetch(`/hr/announcements/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ Announcement deleted successfully!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('❌ ' + (data.message || 'Failed to delete announcement'));
            }
        })
        .catch(error => {
            console.error('Error deleting announcement:', error);
            showNotification('❌ Failed to delete announcement');
        });
    }

    // ===== ACCOUNT SECTION JAVASCRIPT =====
    
    // Account Modal Functions
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

    // Form Handlers
    function handleProfileUpdate(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        fetch('{{ route("profile.update") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ Profile updated successfully!');
                closeEditProfileModal();
                setTimeout(() => location.reload(), 1000);
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
        
        fetch('{{ route("profile.updateAbout") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ About section updated!');
                closeAboutModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('❌ Failed to update about section');
            }
        })
        .catch(error => {
            console.error('Error updating about:', error);
            showNotification('❌ Failed to update about section');
        });
    }

    // Utility function for notifications
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

    // Close modals when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        showSection('announcements');
        
        // Close modal when clicking on overlay
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
    });
</script>

@include('includes.footer')
</body>
</html>