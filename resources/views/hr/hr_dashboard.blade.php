<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AIHRA - HR Dashboard</title>
    @include('includes.header')
    <style>
                /* Highlight active KPI card */
                .dashboard-cards .card.active {
                    border: 2px solid #1A6B61;
                    box-shadow: 0 4px 16px rgba(26,107,97,0.10);
                    background: #e6f7f0;
                    transform: scale(1.04);
                    z-index: 1;
                }
        /* HR Dashboard Styles - Version 2.0 - Updated {{ date('Y-m-d H:i:s') }} */
        /* Layout */
        .sidebar {
            background: linear-gradient(180deg, #164a4a 0%, #2a5547 100%);
            box-shadow: 2px 0 10px rgba(26, 77, 77, 0.1);
            width: 250px;
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
        .sidebar h1 { 
            font-size: 22px; 
            margin-bottom: 20px; 
            text-align: center;
        }
        
        .main-content { 
            flex: 1;
            margin-left: 250px; 
            padding: 30px;
            padding-bottom: 0;
            background: #e6f7f0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            max-width: calc(100vw - 250px);
            overflow-x: hidden;
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
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }
        
        .sb-brand h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #e8f5e8;
        }
        .sb-logo {
            width: 42px;
            height: 42px;
            margin-bottom: 30px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,.25));
        }
        .sb-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
            overflow-y: auto;
        }
        
        .sb-nav li {
            margin-bottom: 5px;
        }
        
        .sb-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            padding-left: 20px;
            color: #e8f5e8;
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 0 8px 8px 0;
            margin-right: 10px;
            border-left: 4px solid transparent;
        }
        
        .sb-link:hover, 
        .sb-link.active {
            background: rgba(255,255,255,0.15);
            border-left: 4px solid #1A6B61;
            padding-left: 20px;
            color: white;
        }
        
        .sb-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: #a8d5b5;
        }
        
        .top-nav {
            display: flex;
            gap: 8px;
            background: white;
            padding: 8px 12px;
            border-radius: 25px;
            box-shadow: 0 2px 8px rgba(22, 74, 74, 0.08);
            border: 1px solid #c7e5e0;
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
            background: #1A6B61;
            color: white;
            border-color: #1A6B61;
            transform: scale(1.05);
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

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #d0e8da;
        }
        
        .welcome-message {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-size: 1.8rem;
            color: #0A2F2D;
        }
        .welcome-message span {
            color: #1A6B61;
        }

        /* Top Navigation Ribbon */
        /* Top nav ribbon - REMOVED */
        /* .top-nav {
            display: flex;
            gap: 8px;
            background: white;
            padding: 8px 12px;
            border-radius: 25px;
            box-shadow: 0 2px 8px rgba(10, 47, 45, 0.08);
            border: 1px solid #d0e8da;
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
            background: #f0f7f2;
            color: #333;
        }
        
        .top-nav-link.active {
            background: #28a745;
            color: white;
            border-color: #28a745;
            transform: scale(1.05);
        } */

        /* Split tabs (match employee UI) */
        .split-tabs {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            background: #fff;
            padding: 6px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .split-tab {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 10px 6px;
            border-radius: 16px;
            flex: 1;
            justify-content: center;
            border: 1px solid transparent;
            background: transparent;
            color: #444;
            cursor: pointer;
            font-weight: 500;
            transition: all .18s ease;
            font-size: 0.85rem;
            min-width: 0;
        }
        .split-tab i {
            font-size: 1.1rem;
        }
        .split-tab span:not(.tab-count) {
            display: block;
            text-align: center;
        }
        .split-tab:hover { background: #f2f2f2; }
        .split-tab.active {
            background: #28a745;
            color: #fff;
            border-color: #28a745;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.2);
        }
        .split-tab .tab-count {
            background: rgba(255,255,255,.2);
            color: inherit;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
        .split-heading {
            margin-top: 10px;
            margin-bottom: 8px;
            font-weight: 600;
            color: #0F3936;
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
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .ticket-list-header {
            flex-shrink: 0;
            background: white;
            padding-bottom: 10px;
        }
        .ticket-list-content {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
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
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px;
        }

        .account-info {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            width: 100%;
            box-sizing: border-box;
        }

        .account-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1A6B61;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .account-details {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }

        .account-name {
            font-weight: 500;
            font-size: 12px;
            color: white;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-role {
            font-size: 10px;
            color: #7dd3c0;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .logout-btn {
            background: none;
            border: none;
            color: #7dd3c0;
            cursor: pointer;
            padding: 0;
            border-radius: 4px;
            transition: all 0.3s;
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .logout-btn i {
            font-size: 13px;
        }

        /* ===== ACCOUNT SECTION STYLES ===== */
        .account-layout {
            display: flex;
            gap: 15px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .profile-panel, .settings-panel {
            flex: 1;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .profile-panel h3, .settings-panel h3 {
            margin: 0 0 10px 0;
            font-size: 17px;
            font-weight: bold;
            color: #333;
        }

        .profile-image-container {
            text-align: center;
            margin-bottom: 12px;
        }

        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .profile-field {
            margin-bottom: 8px;
        }

        .profile-field label {
            display: block;
            margin-bottom: 3px;
            font-weight: 500;
            color: #333;
            font-size: 12px;
        }

        .profile-value {
            padding: 8px;
            background: #e6f7f0;
            border-radius: 6px;
            color: #333;
            font-size: 13px;
        }

        .settings-button {
            width: 100%;
            padding: 10px;
            margin-bottom: 8px;
            background: #e6f7f0;
            border: none;
            border-radius: 6px;
            text-align: left;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #2d5a3d;
            transition: background 0.3s;
        }

        .settings-button:hover {
            background: #d0f0e0;
        }

        .about-section {
            margin-bottom: 12px;
        }

        .about-section label {
            display: block;
            margin-bottom: 4px;
            font-weight: 500;
            color: #333;
            font-size: 12px;
        }

        .about-content {
            padding: 10px;
            background: #e6f7f0;
            border-radius: 6px;
            color: #333;
            cursor: pointer;
            min-height: 40px;
            transition: background 0.3s;
            font-size: 13px;
        }

        .about-content:hover {
            background: #d0f0e0;
        }

        .logout-button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
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

        /* Responsive Design */
        @media (max-width: 1600px) {
            .sidebar {
                width: 250px;
            }
            .main-content {
                margin-left: 250px;
            }
        }

        @media (max-width: 1400px) {
            .sidebar {
                width: 240px;
            }
            .main-content {
                margin-left: 240px;
                padding: 25px;
            }
        }

        @media (max-width: 1200px) {
            .sidebar {
                width: 230px;
            }
            .main-content {
                margin-left: 230px;
                padding: 20px;
            }
            .split-tab {
                font-size: 0.8rem;
                padding: 8px 4px;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
                padding: 15px;
            }
            .ticket-container {
                flex-direction: column;
            }
            .ticket-list,
            .chat-container {
                width: 100%;
                flex: none;
            }
        }

        /* Search Box Styles */
        .search-box {
            position: relative;
            width: 100%;
            max-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #e0efe5;
            border-radius: 20px;
            font-size: 0.9rem;
            background: white;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(74, 140, 94, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        @media (max-width: 768px) {
            .search-box {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
                padding: 12px;
            }
            .split-tabs {
                gap: 4px;
                padding: 4px;
            }
            .split-tab {
                font-size: 0.75rem;
                padding: 6px 2px;
                gap: 2px;
            }
            .split-tab i {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                position: fixed;
                left: -250px;
                width: 250px;
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
        <div class="sb-brand">
            <h2 style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                AIHRA
                <img src="{{ asset('assets/AIHRA_Logo.png') }}" alt="AIHRA" class="sb-logo" style="width: 50px; height: 50px; margin-bottom: 0;">
            </h2>
        </div>

        <ul class="sb-nav">
            <li><a href="#announcements" onclick="showSection('announcements')" class="sb-link active" id="link-announcements"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#inbox" onclick="showSection('inbox')" class="sb-link" id="link-inbox"><i class="fas fa-inbox"></i> Inbox</a></li>
            <li><a href="#account" onclick="showSection('account')" class="sb-link" id="link-account"><i class="fas fa-user"></i> Account</a></li>
        </ul>

        <div class="sidebar-footer">
            <div class="account-info">
                @if(isset($user) && $user->profile_picture)
                    <img src="{{ asset('uploads/'.$user->profile_picture) }}" alt="Profile" class="account-avatar" style="object-fit: cover;">
                @else
                    <div class="account-avatar">
                        {{ substr(Auth::user()->firstName ?? Auth::user()->name, 0, 1) }}{{ substr(Auth::user()->lastName ?? '', 0, 1) }}
                    </div>
                @endif
                <div class="account-details">
                    <div class="account-name">{{ Auth::user()->firstName ?? Auth::user()->name }} {{ Auth::user()->lastName ?? '' }}</div>
                    <div class="account-role">Human<br>Resources</div>
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
        <div style="background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(22, 74, 74, 0.08); display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border: 1px solid #c7e5e0;">
            <h1 class="welcome-message" style="margin: 0; font-size: 1.8rem; font-weight: 600;">Welcome, <span>{{ Auth::user()->name }}</span>!</h1>
            
            <nav class="top-nav">
                <a href="#announcements" onclick="showSection('announcements')" class="top-nav-link active" id="top-link-announcements">Announcements</a>
                <a href="#inbox" onclick="showSection('inbox')" class="top-nav-link" id="top-link-inbox">Inbox</a>
                <a href="#account" onclick="showSection('account')" class="top-nav-link" id="top-link-account">Account</a>
            </nav>
        </div>

        <!-- Announcements Section -->
        <div id="announcements" class="section active">
            <!-- Inbox Summary (Home only) -->
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 20px;">
                <div style="background: #ffffff; border-radius: 10px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div style="font-size: 14px; color: #555;">Inbox</div>
                    <div style="font-size: 24px; font-weight: 700;">{{ $inboxStats['total'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #888;">Total Tickets</div>
                </div>
                <div style="background: #fff4f4; border: 1px solid #f8c7c7; border-radius: 10px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 14px; color: #b00020;">Urgent Priority</div>
                    <div style="font-size: 24px; font-weight: 700; color: #b00020;">{{ $inboxStats['urgent'] ?? 0 }}</div>
                </div>
                <div style="background: #fff9f1; border: 1px solid #ffd9a5; border-radius: 10px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 14px; color: #e65100;">High Priority</div>
                    <div style="font-size: 24px; font-weight: 700; color: #e65100;">{{ $inboxStats['high'] ?? 0 }}</div>
                </div>
                <div style="background: #f1fff6; border: 1px solid #b8e6c3; border-radius: 10px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 14px; color: #2e7d32;">Medium Priority</div>
                    <div style="font-size: 24px; font-weight: 700; color: #2e7d32;">{{ $inboxStats['medium'] ?? 0 }}</div>
                </div>
                <div style="background: #f5f9ff; border: 1px solid #c6dcff; border-radius: 10px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 14px; color: #1565c0;">Low Priority</div>
                    <div style="font-size: 24px; font-weight: 700; color: #1565c0;">{{ $inboxStats['low'] ?? 0 }}</div>
                </div>
                <div style="background: #f7f7f7; border-radius: 10px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 14px; color: #444;">Replied</div>
                    <div style="font-size: 24px; font-weight: 700; color: #444;">{{ $inboxStats['replied'] ?? 0 }}</div>
                </div>
            </div>
            <div class="table-container">
                <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 10px; margin-bottom: 20px;">
                    <h2 style="margin: 0;">📢 Announcements</h2>
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
                                    <span class="edit-link" onclick="editAnnouncement({{ $a->id }})" style="margin-right: 15px; cursor: pointer; color: #4CAF50;">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </span>
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
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                                <small style="color: #999;">
                                    {{ \Carbon\Carbon::parse($a->createdAt)->timezone('Asia/Manila')->format('M d, Y \a\t h:i A') }}
                                </small>
                                @if($a->expiry_date)
                                    @php
                                        $expiryDate = \Carbon\Carbon::parse($a->expiry_date);
                                        $now = \Carbon\Carbon::now();
                                        $daysLeft = $now->diffInDays($expiryDate, false);
                                    @endphp
                                    <small style="padding: 4px 8px; border-radius: 4px; font-weight: 500;
                                        {{ $daysLeft < 0 ? 'background: #f44336; color: white;' : ($daysLeft <= 3 ? 'background: #ff9800; color: white;' : 'background: #4CAF50; color: white;') }}">
                                        @if($daysLeft < 0)
                                            ⚠️ Expired
                                        @elseif($daysLeft == 0)
                                            ⏰ Expires today
                                        @elseif($daysLeft == 1)
                                            ⏰ Expires in 1 day
                                        @else
                                            ⏰ Expires in {{ $daysLeft }} days
                                        @endif
                                    </small>
                                @endif
                            </div>
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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">📨 Inbox</h2>
                    <div class="date-range-filter" style="display: flex; align-items: center; gap: 10px;">
                        <label for="inboxDateRangeSelect" style="font-weight: 500; color: var(--primary);">
                            <i class="fas fa-calendar-alt"></i> Range:
                        </label>
                        <select id="inboxDateRangeSelect" onchange="applyInboxDateFilter()" style="padding: 8px 15px; border: 1px solid #e0efe5; border-radius: 20px; background: white; color: var(--primary); font-size: 0.9rem; cursor: pointer; box-shadow: 0 2px 8px rgba(45, 90, 61, 0.08);">
                            <option value="daily">Today</option>
                            <option value="weekly">This Week</option>
                            <option value="monthly">This Month</option>
                            <option value="annually">This Year</option>
                            <option value="overall" selected>Overall</option>
                        </select>
                        <span id="inboxDateRangeDisplay" style="color: #666; font-size: 0.85rem;">All Time</span>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card" id="kpi-total" onclick="filterTicketsByKPI('all')" style="cursor:pointer;">
                        <h3>{{ $inbox->count() }}</h3>
                        <p>Total Tickets</p>
                    </div>
                    <div class="card urgent" id="kpi-urgent" onclick="filterTicketsByKPI('urgent')" style="cursor:pointer;">
                        <h3>{{ $inbox->where('priority', 'urgent')->count() }}</h3>
                        <p>Urgent Priority</p>
                    </div>
                    <div class="card high" id="kpi-high" onclick="filterTicketsByKPI('high')" style="cursor:pointer;">
                        <h3>{{ $inbox->where('priority', 'high')->count() }}</h3>
                        <p>High Priority</p>
                    </div>
                    <div class="card medium" id="kpi-medium" onclick="filterTicketsByKPI('medium')" style="cursor:pointer;">
                        <h3>{{ $inbox->where('priority', 'medium')->count() }}</h3>
                        <p>Medium Priority</p>
                    </div>
                    <div class="card low" id="kpi-low" onclick="filterTicketsByKPI('low')" style="cursor:pointer;">
                        <h3>{{ $inbox->where('priority', 'low')->count() }}</h3>
                        <p>Low Priority</p>
                    </div>
                    <div class="card replied" id="kpi-replied" onclick="filterTicketsByKPI('replied')" style="cursor:pointer;">
                        <h3>{{ $inbox->where('status', 'Replied')->count() }}</h3>
                        <p>Replied</p>
                    </div>
                </div>

                <div class="ticket-container">
                    <!-- Ticket List with Pending/Resolved toggle -->
                    <script>
                    // Unified filter state
                    let currentKPI = 'all';
                    let currentDateRange = 'overall';

                    function filterTicketsByKPI(type) {
                        currentKPI = type;
                        // Remove active class from all KPI cards
                        document.querySelectorAll('.dashboard-cards .card').forEach(card => card.classList.remove('active'));
                        // Add active class to selected
                        let activeId = 'kpi-' + (type === 'all' ? 'total' : type);
                        let activeCard = document.getElementById(activeId);
                        if (activeCard) activeCard.classList.add('active');
                        applyCombinedFilters();
                    }

                    function applyInboxDateFilter() {
                        const rangeSelect = document.getElementById('inboxDateRangeSelect');
                        const rangeDisplay = document.getElementById('inboxDateRangeDisplay');
                        currentDateRange = rangeSelect.value;
                        const today = new Date();
                        let startDate, endDate, displayText;
                        switch(currentDateRange) {
                            case 'daily':
                                startDate = new Date(today.setHours(0, 0, 0, 0));
                                endDate = new Date(today.setHours(23, 59, 59, 999));
                                displayText = 'Today';
                                break;
                            case 'weekly':
                                const firstDayOfWeek = today.getDate() - today.getDay();
                                startDate = new Date(today.setDate(firstDayOfWeek));
                                startDate.setHours(0, 0, 0, 0);
                                endDate = new Date();
                                displayText = 'This Week';
                                break;
                            case 'monthly':
                                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                                endDate = new Date();
                                displayText = 'This Month';
                                break;
                            case 'annually':
                                startDate = new Date(today.getFullYear(), 0, 1);
                                endDate = new Date();
                                displayText = 'This Year';
                                break;
                            case 'overall':
                            default:
                                startDate = null;
                                endDate = null;
                                displayText = 'All Time';
                        }
                        rangeDisplay.textContent = displayText;
                        applyCombinedFilters();
                    }

                    function applyCombinedFilters() {
                        // Get search term
                        const searchTerm = document.getElementById('searchInboxTickets')?.value?.toLowerCase() || '';
                        
                        // Date range logic
                        const today = new Date();
                        let startDate, endDate;
                        switch(currentDateRange) {
                            case 'daily':
                                startDate = new Date(today.setHours(0, 0, 0, 0));
                                endDate = new Date(today.setHours(23, 59, 59, 999));
                                break;
                            case 'weekly':
                                const firstDayOfWeek = today.getDate() - today.getDay();
                                startDate = new Date(today.setDate(firstDayOfWeek));
                                startDate.setHours(0, 0, 0, 0);
                                endDate = new Date();
                                break;
                            case 'monthly':
                                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                                endDate = new Date();
                                break;
                            case 'annually':
                                startDate = new Date(today.getFullYear(), 0, 1);
                                endDate = new Date();
                                break;
                            case 'overall':
                            default:
                                startDate = null;
                                endDate = null;
                        }
                        // Filter tickets by KPI, date, and search
                        let allTickets = document.querySelectorAll('#inbox .ticket-item');
                        allTickets.forEach(ticket => {
                            let show = true;
                            
                            // Search filter
                            if (searchTerm && show) {
                                const ticketText = ticket.textContent.toLowerCase();
                                show = ticketText.includes(searchTerm);
                            }
                            
                            // KPI filter
                            if(currentKPI !== 'all' && show) {
                                if(currentKPI === 'replied') {
                                    show = ticket.getAttribute('data-status') === 'Replied';
                                } else {
                                    show = ticket.getAttribute('data-priority') === currentKPI;
                                }
                            }
                            // Date filter
                            if(show && startDate && endDate) {
                                const createdDate = ticket.getAttribute('data-created');
                                if (!createdDate) {
                                    show = false;
                                } else {
                                    const ticketDate = new Date(createdDate);
                                    show = (ticketDate >= startDate && ticketDate <= endDate);
                                }
                            }
                            ticket.style.display = show ? '' : 'none';
                        });
                        updateInboxCards();
                        updateTabCounts();
                    }

                    // Set default active on load
                    document.addEventListener('DOMContentLoaded', function() {
                        filterTicketsByKPI('all');
                        
                        // Add search functionality
                        const searchInput = document.getElementById('searchInboxTickets');
                        if (searchInput) {
                            searchInput.addEventListener('input', applyCombinedFilters);
                        }
                    });
                    </script>
                    <div class="ticket-list">
                        <div class="ticket-list-header">
                            <!-- Search Box -->
                            <div class="search-box" style="margin-bottom: 15px; max-width: 220px;">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="searchInboxTickets" placeholder="Search tickets...">
                            </div>

                            <div class="split-tabs">
                            <button type="button" class="split-tab active" id="btn-pending" onclick="switchTicketList('pending')">
                                <i class="fa-regular fa-clock"></i>
                                <span>Pending</span>
                                @php
                                    $pendingCount = $inbox->filter(function($t){
                                        if ($t->status === 'Resolved') return false;
                                        // Determine current deadline stage
                                        $deadline = is_null($t->responded_at) ? $t->response_deadline : $t->resolution_deadline;
                                        if ($deadline) {
                                            $dl = \Carbon\Carbon::parse($deadline);
                                            return !$dl->isPast();
                                        }
                                        // If no deadline, treat as pending
                                        return true;
                                    })->count();
                                @endphp
                                <span class="tab-count">{{ $pendingCount }}</span>
                            </button>
                            <button type="button" class="split-tab" id="btn-overdue" onclick="switchTicketList('overdue')">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Overdue</span>
                                @php
                                    $overdueCount = $inbox->filter(function($t){
                                        if ($t->status === 'Resolved') return false;
                                        $deadline = is_null($t->responded_at) ? $t->response_deadline : $t->resolution_deadline;
                                        if ($deadline) {
                                            $dl = \Carbon\Carbon::parse($deadline);
                                            return $dl->isPast();
                                        }
                                        return false;
                                    })->count();
                                @endphp
                                <span class="tab-count">{{ $overdueCount }}</span>
                            </button>
                            <button type="button" class="split-tab" id="btn-resolved" onclick="switchTicketList('resolved')">
                                <i class="fa-solid fa-check"></i>
                                <span>Resolved</span>
                                <span class="tab-count">{{ $inbox->where('status','Resolved')->count() }}</span>
                            </button>
                            </div>
                        </div>

                        <div class="ticket-list-content">
                        <h3 class="split-heading" id="heading-pending">Pending Tickets</h3>
                        <div id="list-pending">
                        @forelse($inbox->filter(function($t){
                            if ($t->status === 'Resolved') return false;
                            $deadline = is_null($t->responded_at) ? $t->response_deadline : $t->resolution_deadline;
                            if ($deadline) {
                                $dl = \Carbon\Carbon::parse($deadline);
                                return !$dl->isPast();
                            }
                            return true; // no deadline => pending
                        }) as $ticket)
                            <div class="ticket-item" id="ticket-{{ $ticket->ticket_no }}" onclick="openTicket('{{ $ticket->ticket_no }}')" data-created="{{ $ticket->created_at }}" data-priority="{{ $ticket->priority }}" data-status="{{ $ticket->status }}">
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
                            <p>No pending tickets.</p>
                        @endforelse
                        </div>

                        <h3 class="split-heading" style="display:none;" id="heading-overdue">Overdue Tickets</h3>
                        <div id="list-overdue" style="display:none;">
                        @forelse($inbox->filter(function($t){
                            if ($t->status === 'Resolved') return false;
                            $deadline = is_null($t->responded_at) ? $t->response_deadline : $t->resolution_deadline;
                            if ($deadline) {
                                $dl = \Carbon\Carbon::parse($deadline);
                                return $dl->isPast();
                            }
                            return false;
                        }) as $ticket)
                            <div class="ticket-item" id="ticket-{{ $ticket->ticket_no }}" onclick="openTicket('{{ $ticket->ticket_no }}')" data-created="{{ $ticket->created_at }}" data-priority="{{ $ticket->priority }}" data-status="{{ $ticket->status }}">
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
                                            if ($t = $ticket) {
                                                if ($t->status !== 'Resolved') {
                                                    if (is_null($t->responded_at)) {
                                                        $deadline = $t->response_deadline;
                                                        $action = 'Respond';
                                                    } else {
                                                        $deadline = $t->resolution_deadline;
                                                        $action = 'Resolve';
                                                    }
                                                    if ($deadline) {
                                                        $dl = \Carbon\Carbon::parse($deadline);
                                                        $deadlineOverdue = $dl->isPast();
                                                        $diff = $dl->diffForHumans(null, ['parts' => 2, 'short' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
                                                        $deadlineLabel = $deadlineOverdue ? ($action . ' overdue by ' . $diff) : ($action . ' in ' . $diff);
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if(!is_null($deadlineLabel))
                                            <span class="badge deadline overdue">{{ $deadlineLabel }}</span>
                                        @endif
                                    </div>
                                    @if($ticket->status === 'Replied')
                                        <span class="badge replied">Replied</span>
                                    @endif
                                    <button class="resolve-btn" onclick="event.stopPropagation(); resolveTicket('{{ $ticket->ticket_no }}')">
                                        ✅ Resolve
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p>No overdue tickets.</p>
                        @endforelse
                        </div>

                        <h3 class="split-heading" style="display:none;" id="heading-resolved">Resolved Tickets</h3>
                        <div id="list-resolved" style="display:none;">
                        @forelse($inbox->filter(function($t){ return $t->status === 'Resolved'; }) as $ticket)
                            <div class="ticket-item" id="ticket-{{ $ticket->ticket_no }}" onclick="openTicket('{{ $ticket->ticket_no }}')" data-created="{{ $ticket->created_at }}" data-priority="{{ $ticket->priority }}" data-status="{{ $ticket->status }}">
                                <strong>🎫 {{ $ticket->ticket_no }}</strong>
                                <div class="ticket-meta">
                                    <div>{{ Str::limit($ticket->message, 50) }}</div>
                                    <div>
                                        <span class="badge {{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                                        <span class="badge">{{ $ticket->category }}</span>
                                        <span class="badge resolved">Resolved</span>
                                        @if($ticket->is_expired)
                                            <span class="badge expired">Expired</span>
                                        @endif
                                    </div>
                                    <span class="resolved-badge">✅ Resolved</span>
                                    @if($ticket->resolved_by)
                                        <div class="resolved-by-info" style="margin-top: 4px; color: #155724; font-size: 12px;">
                                            <strong>Resolved by Employee ID:</strong> {{ $ticket->resolved_by }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p>No resolved tickets.</p>
                        @endforelse
                        </div>
                        </div>
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

            <div class="form-group">
                <label>Expiry Date <span class="required">*</span></label>
                <input type="date" name="expiry_date" id="announcementExpiryDate" required 
                       placeholder="Select expiry date" min="{{ date('Y-m-d') }}">
                <small style="color: #666; display: block; margin-top: 5px;">
                    Announcement will be automatically deleted after this date.
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

            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middleName" value="{{ $user->middleName ?? '' }}">
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

            <div class="form-group">
                <label>Expiry Date</label>
                <input type="date" name="expiry_date" id="editAnnouncementExpiryDate" min="{{ date('Y-m-d') }}">
                <small style="color: #666;">Set or clear the expiry date</small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="submit-button" style="flex: 1;">Update</button>
                <button type="button" class="cancel-button" onclick="closeEditAnnouncementModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Category Assignment Modal for Ticket Resolution -->
<div id="categoryModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">Assign Category Before Resolving</h3>
        </div>
        <form id="categoryForm" class="modal-form">
            <input type="hidden" id="categoryTicketNo">
            
            <div class="form-group">
                <label>Select Category <span style="color: red;">*</span></label>
                <select name="category" id="categorySelect" required style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;" onchange="toggleCustomCategory()">
                    <option value="">-- Select a category --</option>
                    <option value="Conditions on employment">Conditions on employment</option>
                    <option value="Compensation and benefits">Compensation and benefits</option>
                    <option value="Employee Development">Employee Development</option>
                    <option value="Ranking and Promotion">Ranking and Promotion</option>
                    <option value="General">General</option>
                    <option value="CUSTOM">Custom Category</option>
                </select>
            </div>

            <div class="form-group" id="customCategoryGroup" style="display: none;">
                <label>Enter Custom Category <span style="color: red;">*</span></label>
                <input type="text" id="customCategoryInput" placeholder="Type custom category here..." style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="submit-button" style="flex: 1; padding: 12px; background: #1A6B61; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Resolve Ticket
                </button>
                <button type="button" class="cancel-button" onclick="closeCategoryModal()" style="flex: 1; padding: 12px; background: #ccc; color: #333; border: none; border-radius: 6px; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentTicket = null;

    function toggleCustomCategory() {
        const categorySelect = document.getElementById('categorySelect');
        const customCategoryGroup = document.getElementById('customCategoryGroup');
        const customCategoryInput = document.getElementById('customCategoryInput');
        
        if (categorySelect.value === 'CUSTOM') {
            customCategoryGroup.style.display = 'block';
            customCategoryInput.required = true;
        } else {
            customCategoryGroup.style.display = 'none';
            customCategoryInput.required = false;
            customCategoryInput.value = '';
        }
    }

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
        
        // Update top-nav active state
        document.querySelectorAll('.top-nav-link').forEach(link => link.classList.remove('active'));
        const activeTopLink = document.getElementById('top-link-' + id);
        if (activeTopLink) activeTopLink.classList.add('active');
    }

    async function resolveTicket(ticketNo) {
        // Show category modal instead of immediate confirmation
        document.getElementById('categoryTicketNo').value = ticketNo;
        document.getElementById('categoryModal').style.display = 'flex';
        document.getElementById('categorySelect').value = ''; // Reset selection
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
        document.getElementById('categorySelect').value = '';
        document.getElementById('customCategoryInput').value = '';
        document.getElementById('customCategoryGroup').style.display = 'none';
        document.getElementById('customCategoryInput').required = false;
    }

    // Handle category form submission
    document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const ticketNo = document.getElementById('categoryTicketNo').value;
        const categorySelect = document.getElementById('categorySelect').value;
        const customCategoryInput = document.getElementById('customCategoryInput').value;
        
        let category;
        if (categorySelect === 'CUSTOM') {
            if (!customCategoryInput.trim()) {
                alert('Please enter a custom category');
                return;
            }
            // Uppercase the custom category before sending
            category = customCategoryInput.trim().toUpperCase();
        } else {
            category = categorySelect;
        }
        
        if (!category) {
            alert('Please select a category');
            return;
        }
        
        try {
            const res = await fetch('/hr/resolve-ticket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    ticket_no: ticketNo,
                    category: category
                })
            });
            const result = await res.json();
            console.log('Resolve response:', result);
            if (result.success) {
                closeCategoryModal();
                alert('Ticket resolved successfully!');
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Resolve error:', error);
            alert('Failed to resolve ticket');
        }
    });

    // Update tab badge counts based on visible filtered tickets
    function updateTabCounts() {
        const pendingTickets = Array.from(document.querySelectorAll('#list-pending .ticket-item')).filter(t => t.style.display !== 'none');
        const overdueTickets = Array.from(document.querySelectorAll('#list-overdue .ticket-item')).filter(t => t.style.display !== 'none');
        const resolvedTickets = Array.from(document.querySelectorAll('#list-resolved .ticket-item')).filter(t => t.style.display !== 'none');
        
        const btnPendingCount = document.querySelector('#btn-pending .tab-count');
        const btnOverdueCount = document.querySelector('#btn-overdue .tab-count');
        const btnResolvedCount = document.querySelector('#btn-resolved .tab-count');
        
        if (btnPendingCount) btnPendingCount.textContent = pendingTickets.length;
        if (btnOverdueCount) btnOverdueCount.textContent = overdueTickets.length;
        if (btnResolvedCount) btnResolvedCount.textContent = resolvedTickets.length;
    }

    // Switch between pending, overdue, and resolved ticket lists
    function switchTicketList(which){
        const pendingBtn = document.getElementById('btn-pending');
        const overdueBtn = document.getElementById('btn-overdue');
        const resolvedBtn = document.getElementById('btn-resolved');
        const headingPending = document.getElementById('heading-pending');
        const headingOverdue = document.getElementById('heading-overdue');
        const headingResolved = document.getElementById('heading-resolved');
        const listPending = document.getElementById('list-pending');
        const listOverdue = document.getElementById('list-overdue');
        const listResolved = document.getElementById('list-resolved');
        if(!pendingBtn || !overdueBtn || !resolvedBtn || !headingPending || !headingOverdue || !headingResolved || !listPending || !listOverdue || !listResolved) return;
        // Reset active
        pendingBtn.classList.remove('active');
        overdueBtn.classList.remove('active');
        resolvedBtn.classList.remove('active');
        // Hide all
        headingPending.style.display = 'none';
        listPending.style.display = 'none';
        headingOverdue.style.display = 'none';
        listOverdue.style.display = 'none';
        headingResolved.style.display = 'none';
        listResolved.style.display = 'none';
        // Show selected
        if(which === 'pending'){
            pendingBtn.classList.add('active');
            headingPending.style.display = '';
            listPending.style.display = '';
        } else if(which === 'overdue'){
            overdueBtn.classList.add('active');
            headingOverdue.style.display = '';
            listOverdue.style.display = '';
        } else {
            resolvedBtn.classList.add('active');
            headingResolved.style.display = '';
            listResolved.style.display = '';
        }
    }

    async function openTicket(ticketNo) {
        currentTicket = ticketNo;
        document.querySelectorAll('.ticket-item').forEach(item => item.classList.remove('active'));
        document.getElementById(`ticket-${ticketNo}`).classList.add('active');
        
        // Get ticket status to check if resolved
        const ticketElement = document.getElementById(`ticket-${ticketNo}`);
        const ticketStatus = ticketElement ? ticketElement.getAttribute('data-status') : '';
        const isResolved = ticketStatus === 'Resolved';
        
        // Show/hide reply form based on ticket status
        const replyForm = document.getElementById('replyForm');
        if (isResolved) {
            replyForm.style.display = 'none';
            // Show a message that ticket is resolved
            const chatContainer = replyForm.parentElement;
            let resolvedNote = document.getElementById('resolvedNote');
            if (!resolvedNote) {
                resolvedNote = document.createElement('div');
                resolvedNote.id = 'resolvedNote';
                resolvedNote.style.cssText = 'text-align: center; padding: 15px; background: #e2e3e5; border-radius: 8px; color: #383d41; margin-top: 10px;';
                resolvedNote.innerHTML = '✅ <strong>This ticket has been resolved.</strong> No further replies can be sent.';
                chatContainer.appendChild(resolvedNote);
            } else {
                resolvedNote.style.display = 'block';
            }
        } else {
            replyForm.style.display = 'flex';
            const resolvedNote = document.getElementById('resolvedNote');
            if (resolvedNote) resolvedNote.style.display = 'none';
        }
        
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
                        ${msg.sender_name || (msg.sender === 'employee' ? 'Employee' : 'HR')} • 
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
    
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + (data.message || 'Announcement created successfully!'));
            closeAddAnnouncementModal();
            form.reset();
            // Reload to refresh announcements
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('❌ ' + (data.message || 'Failed to create announcement'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Failed to create announcement');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
    
});
    
    function editAnnouncement(id) {
        // Fetch announcement data
        fetch(`/hr/announcements/${id}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(async (response) => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    throw new Error('Invalid content-type');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const announcement = data.announcement;
                    
                    // Populate the edit form
                    document.getElementById('editAnnouncementId').value = announcement.id;
                    document.getElementById('editAnnouncementTitle').value = announcement.title;
                    document.getElementById('editAnnouncementDescription').value = announcement.description;
                    // Populate expiry date if exists
                    if (announcement.expiry_date) {
                        document.getElementById('editAnnouncementExpiryDate').value = announcement.expiry_date;
                    } else {
                        document.getElementById('editAnnouncementExpiryDate').value = '';
                    }
                    
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

    // ===== NAVIGATION FIX =====
    // Ensure sidebar link clicks switch sections reliably
    (function() {
        const bindNav = (selectorPrefix) => {
            document.querySelectorAll(selectorPrefix + ' a').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const href = link.getAttribute('href') || '';
                    const hash = href.startsWith('#') ? href.substring(1) : null;
                    const target = link.dataset.target || hash;
                    if (target) {
                        showSection(target);
                    }
                });
            });
        };
        // Bind sidebar nav
        const sidebarNav = document.querySelector('.sb-nav');
        if (sidebarNav) bindNav('.sb-nav');
    })();

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

    // Date range filter for inbox
    function applyInboxDateFilter() {
        const rangeSelect = document.getElementById('inboxDateRangeSelect');
        const rangeDisplay = document.getElementById('inboxDateRangeDisplay');
        const selectedRange = rangeSelect.value;
        
        const today = new Date();
        let startDate, endDate, displayText;
        
        switch(selectedRange) {
            case 'daily':
                startDate = new Date(today.setHours(0, 0, 0, 0));
                endDate = new Date(today.setHours(23, 59, 59, 999));
                displayText = 'Today';
                break;
            case 'weekly':
                const firstDayOfWeek = today.getDate() - today.getDay();
                startDate = new Date(today.setDate(firstDayOfWeek));
                startDate.setHours(0, 0, 0, 0);
                endDate = new Date();
                displayText = 'This Week';
                break;
            case 'monthly':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date();
                displayText = 'This Month';
                break;
            case 'annually':
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date();
                displayText = 'This Year';
                break;
            case 'overall':
            default:
                startDate = null;
                endDate = null;
                displayText = 'All Time';
        }
        
        rangeDisplay.textContent = displayText;
        
        // Filter inbox ticket cards by date
        const tickets = document.querySelectorAll('#inbox .ticket-item');
        let visibleCount = 0;
        
        tickets.forEach(ticket => {
            const createdDate = ticket.getAttribute('data-created');
            if (!createdDate) {
                ticket.style.display = 'none';
                return;
            }
            
            const ticketDate = new Date(createdDate);
            
            if (!startDate || !endDate || (ticketDate >= startDate && ticketDate <= endDate)) {
                ticket.style.display = 'block';
                visibleCount++;
            } else {
                ticket.style.display = 'none';
            }
        });
        
        // Update dashboard cards based on visible tickets
        updateInboxCards();
        
        // Update tab counts
        updateTabCounts();
    }
    
    function updateInboxCards() {
        // Get all visible tickets from all tabs (pending, overdue, resolved)
        const tickets = Array.from(document.querySelectorAll('#inbox .ticket-item'))
            .filter(ticket => ticket.style.display !== 'none');
        
        const cards = document.querySelectorAll('#inbox .dashboard-cards .card');
        if (cards.length >= 6) {
            // Total tickets
            cards[0].querySelector('h3').textContent = tickets.length;
            
            // Count by priority (only from visible tickets)
            const urgent = tickets.filter(ticket => ticket.getAttribute('data-priority') === 'urgent').length;
            const high = tickets.filter(ticket => ticket.getAttribute('data-priority') === 'high').length;
            const medium = tickets.filter(ticket => ticket.getAttribute('data-priority') === 'medium').length;
            const low = tickets.filter(ticket => ticket.getAttribute('data-priority') === 'low').length;
            
            cards[1].querySelector('h3').textContent = urgent;
            cards[2].querySelector('h3').textContent = high;
            cards[3].querySelector('h3').textContent = medium;
            cards[4].querySelector('h3').textContent = low;
            
            // Count replied (only from visible tickets)
            const replied = tickets.filter(ticket => ticket.getAttribute('data-status') === 'Replied').length;
            cards[5].querySelector('h3').textContent = replied;
        }
    }
</script>

@include('includes.footer')
</body>
</html>